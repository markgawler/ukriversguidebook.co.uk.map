<script setup>
import RiverMap from "@/components/RiverMap.vue";
import { accessToken, authenticated } from "@/components/AccessToken.vue"

//TODO: Fix claaback so its a property or in a store?
const callbackURL = document.getElementById("app").getAttribute("callback");

const mapData = window.mapParams.mapdata;
const initialBounds =
  mapData == undefined
    ? []
    : [
        [parseFloat(mapData.n_lat), parseFloat(mapData.w_lng)],
        [parseFloat(mapData.s_lat), parseFloat(mapData.e_lng)],
      ];
const mapId = mapData.aid == undefined ? 0 : parseInt(mapData.aid);


</script>

<template>
  <div class="home">Section Map</div>
  <RiverMap
    :access-token="accessToken"
    :callback-u-r-l="callbackURL"
    :initial-bounds="initialBounds"
    :map-id="mapId"
    :premium="authenticated"
  />
</template>
