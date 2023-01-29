<script setup>
import { ref } from "vue";
import { useStore } from "vuex";

const store = useStore();
const props = defineProps({
  point: { type: Object, default: null },
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
}
</script>

<template>
  <div class="mp-row mp-grid">
    <div>{{ point.id }}</div>
    <div>{{ point.riverguide }}</div>
    <div v-if="editing">{{ point.description }}xx</div>
    <div v-else>{{ point.description }}</div>
    <div v-if="editing">{{ point.type }}xx</div>
    <div v-else>{{ point.type }}</div>
    <div>
      <button @click="toggleMenu">Action</button>
      <ul v-if="active">
        <li @click="toggleEdit">Edit</li>
        <li @click="deletePoint">Delete</li>
      </ul>
    </div>
  </div>
</template>
