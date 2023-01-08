<script setup>
import AccessToken from './components/AccessToken.vue';
</script>

<template>
  <AccessToken></AccessToken>
  <router-link to="/">Home</router-link> |
  <router-link to="/about">About</router-link> |
  <router-link to="/section">Section</router-link> |
  <router-view></router-view>
</template>

<style scoped></style>
