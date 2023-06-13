<script setup>
import "leaflet/dist/leaflet.css";
import "proj4leaflet";
import { onBeforeUnmount, onMounted, ref, watch, computed } from "vue";
import { useStore } from "vuex";
import { getPointsByRadius } from "../network/mapData";
import L from "leaflet";
import "../utils/WithHeaders";
import MapCursor from "./MapCursor.vue";
import redIconMarker from "../assets/marker-icon-red.png";
import blueIconMarker from "../assets/marker-icon-blue.png";
import shadowIconMarker from "../assets/marker-shadow.png";

const store = useStore();
const lat = ref(0);
const lng = ref(0);
let map = {}; // the map
let road = {}; // road layer
let leisure = {}; // leisure layer
let localMarkerLayer = {}; // layer for current maps markers
let otherMarkerLayer = {}; // layer for other maps markers
let resizeObserver = null; // observer for map <div> resize, used handle map resize
const mapContainer = ref(null); // Reference to mapContainer <div> used for watching for map resize
const markers = []; // the markers

const props = defineProps({
  initialBounds: { type: Array, default: null },
  mapId: { type: Number, default: 0 },
  canEdit: { type: Boolean, default: false },
  editing: { type: Boolean, default: false }
});

const accessToken = computed(() => store.state.mapAccess.accessToken);
const premium = computed(() => store.state.mapAccess.userId > 0); // Authenticated user if userId > 0

watch(
  () => store.state.mapAccess.accessToken,
  (token) => {
    addMapLayers(token);
  }
);
watch (
  // Enable / Disable draging of markers when switching in and out of edditing
  () => props.editing, (x) => {
    // Get the local Markers
    markers.filter((m) => m.local == true).forEach(m => {
      if (x) {
        m.marker.dragging.enable();
      } else {
        m.marker.dragging.disable();
      }
    });
  } 
);

/*
  Keep the Map in sync with the Store, subscribe to mutations of the mapPoints store: 
    Add Markers:-
      addPoint
      unDeletePoint
    Delete Markers:-
      deletePoint
      deleteNewPoints
    Update 
      updatePoint
    Reload data
      mapParameters/loadMap
      reloadPoints
 */
const unsubscribe = store.subscribe((mutation) => {
  switch (mutation.type) {
    case "mapPoints/deletePoint":
      {
        const id = mutation.payload; // Payload of mutation is the id of the point to delete
        const index = markers.findIndex((x) => x.id === id);
        localMarkerLayer.removeLayer(markers[index].marker);
        markers.splice(index, 1);
      }
      break;
    case "mapPoints/addPoint":
    case "mapPoints/unDeletePoint":
      {
        const pt = mutation.payload;
        // Add / restore the point to the Map
        addMapMarker(pt, parseInt(pt.mapid) === props.mapId);
      }
      break;
    case "mapPoints/updatePoint":
      {
        // Payload is the id and the description of the updated marker
        const pl = mutation.payload;
        const marker = markers.find((x) => x.id === pl.id).marker; // find the leaflet marker

        // Guard against null values, this hapens when the point description is updated in the
        // mappoints the marker position will not be populated, likewise when a marker is dregedd 
        // the description may not be populated. 
        if (pl.description != null) {
          marker.getPopup().setContent(pl.description);
        }
        if (pl.X != null) {
          marker.setLatLng([pl.Y, pl.X])
        }
      }
      break;
    case "mapPoints/deleteNewPoints":
      {
        // Find all the new markers, This exploit knowlage of the store in that there index will be less 
        // than 0, this does place a dependency on the stor implementation :-( FIXME
        const pts = markers.filter((pt) => pt.id < 0)
        for (const pt of pts) {
          const index = markers.findIndex((mk) => mk.id === pt.id);
          localMarkerLayer.removeLayer(markers[index].marker);
          markers.splice(index, 1);
        }
      }
      break;
    case "mapParameters/loadMap":
    case "mapPoints/reloadPoints":
      {
        // Load any map point that would be visible on the map. Some points outside 
        // the map bounds will be loaded as the area is calculated as a circle encompassing the whole map. 
        const p = store.getters["mapParameters/getCircleParams"]
        getPointsByRadius(p.center, p.radius);
      }
      break;
  }
});

