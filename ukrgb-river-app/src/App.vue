<template>
<div>
  <Map v-bind:authenticated="false" v-bind:center={n,e} />
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
    n: 0,
    e: 0
  }),
  methods: {
    readGlobal () {
      let mapData = null
      console.log(window.mapParams)
      if (window.mapParams === undefined) {
        console.log('Undefined')
      } else {
        console.log('Defined')
        mapData = window.mapParams.mapdata
        console.log(mapData)
        this.n = parseFloat(mapData.n_lat)
        this.e = parseFloat(mapData.e_lng)
        console.log(this.n, this.e)
      }
    }
  },
  mounted () {
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
