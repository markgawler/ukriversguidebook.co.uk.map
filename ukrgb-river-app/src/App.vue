<template>
<div>
  <div>
    Authenticated: {{ authenticated }}
  </div>
  <Map v-bind:premium=authenticated v-bind:accessToken=accessToken v-bind:initialBounds=initialBounds v-bind:mapId=mapId v-bind:callbackURL=callbackURL />
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
    callbackURL: '',
    authenticated: false,
    accessToken: '',
    tokenExpiresIn: 0 // Inital length of validity of access token (seconds)
  }),
  watch: {
    accessToken () {
      this.refreshTokenTimeout(this.tokenExpiresIn - 30)
    }
  },
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
        this.callbackURL = window.mapParams.url
        this.userId = 0
      }
    },
    async refreshTokenTimeout (inSeconds) {
      // Access Token has been update sleep until time to refresh the token again
      const sleep = function (x) { return new Promise(resolve => setTimeout(resolve, x * 1000)) }
      await sleep(inSeconds)
      this.getAccessToken()
    },
    getAccessToken () {
      // Get the access token and the users authentication status (logged in or not)
      const axios = require('axios')
      console.log('Check Auth')
      axios.get(this.callbackURL, {
        params: {
          task: 'authenticate'
        }
      })
        .then(response => {
          console.log('Auth response', response)
          this.authenticated = response.data.userId > 0 // Authenticated user if userId > 0
          this.tokenExpiresIn = response.data.expiresIn
          this.accessToken = response.data.accessToken
        })
        .catch(error => {
          this.authenticated = false
          console.log(error)
        })
    }
  },
  created () {
    this.readGlobal()
    this.getAccessToken()
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
