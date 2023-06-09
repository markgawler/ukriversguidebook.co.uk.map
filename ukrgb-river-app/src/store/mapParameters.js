const state = () => ({
  //mapId: 0, // not used yet, need moving from MapPoints store FIXME
  bounds: [],
  center: [],
});

const getters = {
  getCenter: (state) => {
    return state.center;
  }
};

const actions = {
  storeBounds({ commit }, bounds) {
    // Store the Map Bounds
    // LeefletJS LatLngBounds object https://leafletjs.com/reference.html#latlngbounds
    commit("storeBounds", bounds);
  },
  storeCenter({ commit }, center) {
    // Store the centerpoint of the map
    // LeefletJS LatLng object https://leafletjs.com/reference.html#latlng
    commit("storeCenter", center);
  },
};

const mutations = {
  storeBounds(state, b) {
    state.bounds = b;
  },
  storeCenter(state, c) {
    state.center = c;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
