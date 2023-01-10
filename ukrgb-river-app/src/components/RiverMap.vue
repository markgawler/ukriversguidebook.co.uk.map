<script setup>
import "leaflet/dist/leaflet.css";
import "proj4leaflet";
import { onBeforeUnmount, onMounted } from "vue";
import { ref, reactive, watch } from "vue";
import L from "leaflet";
import "../utils/WithHeaders";
import axios from "axios";
import MapCursor from "./MapCursor.vue";
import redIconMarker from "../assets/marker-icon-red.png";
import shadowIconMarker from "../assets/marker-shadow.png";
import { accessToken } from "./AccessToken.vue";

const lat = ref(0);
const lng = ref(0);
let map = {}; // the map
let road = {}; // road layer
let leisure = {}; // leisure layer
const points = reactive({ values: [] }); // the markers belonging to this map
let resizeObserver = null;
const mapContainer = ref(null); // Reference to mapContainer <div> used for watching for map resize

const props = defineProps({
  accessToken: { type: String, default: "" },
  callbackURL: { type: String, default: "" },
  initialBounds: { type: Array, default: null },
  mapId: { type: Number, default: 0 },
  premium: Boolean,
});

watch(
  () => props.accessToken,
  (token) => {
    addLayers(token);
  }
);

watch(points, () => {
  if (points.values != null && points.values.length > 0) {
    addPoints();
  }
});

onBeforeUnmount(() => {
  resizeObserver.unobserve(mapContainer.value);
});

onMounted(() => {
  createMap();
  loadMapPointData();
  if (accessToken.value !== "") {
    addLayers(accessToken.value);
  }
});

const addLayers = (token) => {
  // On receipt of the first Access Token create the Map, otherwise update
  // the Autherisation header with the new Access Token.
  const header = [{ header: "Authorization", value: "Bearer " + token }];
  if (road.headers == undefined) {
    // now we have an access token we can add the laters to the map

    // Instantiate a tile layer object for the Road style (displayed at zoom levels 10-13).
    road = getLayer("Road", props.premium);
    road.headers = header;
    road.addTo(map);

    // Instantiate a tile layer object for the Leisure style (displayed at zoom levels 0-9).
    leisure = getLayer("Leisure", props.premium);
    leisure.headers = header;
    leisure.addTo(map);
  } else {
    // An access token has perviously been set in the headder i.e. the Bearer includes a token
    // Update the Access Token in the Autherisation headder of all the layers (with headders)
    map.eachLayer(function (layer) {
      if ("headers" in layer) {
        layer.headers = header;
      }
    });
  }
};

const createMap = () => {
  // Setup the EPSG:27700 (British National Grid) projection.
  var crs = new L.Proj.CRS(
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
      var container = L.DomUtil.create("div", "os-branding-logo");
      return container;
    },
  });
  map = L.map("map", mapOptions);
  map.addControl(new LogoControl());
  map.fitBounds(props.initialBounds);

  // Event handlet for mouse movement to update cursor
  map.on("mousemove", (e) => {
    lng.value = e.latlng.lng;
    lat.value = e.latlng.lat;
  });

  resizeObserver = new ResizeObserver(() => {
    map.invalidateSize();
  });
  resizeObserver.observe(mapContainer.value);
};

function loadMapPointData() {
  // Make a request for the map points for a given map
  axios
    .get(props.callbackURL, {
      params: {
        task: "mappoint",
        guideid: props.mapId,
      },
    })
    .then((response) => {
      // success
      points.values = response.data;
    })
    .catch((error) => {
      // error
      console.log(error);
    });
}

function addPoints() {
  const s = 8 / 10;
  const redIcon = new L.Icon({
    iconUrl: redIconMarker,
    shadowUrl: shadowIconMarker,
    iconSize: [25 * s, 41 * s],
    iconAnchor: [12 * s, 41 * s],
    popupAnchor: [1, -34 * s],
    shadowSize: [41 * s, 41 * s],
  });
  for (const p of points.values) {
    L.marker([p.Y, p.X], { icon: redIcon }).addTo(map).bindPopup(p.description);
  }
}

// Control which zoom levels are avalable to authenticated and unauthenitcated users, some
// premium zoom levels are avalable to unauthenticated users (at present)
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

// Get OS Map Layer with autherisation headers
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
    [{ header: "Authorization", value: "Bearer " + props.accessToken }]
  );
}
</script>

<template>
  <div class="resize">
    <div id="map" ref="mapContainer" class="mapcontainer"></div>
  </div>
  <MapCursor :poss="{ lat, lng }" />
</template>

<style>
.mapcontainer {
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
