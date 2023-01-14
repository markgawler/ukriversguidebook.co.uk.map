import { createApp } from "vue";
import { createRouter, createWebHistory } from "vue-router";
import App from "./App.vue";
import MapHome from "@/views/MapHome.vue";
import AboutMaps from "@/views/AboutMaps.vue";
import SectionMap from "@/views/SectionMap.vue";

const app = document.getElementById("app");
const mode = app.getAttribute("mode");
let routes = [];

if (mode.toLowerCase() === "plugin") {
  routes = [
    { path: "/", name: "Section Map", component: SectionMap },
  ];
} else {
  routes = [
    { path: "/", name: "Home", component: MapHome },
    { path: "/about", name: "About", component: AboutMaps },
    { path: "/section", name: "Section Map", component: SectionMap },
  ];
}

const router = createRouter({
  history: createWebHistory(),
  routes,
});
createApp(App).use(router).mount("#app");
