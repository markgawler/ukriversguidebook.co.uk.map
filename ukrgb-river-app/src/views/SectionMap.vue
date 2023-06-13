<script setup>
import RiverMap from "@/components/RiverMap.vue";
import EditMapButton from "@/components/EditMapButton.vue";
import MapPoints from "@/components/MapPoints.vue";
import { ref } from "vue";
const app = document.getElementById("app");
const id = app.getAttribute("mapid");
const mapId = id == null ? 0 : parseInt(id);

// Decode the Base 64 encoded JSON string holding the map bounds
const bounds = app.getAttribute("bounds");
const jbounds = bounds === undefined ? undefined : JSON.parse(atob(bounds));
const canEdit = app.getAttribute("edit") === "full"
const editing = ref(false);

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
    ]
  ;
const editMap = () => {
  editing.value = !editing.value
}

</script>

<template>
  <div v-if="!editing && canEdit">
    <EditMapButton @edit-map-open="editMap" />
  </div>
  <RiverMap :initial-bounds="initialBounds" :map-id="mapId" :editing="editing" :can-edit="canEdit" />
  <div v-if="editing && canEdit">
    <MapPoints :map-id="mapId" @edit-map-close="editMap" />
  </div>
</template>
