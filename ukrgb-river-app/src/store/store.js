import { createStore } from "vuex";

// Create a new store instance.
const store = createStore({
  state: () => ({
    markers: [],
    accessToken: "",
    userId: 0
  }),

  mutations: {
    updateAccessToken(state,token) {
        state.accessToken = token;
    },
    addMarker(state, marker) {
      state.markers.push(marker);
    },
    setUserId(state,id) {
        state.userId = id
    }
  },
});
export default store;
