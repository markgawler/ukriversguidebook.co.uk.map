<script setup>
import { ref, watch } from "vue";
import axios from "axios";
import RiverMap from "./components/RiverMap.vue";

const accessToken = ref("");
let authenticated = false;
let tokenExpiresIn = 0; // Inital length of validity of access token (seconds)
const callbackURL =
  window.mapParams.url == undefined ? "" : window.mapParams.url;

const mapData = window.mapParams.mapdata;
const initialBounds =
  mapData == undefined
    ? []
    : [
        [parseFloat(mapData.n_lat), parseFloat(mapData.w_lng)],
        [parseFloat(mapData.s_lat), parseFloat(mapData.e_lng)],
      ];
const mapId = mapData.aid == undefined ? 0 : parseInt(mapData.aid);

function getAccessToken() {
  // Get the access token and the users authentication status (logged in or not)
  axios
    .get(callbackURL, {
      params: {
        task: "authenticate",
        cb: Date.now(),
      },
    })
    .then((response) => {
      authenticated = response.data.userId > 0; // Authenticated user if userId > 0
      tokenExpiresIn = response.data.expiresIn;
      accessToken.value = response.data.accessToken;
    })
    .catch((error) => {
      authenticated = false;
      console.log(error);
    });
}

watch(accessToken, async () => {
  // Access Token has been update sleep until time to refresh the token again
  const sleep = function (x) {
    return new Promise((resolve) => setTimeout(resolve, x * 1000));
  };
  await sleep(tokenExpiresIn - 7);
  getAccessToken();
});

getAccessToken();
</script>

<template>
  <RiverMap
    :access-token="accessToken"
    :callback-u-r-l="callbackURL"
    :initial-bounds="initialBounds"
    :map-id="mapId"
    :premium="authenticated"
  />
</template>

<style scoped></style>
