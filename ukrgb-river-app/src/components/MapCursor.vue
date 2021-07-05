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

<script>
import { gridrefNumToLet } from '../utils/GridRefUtils'
import proj4 from 'proj4'

const fmt = new Intl.NumberFormat('en', {
  minimumIntegerDigits: '3',
  minimumFractionDigits: '6',
  signDisplay: 'never'
})

export default {
  name: 'MapCursor',
  props: {
    poss: Object
  },
  computed: {
    gridRef () {
      // ne is the EPSG:27700 Northings and Eastings, which need converting to a Grid Ref
      if (this.poss.lng !== 0) {
        const lat = parseFloat(this.poss.lat)
        const lng = parseFloat(this.poss.lng)
        const ne = proj4('EPSG:4326', 'EPSG:27700', [lng, lat])
        return gridrefNumToLet(...ne, 10)
      }
      return ''
    },
    lat () {
      return fmt.format(this.poss.lat) + '° N'
    },
    lng () {
      const l = this.poss.lng
      return fmt.format(l) + (l < 0 ? '° W' : '° E')
    }
  }
}
</script>

<style>
.wrapper {
  display: grid;
  grid-gap: 2px;
  grid-template-columns: 13.5em 10em;
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
  font-size: 8pt;
}
</style>
