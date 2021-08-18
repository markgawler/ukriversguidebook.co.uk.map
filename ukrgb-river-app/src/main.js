import { createApp } from 'vue'
import App from './App.vue'

window.mapParams = { mapdata: { w_lng: '-1.806281', s_lat: '50.7448296', e_lng: '-1.740877609.', n_lat: '50.7570654', map_type: '0', aid: '1' } }
createApp(App).mount('#app')

// tl {"lat":50.757065482740984,"lng":-1.8062819199767086}
// br {"lat":50.744829658951765,"lng":-1.7408776096097731}
