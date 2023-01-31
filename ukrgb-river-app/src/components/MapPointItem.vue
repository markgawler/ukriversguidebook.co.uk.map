<script setup>
import { ref, computed } from "vue";
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
    store.commit("mapPoints/updatePoint", {
      id: props.point.id,
      description: description,
    });
  },
});

const active = ref(false);
const editing = ref(false);
const toggleMenu = () => {
  active.value = !active.value;
};
const toggleEdit = () => {
  editing.value = !editing.value;
  active.value = false;
};

const deletePoint = () => {
  store.commit("mapPoints/deletePoint", props.point.id);
};
</script>

<template>
  <div class="mp-row mp-grid">
    <div>{{ point.id }}</div>
    <div>{{ point.riverguide }}</div>
    <div v-if="editing"><input v-model="description" /></div>
    <div v-else>{{ point.description }}</div>
    <div>{{ point.type }}</div>
    <div>
      <button @click="toggleMenu">X</button>
      <ul v-if="active">
        <li @click="toggleEdit">Edit</li>
        <li @click="deletePoint">Delete</li>
      </ul>
    </div>
  </div>
</template>
