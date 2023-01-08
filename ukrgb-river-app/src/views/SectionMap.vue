<script setup>
import RiverMap from "@/components/RiverMap.vue";
import { accessToken, authenticated } from "@/components/AccessToken.vue";

//TODO: Fix claaback so its a property or in a store?
const app = document.getElementById("app");
const callbackURL = app.getAttribute("callback");
const id = app.getAttribute("guideid");
const guideId = id == undefined ? 0 : parseInt(id)

const mapData = window.mapParams.mapdata;
const initialBounds =
  mapData == undefined
    ? []
    : [
        [parseFloat(mapData.n_lat), parseFloat(mapData.w_lng)],
        [parseFloat(mapData.s_lat), parseFloat(mapData.e_lng)],
      ];
</script>

<template>
  <div class="home">Section Map</div>
  <RiverMap
    :access-token="accessToken"
    :callback-u-r-l="callbackURL"
    :initial-bounds="initialBounds"
    :map-id="guideId"
    :premium="authenticated"
  />
</template>
