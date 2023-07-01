<script setup>
defineEmits(["edit-map-open"]);
</script>

<template>
  <div class="edit-map-button">
    <button @click="$emit('edit-map-open')">Edit Map</button>
  </div>
</template>

<style>
.edit-map-button {
  text-align: right;
}
</style>