onBeforeUnmount(() => {
  unsubscribe(); // unsubscribe for store mutations
  resizeObserver.unobserve(mapContainer.value);
});

onMounted(() => {
  createMap();
  addMarkerPointLayers();
  if (accessToken.value !== "") {
    addMapLayers(accessToken.value);
  }
});

const scale = 8 / 10; // scale the marker 80%
const redMarker = new L.Icon({
  iconUrl: redIconMarker,
  shadowUrl: shadowIconMarker,
  iconSize: [25 * scale, 41 * scale],
  iconAnchor: [12 * scale, 41 * scale],
  popupAnchor: [1, -34 * scale],
  shadowSize: [41 * scale, 41 * scale],
});
const blueMarker = new L.Icon({
  iconUrl: blueIconMarker,
  shadowUrl: shadowIconMarker,
  iconSize: [25 * scale, 41 * scale],
  iconAnchor: [12 * scale, 41 * scale],
  popupAnchor: [1, -34 * scale],
  shadowSize: [41 * scale, 41 * scale],
});

const addMapLayers = (token) => {
  // On receipt of the first Access Token create the Map, otherwise update
  // the Authorisation header with the new Access Token.
  const header = [{ header: "Authorization", value: "Bearer " + token }];
  if (road.headers === undefined) {
    // now we have an access token we can add the layers to the map

    // Instantiate a tile layer object for the Road style (displayed at zoom levels 10-13).
    road = getLayer("Road", premium.value);
    road.headers = header;
    road.addTo(map);

    // Instantiate a tile layer object for the Leisure style (displayed at zoom levels 0-9).
    leisure = getLayer("Leisure", premium.value);
    leisure.headers = header;
    leisure.addTo(map);
  } else {
    // An access token has previously been set in the header i.e. the Bearer includes a token
    // Update the Access Token in the Authorisation header of all the layers (with headers)
    map.eachLayer((layer) => {
      if ("headers" in layer) {
        layer.headers = header;
      }
    });
  }
};

const addMarkerPointLayers = () => {
  otherMarkerLayer = L.layerGroup([]).addTo(map);
  localMarkerLayer = L.layerGroup([]).addTo(map);

  const overlayMaps = {
    "Map Marker": localMarkerLayer,
    "Other Maps Markers": otherMarkerLayer,
  };
  // Add the layer control, the null parameter would be used if we had selectable base maps
  L.control.layers(null, overlayMaps).addTo(map);
  mapMovedOrZoomed();
};

const createMap = () => {
  // Set up the EPSG:27700 (British National Grid) projection.
  const crs = new L.Proj.CRS(
    "EPSG:27700",
    "+proj=tmerc +lat_0=49 +lon_0=-2 +k=0.9996012717 +x_0=400000 +y_0=-100000 +ellps=airy +towgs84=446.448,-125.157,542.06,0.15,0.247,0.842,-20.489 +units=m +no_defs",
    {
      resolutions: [
        896.0, 448.0, 224.0, 112.0, 56.0, 28.0, 14.0, 7.0, 3.5, 1.75, 0.875,
        0.4375, 0.21875, 0.109375,
      ],
      origin: [-238375.0, 1376256.0],
    }
  );

  // Initialize the map.
  const mapOptions = {
    crs: crs,
    minZoom: 0,
    maxBounds: [
      [49.562026923812304, -10.83428466254654],
      [61.93445135313357, 7.548212515441139],
    ],
    attributionControl: true,
  };
  const LogoControl = L.Control.extend({
    options: {
      position: "bottomleft",
    },
    onAdd: () => {
      return L.DomUtil.create("div", "os-branding-logo");
    },
  });
  map = L.map("map", mapOptions);
  map.addControl(new LogoControl());
  map.fitBounds(props.initialBounds);

  // Event handler for mouse movement to update cursor
  map.on("mousemove", (e) => {
    lng.value = e.latlng.lng;
    lat.value = e.latlng.lat;
  });

  resizeObserver = new ResizeObserver(() => {
    map.invalidateSize();
  });
  resizeObserver.observe(mapContainer.value);

  map.on("moveend", () => {
    mapMovedOrZoomed();
  });
};

