<script setup>
import { ref } from "vue";

defineProps({
  point: { type: Object, default: null },
});

const active = ref(false);
const toggle = () => (active.value = !active.value);
</script>

<template>
  <div class="mp-row mp-grid">
    <div>{{ point.id }}</div>
    <div>{{ point.riverguide }}</div>
    <div>{{ point.description }}</div>
    <div>{{ point.type }}</div>
    <div>
      <button @click="toggle">Action</button>
      <div v-if="active">Menu</div>
    </div>
  </div>
</template>
