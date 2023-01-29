<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import MapPointItem from "./MapPointItem.vue";

const store = useStore();

const props = defineProps({
  guideId: { type: Number, default: 0 },
});
const points = computed(() => store.getters["mapPoints/getPointsByGuideId"](props.guideId));

</script>

<template>
  {{  guideId }}
  <div class="mp-row mp-top mp-grid">
    <div></div>
    <div>Guide</div>
    <div>Description</div>
    <div>Type</div>
    <div>Action</div>
  </div>
  <div v-for="point in points" :key="point.id">
    <MapPointItem :point="point" />
  </div>
</template>

<style>
/* Layout for site and link list */
p {
  text-align: left;
}
.mp-top {
  border-top: 1px solid gray;
}
.mp-row {
  max-width: 560px;
  display: grid;
}
.mp-row div {
  border-bottom: 1px solid gray;
  border-left: 1px solid gray;
}
.mp-row div:last-child {
  border-right: 1px solid gray;
}

.mp-grid {
  grid-template-columns: 0.4fr 1fr 6fr 1fr 1fr; /* Size of items defined inside container */
}
</style>
