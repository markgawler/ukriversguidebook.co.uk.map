<script setup>
import { useStore } from "vuex";

const store = useStore();

const onClickControl = () => {
  console.log('clicked Add')
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
  <div>
    <div class="radio-toolbar">
      <input id="radio1" type="radio" name="radios" @click="onClickControl" />
      <label for="radio1">Add </label>
    </div>
  </div>
</template>

<style>
.radio-toolbar {
  display: flex;
}

.radio-toolbar input[type="radio"] {
  display: none;
}

.radio-toolbar label {
  display: inline-block;
  background-color: #ddd;
  padding: 4px 11px;
  font-family: Arial;
  font-size: 16px;
  cursor: pointer;
}

.radio-toolbar input[type="radio"]:checked+label {
  background-color: #bbb;
}
</style>