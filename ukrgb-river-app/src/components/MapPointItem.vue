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

const type = computed({
  get() {
    return props.point.type;
  },
  set(type) {
    store.dispatch("mapPoints/updatePoint", {
      id: props.point.id,
      type: parseInt(type),
    });
  },
});

const deletePoint = () => {
  store.dispatch("mapPoints/deletePoint", props.point.id);
};
</script>

<template>
  <div class="mp-row mp-grid">
    <div>
      <button @click="deletePoint">X</button>
    </div>
    <div :class="{ mperror: type == 1 }">
      <select v-model="type">
        <option disabled value="0">Select...</option>
<!--        <option value="1">Undefined</option> -->
        <option value="2">Put-in</option>
        <option value="3">Take-out</option>
        <option value="4">Access Point</option>
        <option value="6">River Feature</option>
        <option value="5">Parking</option>
      </select>
    </div>
    <div :class="{ mperror: description == '' }">
      <input v-model="description" />
    </div>
  </div>
</template>

<style>
.mperror {
  border-radius: 5px;
  outline-style: solid;
  outline-color: red;
  outline-width: 1px;
}
</style>
