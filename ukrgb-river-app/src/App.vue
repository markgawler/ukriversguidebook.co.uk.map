<template>
<div>
  <Map v-bind:authenticated="false" v-bind:initialBounds=initialBounds v-bind:mapId=mapId v-bind:callback=callback />
</div>
</template>

<script>
import Map from './components/Map.vue'

export default {
  name: 'App',
  components: {
    Map
  },
  data: () => ({
    initialBounds: [],
    mapId: 0,
    callback: ''
  }),
  methods: {
    readGlobal () {
      let mapData = null
      if (window.mapParams !== undefined) {
        mapData = window.mapParams.mapdata
        this.initialBounds = [
          [parseFloat(mapData.n_lat), parseFloat(mapData.w_lng)],
          [parseFloat(mapData.s_lat), parseFloat(mapData.e_lng)]
        ]
        this.mapId = parseInt(mapData.aid)
        this.callback = window.mapParams.url
      }
    }
  },
  created () {
    this.readGlobal()
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  line-height: normal;
}
</style>
