<script setup>
import RiverMap from "@/components/RiverMap.vue";
import MapPoints from "@/components/MapPoints.vue";

//TODO: Fix claaback so its a property or in a store?
const app = document.getElementById("app");
const callbackURL = app.getAttribute("callback");

const id = app.getAttribute("guideid");
const guideId = id === undefined ? 0 : parseInt(id);

// Decode the Base 64 encoded JSON string holding the map bounds
const bounds = app.getAttribute("bounds");
const jbounds = bounds === undefined ? undefined : JSON.parse(atob(bounds));

const initialBounds =
  jbounds === undefined
    ? [
        // Default to the max extent of the OS tile layers
        [49.562026923812304, -10.83428466254654],
        [61.93445135313357, 7.548212515441139],
      ]
    : [
        [parseFloat(jbounds.n_lat), parseFloat(jbounds.w_lng)],
        [parseFloat(jbounds.s_lat), parseFloat(jbounds.e_lng)],
      ];
</script>

<template>
  <RiverMap
    :callback-u-r-l="callbackURL"
    :initial-bounds="initialBounds"
    :guide-id="guideId"
  />
  <MapPoints :guide-id="guideId"/>
</template>
