const state = () => ({
  points: [],
});

const getters = {
  getPointById: (state) => (id) => {
    return state.points.find((x) => x.id == id);
  },

  getPointsByGuideId: (state) => (guideId) => state.points.filter((x) => x.riverguide == guideId),

  getPoints: (state) => state.points,

};

const actions = {
  storePoints({ commit, state }, pts) {
    // Add new markers to the store
    for (const p of pts) {
      // Check if the point exists
      if (!state.points.find((x) => x.id == p.id)) {
        commit("addPoint", p);
      }
    }
  },
  // storePoint({ commit, state }, marker) {
  //   // Add new markers to the store
  //   const id = marker.id;
  //   if (state.markers[id] === undefined) {
  //     commit("addMarker", marker);
  //   }
  // },
};

const mutations = {
  addPoint(state, point) {
    state.points.push(point);
  },

  deletePoint (state, pointId) {
    const index = state.points.findIndex((x) => x.id === pointId)
    state.points.splice(index, 1)
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
