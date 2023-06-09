<script setup>
import { useStore } from "vuex";

const store = useStore();

const onClickControl = () => {
    const center = store.getters["mapParameters/getCenter"]
    const point = {
      description: "",
      X: center.lng,
      Y: center.lat
    }
    store.dispatch("mapPoints/addNewPoint",point);
}
</script>
    
<template>
      <button  @click="onClickControl" >New</button>
</template>

<style>
</style>