import { createApp } from 'vue'
import App from './App.vue'

window.mapParams = { mapdata: { w_lng: '-2', s_lat: '52', e_lng: '-1.77002.', n_lat: '50.7486', map_type: '0', aid: '1' } }
createApp(App).mount('#app')
