<script setup>
import { gridrefNumToLet } from '../utils/GridRefUtils'
import proj4 from 'proj4'
import { computed } from "vue"

const fmt = new Intl.NumberFormat('en', {
  minimumIntegerDigits: '3',
  minimumFractionDigits: '6',
  signDisplay: 'never'
})

const props = defineProps ({
  poss: Object
  })

const gridRef = computed(() => {
    // ne is the EPSG:27700 Northings and Eastings, which need converting to a Grid Ref
    if (props.poss.lng !== 0) {
      const lat = parseFloat(props.poss.lat)
      const lng = parseFloat(props.poss.lng)
      const ne = proj4('EPSG:4326', 'EPSG:27700', [lng, lat])
      return gridrefNumToLet(...ne, 10)
    }
    return ''
  })

const lat = computed (() => {
  return fmt.format(props.poss.lat) + '° N'
})

const lng = computed (() => {
  const l = props.poss.lng
  return fmt.format(l) + (l < 0 ? '° W' : '° E')
})
</script>

<template>
  <div class="wrapper">
    <div class="position">
      <div class="box">Lat/Long:</div>
      <div class="box">{{ lat }}</div>
      <div class="box">{{ lng }}</div>
    </div>
    <div class="position">
      <div class="box">OS Grid Ref:</div>
      <div class="box">{{ gridRef }}</div>
    </div>
  </div>
</template>

<style>
.wrapper {
  font-size: 8pt;
  display: grid;
  grid-gap: 2px;
  grid-template-columns: 20em 15em;
  line-height: normal;
}

.box {
  display: inline-block;
  padding-left: 3px;
  padding-right: 3px;
  user-select: all;
}

.position {
  background-color: #444;
  color: #fff;
  border-radius: 2px;
  padding: 1px;
}
</style>
