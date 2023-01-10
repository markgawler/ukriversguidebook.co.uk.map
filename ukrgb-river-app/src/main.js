import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import MapHome from "@/views/MapHome.vue";
import AboutMaps from "@/views/AboutMaps.vue";
import SectionMap from "@/views/SectionMap.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", name: "Home", component: MapHome },
    { path: "/about", name: "About", component: AboutMaps },
    { path: "/section", name: "Section Map", component: SectionMap},
  ],
});
createApp(App).use(router).mount("#app");
