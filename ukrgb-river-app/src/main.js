import { createApp } from 'vue'
import App from './App.vue'
if (window.mapParams === undefined) {
  window.mapParams = {
    url: 'http://localhost:3000/index.php?option=com_ukrgbmap&tmpl=raw&format=json',
    mapdata: {
      w_lng: '-1.7105113819807',
      s_lat: '52.182724885006',
      e_lng: '-1.6913676124912',
      n_lat: '52.201556311097',
      map_type: '0',
      aid: '2293'
    }
  }
}
createApp(App).mount('#app')
