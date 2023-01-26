const state = () => ({
  markers: [],
});

const actions = {
  storePoints({ commit, state }, markers) {
    // Add new markers to the store
    for (const mk of markers) {
      const id = mk.id;
      if (state.markers[id] === undefined) {
        commit("addMarker", mk);
      }
    }
  },
  storePoint({ commit, state }, marker) {
    // Add new markers to the store
    const id = marker.id;
    if (state.markers[id] === undefined) {
      commit("addMarker", marker);
    }
  },
};

const mutations = {
  addMarker(state, marker) {
    state.markers[marker.id] = marker;
  },
};

export default {
  namespaced: true,
  state,
  //getters,
  actions,
  mutations,
};
