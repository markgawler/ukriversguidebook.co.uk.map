import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
// Load test data is required, this is done this way for compatability with original map.
// TODO: Fix the intial map data loading.
if (window.mapParams === undefined) {
  window.mapParams = {
    // Test Server
    url: "http://localhost:3000/index.php?option=com_ukrgbmap&tmpl=raw&format=json",
    // Map bounds and Joomla article ID
    mapdata: {
      w_lng: "-3.85",
      s_lat: "50.4",
      e_lng: "-3.9",
      n_lat: "50.55",
      map_type: "0",
      aid: "2293",
    },
  };
}
createApp(App).mount("#app");
