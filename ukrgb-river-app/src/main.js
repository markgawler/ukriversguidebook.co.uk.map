import { createApp } from 'vue'
import App from './App.vue'
if (window.mapParams === undefined) {
  window.mapParams = {
    url: 'http://172.18.0.3/index.php?option=com_ukrgbmap&tmpl=raw&format=json',
    mapdata: {
      w_lng: '-1.806281',
      s_lat: '50.7448296',
      e_lng: '-1.740877609.',
      n_lat: '50.7570654',
      map_type: '0',
      aid: '2'
    }
  }
}
createApp(App).mount('#app')
