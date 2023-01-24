import { createStore } from "vuex";
import mapPoints from "./mapPoints";
import mapAccess from "./mapAccess";

// Create a new store instance.
const store = createStore({
  modules: {
    mapPoints,
    mapAccess
  },

  state: () => ({
    markers: [],
    accessToken: "",
    userId: 0
  }),
});
export default store;