/*  When the map is moved/panned or zoomed Update the store with the new center point and bounds
    this is used to refetch the map points in the map area */
function mapMovedOrZoomed() {
  // Calculate the radius (in km) of the circle that will cover the map
  const center = map.getCenter();
  const bounds = map.getBounds();
  const radius =
    center.distanceTo(L.latLng(bounds.getNorth(), bounds.getEast())) / 1000;

  store.dispatch("mapParameters/storeParameters",
    {
      bounds: bounds,
      center: center,
      radius: radius
    }
  )
}

function addMapMarker(point, local = true) {
  let markerIcon = {};
  let popupContent = "";
  let layerGroup = {};
  if (local) {
    markerIcon = redMarker;
    layerGroup = localMarkerLayer;
    popupContent = point.description;
  } else {
    markerIcon = blueMarker;
    layerGroup = otherMarkerLayer;
    popupContent =
      '<a href="/index.php?option=com_content&id=' +
      point.riverguide +
      '&view=article">' +
      point.description +
      "</a><br>";
  }
  const marker = new L.marker([point.Y, point.X], { icon: markerIcon, draggable: local && props.editing });

  // If the marker is local add function to update the store with the new location of the marker when
  // the draging stops.
  if (local && props.canEdit) {
    marker.on("moveend", () => {
      const latlng = marker.getLatLng();
      store.dispatch("mapPoints/updatePoint", {
        id: point.id,
        X: latlng.lng,
        Y: latlng.lat,
      });
    })
  }
  // Add the marker to the layer 
  marker.addTo(layerGroup).bindPopup(popupContent);
  markers.push({ id: point.id, marker: marker, local: local }); // track the id to marker references 
}

// Control which zoom levels are available to authenticated and unauthenticated users, some
// premium zoom levels are available to unauthenticated users (at present)
function getMapConfig(premium) {
  if (premium) {
    return {
      maxZoom: 13,
      maxZoomLeisure: 9,
      minZoomRoad: 10,
    };
  } else {
    return {
      maxZoom: 10,
      maxZoomLeisure: 9,
      minZoomRoad: 10,
    };
  }
}

// Get OS Map Layer with authorisation headers
function getLayer(layerType, premium) {
  const config = getMapConfig(premium);
  let minZoom = 0;
  let maxZoom = config.maxZoom;

  switch (layerType) {
    case "Road":
      minZoom = config.minZoomRoad;
      break;
    case "Leisure":
      maxZoom = config.maxZoomLeisure;
      break;
    default:
      console.log("Invalid layer type: ", layerType);
      return null;
  }
  const year = new Date().getFullYear();
  const serviceUrl = "https://api.os.uk/maps/raster/v1/zxy";
  const attribution =
    "Contains OS data &copy; Crown copyright and database rights " + year;
  return L.TileLayerH(
    serviceUrl + "/" + layerType + "_27700/{z}/{x}/{y}.png",
    {
      minZoom: minZoom,
      maxZoom: maxZoom,
      attribution: attribution,
    },
    [{ header: "Authorization", value: "Bearer " + accessToken.value }]
  );
}
</script>

<template>
  <div class="resize">
    <div id="map" ref="mapContainer" class="map-container"></div>
  </div>
  <MapCursor :poss="{ lat, lng }" />
</template>

<style>
.map-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.os-branding-logo {
  background-image: url("~@/assets/os-logo-maps.png");
  width: 90px;
  height: 24px;
  background-position: center;
}

.resize {
  overflow: hidden;
  resize: both;
  height: 400px;
  max-height: 800px;
  max-width: 1200px;
}
</style>
