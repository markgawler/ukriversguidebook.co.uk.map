import { createStore } from "vuex";

// Create a new store instance.
const store = createStore({
  state: () => ({
    markers: [],
    accessToken: ""
  }),

  mutations: {
    updateAccessToken(state,token) {
        state.accessToken = token;
    },
    addMarker(state, marker) {
      state.markers.push(marker);
    },
  },
});
export default store;
