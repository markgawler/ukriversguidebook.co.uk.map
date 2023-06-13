<script setup>
import { computed, onMounted } from "vue";
import { useStore } from "vuex";
import MapPointItem from "./MapPointItem.vue";
import { savePoints } from "../network/mapData";
import AddMarkerButton from "./AddMarkerButton.vue";

defineEmits(['edit-map-close'])

const store = useStore();

const props = defineProps({
  mapId: { type: Number, default: 0 },
});

const points = computed(() =>
  store.getters["mapPoints/getPointsByMapId"](props.mapId)
);

const canSave = computed(() =>
  store.getters["mapPoints/getModified"]
)

onMounted(() => {
  store.commit("mapPoints/storeMapId", props.mapId);
});

const cancelEdits = () => {
  store.dispatch("mapPoints/cancelUpdates");
};

const saveEdits = () => {
  store.dispatch("mapPoints/saveUpdates", savePoints);
};

</script>

<template>
  <div class="mp-boarder">
    <div class="mp-buttons-close"><button @click="$emit('edit-map-close')">X</button></div>
    <div class="mp-baseline">
      <div class="mp-row mp-baseline mp-grid">
        <div></div>
        <div>Description</div>
        <div>Type</div>
      </div>
      <div v-for="point in points" :key="point.id">
        <MapPointItem :point="point" />
      </div>
      <AddMarkerButton />

    </div>
    <div class="mp-buttons">
      <div><button :disabled="!canSave" @click="saveEdits">Save</button></div>
      <div><button @click="cancelEdits">Cancel</button></div>
    </div>
  </div>
</template>

<style>
/* Layout for site and link list */

.mp-buttons {
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 10px;
  display: inline-grid;
  padding: 5px;
  width: 98%;
}

.mp-buttons-close {
  text-align: right;
}
.mp-buttons div:first-child {
  text-align: right;
}

.mp-boarder {
  border: 1px solid gray;
  max-width: 600px;
  border-radius: 4px;
  padding-bottom: 1px;
  margin-top: 5px;
}

.mp-baseline {
  border-bottom: 1px solid lightgray;
  margin-bottom: 2px;
}

.mp-row {
  display: grid;
  padding-bottom: 1px;
}

.mp-row input {
  border: none;
  width: 98%;
  padding: 4px;
}

.mp-row input:focus,
textarea:focus,
select:focus {
  border-radius: 5px;
  outline-style: solid;
  outline-color: lightgray;
  outline-width: 1px;
}

.mp-grid {
  grid-template-columns: 0.4fr 6fr 1fr;
  /* Size of items defined inside container */
  grid-gap: 8px;
}
</style>
