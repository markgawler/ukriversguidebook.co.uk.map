<script setup>
import { computed } from "vue";
import { useStore } from "vuex";

const store = useStore();
const props = defineProps({
  point: { type: Object, default: null },
});

const description = computed({
  get() {
    return props.point.description;
  },
  set(description) {
    store.dispatch("mapPoints/updatePoint", {
      id: props.point.id,
      description: description,
    });
  },
});

const deletePoint = () => {
  store.commit("mapPoints/deletePoint", props.point.id);
};
</script>

<template>
  <div class="mp-row mp-grid">
    <div>
      <button @click="deletePoint">X</button>
    </div>
    <div><input v-model="description" /></div>
    <div>{{ point.type }}</div>
  </div>
</template>
