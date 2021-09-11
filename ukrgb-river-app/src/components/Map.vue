<template>
  <div class='mapcontainer' id='map'></div>
  <MapCursor v-bind:poss='{ lat, lng }' />
</template>

<script>
import 'leaflet/dist/leaflet.css'
import MapCursor from './MapCursor'
import L from 'leaflet'
import 'proj4leaflet'
import { toRaw } from 'vue'
import '../utils/GridRefUtils'
import { withHeaders } from '../utils/WithHeaders'

export default {
  name: 'Map',
  components: {
    MapCursor
  },
  props: {
    premium: Boolean,
    initialBounds: Array,
    callbackURL: String,
    accessToken: String,
    mapId: Number
  },
  data: () => ({
    // mouse cursor
    lat: 0,
    lng: 0,
    map: Object, // the map
    road: Object, // road layer
    leisure: Object, // leasure layer
    points: null, // the markers belonging to this map
    initialised: false // True when map created followin recipt of access token
  }),
  watch: {
    points () {
      if (this.initialised) {
        this.addPoints()
      }
    },
    premium () {
      if (this.initialised) {
        const c = this.getMapConfig(this.premium)
        this.leisure.options.maxZoom = c.maxZoomLeisure
        this.road.options.minZoom = c.minZoomRoad
        this.road.options.maxZoom = c.maxZoom
        toRaw(this.map).removeLayer(this.road)
        toRaw(this.map).removeLayer(this.leisure)
        toRaw(this.map).addLayer(this.road)
        toRaw(this.map).addLayer(this.leisure)
      }
    },
    accessToken () {
      // On receipt of the first Access Token create the Map, otherwise update
      // the Autherisation header with the new Access Token.
      if (this.initialised) {
        // Update the Access Token in the Autherisation headder of all the layers (with headders)
        const header = [
          { header: 'Authorization', value: 'Bearer ' + this.accessToken }
        ]
        toRaw(this.map).eachLayer(function (layer) {
          if ('headers' in layer) {
            layer.headers = header
          }
        })
      } else {
        // Create the Map on receipt of an Access Token.
        this.createMap()
        this.initialised = true
        if (this.points != null && this.points.length > 0) {
          this.addPoints()
        }
      }
    }
  },
  methods: {
    getMapConfig (premium) {
      if (premium) {
        return {
          maxZoom: 13,
          maxZoomLeisure: 9,
          minZoomRoad: 10
        }
      } else {
        return {
          maxZoom: 9,
          maxZoomLeisure: 5,
          minZoomRoad: 6
        }
      }
    },
    getLayer (layerType, premium) {
      const config = this.getMapConfig(premium)
      let minZoom = 0
      let maxZoom = config.maxZoom

      switch (layerType) {
        case 'Road':
          minZoom = config.minZoomRoad
          break
        case 'Leisure':
          maxZoom = config.maxZoomLeisure
          break
        default:
          console.log('Invalid layer type: ', layerType)
          return null
      }

      const year = new Date().getFullYear()
      const serviceUrl = 'https://api.os.uk/maps/raster/v1/zxy'
      const attribution = 'Contains OS data &copy; Crown copyright and database rights ' + year
      return withHeaders(
        serviceUrl + '/' + layerType + '_27700/{z}/{x}/{y}.png',
        {
          minZoom: minZoom,
          maxZoom: maxZoom,
          attribution: attribution
        },
        [
          { header: 'Authorization', value: 'Bearer ' + this.accessToken }
        ]
      )
    },
    createMap () {
      const premium = this.premium

      // Setup the EPSG:27700 (British National Grid) projection.
      var crs = new L.Proj.CRS(
        'EPSG:27700',
        '+proj=tmerc +lat_0=49 +lon_0=-2 +k=0.9996012717 +x_0=400000 +y_0=-100000 +ellps=airy +towgs84=446.448,-125.157,542.06,0.15,0.247,0.842,-20.489 +units=m +no_defs',
        {
          resolutions: [
            896.0, 448.0, 224.0, 112.0, 56.0, 28.0, 14.0, 7.0, 3.5, 1.75, 0.875, 0.4375, 0.21875, 0.109375
          ],
          origin: [-238375.0, 1376256.0]
        }
      )

      // Instantiate a tile layer object for the Leisure style (displayed at zoom levels 0-9).
      this.leisure = this.getLayer('Leisure', premium)

      // Instantiate a tile layer object for the Road style (displayed at zoom levels 10-13).
      this.road = this.getLayer('Road', premium)

      // Initialize the map.
      const mapOptions = {
        crs: crs,
        layers: [this.road, this.leisure],
        minZoom: 0,
        maxBounds: [
          [49.562026923812304, -10.83428466254654],
          [61.93445135313357, 7.548212515441139]
        ],
        attributionControl: true
      }

      const LogoControl = L.Control.extend({
        options: {
          position: 'bottomleft'
        },
        onAdd: () => {
          var container = L.DomUtil.create('div', 'os-branding-logo')
          return container
        }
      })

      this.map = L.map('map', mapOptions)
      this.map.addControl(new LogoControl())
      this.map.fitBounds(this.initialBounds)

      // Event handlet for mouse movement to update cursor
      this.map.on('mousemove', (e) => {
        this.lng = e.latlng.lng
        this.lat = e.latlng.lat
      })
    },
    loadMapPointData () {
      const axios = require('axios')

      // Make a request for the map points for a given map
      axios.get(this.callbackURL, {
        params: {
          task: 'mappoint',
          guideid: this.mapId
        }
      })
        .then(response => {
          // success
          this.points = response.data
        })
        .catch(error => {
          // error
          console.log(error)
        })
    },
    addPoints () {
      const s = 8 / 10
      const redIcon = new L.Icon({
        iconUrl: require('@/assets/marker-icon-red.png'),
        shadowUrl: require('@/assets/marker-shadow.png'),
        iconSize: [25 * s, 41 * s],
        iconAnchor: [12 * s, 41 * s],
        popupAnchor: [1, -34 * s],
        shadowSize: [41 * s, 41 * s]
      })
      for (const p of this.points) {
        L.marker([p.Y, p.X], { icon: redIcon })
          .addTo(toRaw(this.map)) // toRaw resolves Vue 3 proxy issue with complex object
          .bindPopup(p.description)
      }
    }
  },
  mounted () {
    this.loadMapPointData()
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
