const state = () => ({
  //mapId: 0, // not used yet, need moving from MapPoints store FIXME
  bounds: [],
  center: [],
  radius: 0,
});

const getters = {
  getCenter: (state) => {
    return state.center;
  },
  getCircleParams: (state) => {
    return { center: state.center, radius: state.radius };
  },
};

const actions = {
  storeParameters({ commit }, params) {
    // Store the Map parameters and trigger a load of the MapPoints in the visable area
    commit("storeCenter", params.center);
    commit("storeRadius", params.radius);
    commit("storeBounds", params.bounds);
    commit("loadMap");
  },
};

const mutations = {
  storeBounds(state, b) {
    state.bounds = b;
  },
  storeCenter(state, c) {
    state.center = c;
  },
  storeRadius(state, r) {
    state.radius = r;
  },
  // Dummy mutation which can be subscribed to to triger actions when Map paramters change
  loadMap() {},
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
