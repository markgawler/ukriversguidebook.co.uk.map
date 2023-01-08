import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import MapHome from "@/views/MapHome.vue";
import AboutMaps from "@/views/AboutMaps.vue";
import SectionMap from "@/views/SectionMap.vue";

// Load test data is required, this is done this way for compatability with original map.
// TODO: Fix the intial map data loading.
if (window.mapParams === undefined) {
  window.mapParams = {
    // Test Server
    //url: "http://localhost:3000/index.php?option=com_ukrgbmap&tmpl=raw&format=json",
    // Map bounds and Joomla article ID
    mapdata: {
      w_lng: "-3.85",
      s_lat: "50.4",
      e_lng: "-3.9",
      n_lat: "50.55",
      map_type: "0",
      //aid: "2293",
    },
  };
}

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", name: "Home", component: MapHome },
    { path: "/about", name: "About", component: AboutMaps },
    { path: "/section", name: "Section Map", component: SectionMap},

  ],
});
createApp(App).use(router).mount("#app");
