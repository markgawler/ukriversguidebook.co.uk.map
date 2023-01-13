<script setup>
import { gridrefNumToLet } from "../utils/GridRefUtils";
import proj4 from "proj4";
import { computed } from "vue";

const fmt = new Intl.NumberFormat("en", {
  minimumIntegerDigits: "3",
  minimumFractionDigits: "6",
  signDisplay: "never",
});

const props = defineProps({
  points: { type: Object, default:  () => ({}) },
});



</script>

<template>
   <div class="">Points</div>
</template>

<style>

</style>
