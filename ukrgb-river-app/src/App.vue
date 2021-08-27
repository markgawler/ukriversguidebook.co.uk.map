<template>
<div>
  <div>
    Authenticated: {{ authenticated }}
  </div>
  <Map v-bind:premium=authenticated v-bind:initialBounds=initialBounds v-bind:mapId=mapId v-bind:callback=callback />
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
    callback: '',
    authenticated: false
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
        this.userId = 0
        /*  this.userId = parseInt(window.mapParams.userId)

        if (this.userId !== 0) {
          this.authenticated = true
          this.userId = parseInt(mapData.userId)
        } else {
          this.authenticated = false
          this.userId = 0
        }
        console.log('UserId', this.userId) */
      }
    },
    checkAuthStatus () {
      const axios = require('axios')
      console.log('Check Auth')
      // Make a request for the map points for a given map
      axios.get(this.callback, {
        params: {
          task: 'authenticate',
          userid: this.userId
        }
      })
        .then(response => {
          // handle success
          console.log('Auth response', response)
          this.authenticated = response.data.userid !== 0
          console.log('API Userid: ', response.data.userid)
        })
        .catch(error => {
          this.authenticated = false
          console.log(error)
        })
        .then(() => {
          // always executed
        })
    }
  },
  created () {
    this.readGlobal()
    this.checkAuthStatus()
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
