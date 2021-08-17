<template>
  <div class='mapcontainer' id='map'></div>
  <MapCursor v-bind:poss='{ lat, lng }' />

</template>

<script>
import 'leaflet/dist/leaflet.css'
import MapCursor from './MapCursor'
import L from 'leaflet'
import 'proj4leaflet'

// Fix for marker not appearing
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
})

export default {
  name: 'Map',
  components: {
    MapCursor
  },
  props: {
    authenticated: Boolean,
    center: Object
  },
  data: () => ({
    // mouse cursor
    lat: 0,
    lng: 0
  }),
  methods: {
    createMap () {
      const apiKey = 'P1UMHqoffDhreNwEh2xsZKnS02fRf5d8'
      const serviceUrl = 'https://api.os.uk/maps/raster/v1/zxy'
      const year = new Date().getFullYear()

      let maxZoom = 9
      let maxZoomLeisure = 5
      let minZoomRoad = 6
      let minZoomOutdoor = minZoomRoad
      if (this.authenticated) {
        maxZoom = 13
        maxZoomLeisure = 9
        minZoomRoad = 10
        minZoomOutdoor = minZoomRoad
      }

      // Setup the EPSG:27700 (British National Grid) projection.
      var crs = new L.Proj.CRS(
        'EPSG:27700',
        '+proj=tmerc +lat_0=49 +lon_0=-2 +k=0.9996012717 +x_0=400000 +y_0=-100000 +ellps=airy +towgs84=446.448,-125.157,542.06,0.15,0.247,0.842,-20.489 +units=m +no_defs',
        {
          resolutions: [
            896.0, 448.0, 224.0, 112.0, 56.0, 28.0, 14.0, 7.0, 3.5, 1.75, 0.875,
            0.4375, 0.21875, 0.109375
          ],
          origin: [-238375.0, 1376256.0]
        }
      )
      const attribution =
        'Contains OS data &copy; Crown copyright and database rights ' + year

      // Instantiate a tile layer object for the Leisure style (displayed at zoom levels 0-9).
      var leisure = L.tileLayer(
        serviceUrl + '/Leisure_27700/{z}/{x}/{y}.png?key=' + apiKey,
        {
          maxZoom: maxZoomLeisure,
          attribution: attribution
        }
      )

      var outdoor = L.tileLayer(
        serviceUrl + '/Outdoor_27700/{z}/{x}/{y}.png?key=' + apiKey,
        {
          minZoom: minZoomOutdoor,
          attribution: attribution
        }
      )

      // Instantiate a tile layer object for the Road style (displayed at zoom levels 10-13).
      var road = L.tileLayer(
        serviceUrl + '/Road_27700/{z}/{x}/{y}.png?key=' + apiKey,
        {
          minZoom: minZoomRoad,
          attribution: attribution
        }
      )

      // Initialize the map.
      var mapOptions = {
        crs: crs,
        layers: [road, outdoor, leisure],
        minZoom: 0,
        maxZoom: maxZoom,
        center: [this.center.n, this.center.e],
        zoom: 0,
        maxBounds: [
          [49.562026923812304, -10.83428466254654],
          [61.93445135313357, 7.548212515441139]
        ],
        attributionControl: true
      }

      // Add layer control to the map.
      var baseMaps = {
        Outdoor: outdoor,
        Leisure: leisure,
        Road: road
      }

      var overlayMaps = {}

      const LogoControl = L.Control.extend({
        options: {
          position: 'bottomleft'
        },
        onAdd: () => {
          var container = L.DomUtil.create('div', 'os-branding-logo')
          return container
        }
      })

      var map = L.map('map', mapOptions)
      map.addControl(new LogoControl())
      L.marker([54.42, -2.98])
        .addTo(map)
        .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')

      map.on('mousemove', (e) => {
        this.lng = e.latlng.lng
        this.lat = e.latlng.lat
      })

      L.control.layers(baseMaps, overlayMaps).addTo(map)
    }
  },
  mounted () {
    this.$nextTick(() => {
      this.createMap()
    })
  }
}
</script>

<style>
.mapcontainer {
  position: relative;
  width: 100%;
  height: 400px;
}

.os-branding-logo {
  background-image: url('~@/assets/os-logo-maps.png');
  width: 90px;
  height: 24px;
  background-position: center;
}
</style>
