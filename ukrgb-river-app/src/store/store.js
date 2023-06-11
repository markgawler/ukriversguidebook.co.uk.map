import { createStore } from "vuex";
import mapPoints from "./mapPoints";
import mapAccess from "./mapAccess";
import mapParameters from "./mapParameters";

// Create a new store instance.
export const store = createStore({
  modules: {
    mapPoints,
    mapAccess,
    mapParameters
  },
});
export default store;
