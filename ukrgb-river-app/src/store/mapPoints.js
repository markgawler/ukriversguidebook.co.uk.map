const state = () => ({
  points: [],
  archivedPoints: [],
});

const getters = {
  getPointById: (state) => (id) => {
    return state.points.find((x) => x.id == id && !x.deleted);
  },

  getPointsByGuideId: (state) => (guideId) =>
    state.points.filter((x) => x.riverguide == guideId && !x.deleted),

  //getPoints: (state) => state.points,
};

const actions = {
  storePoints({ commit, state }, pts) {
    // Add new point to the store
    for (const p of pts) {
      // Check if the point exists
      if (!state.points.find((x) => x.id == p.id)) {
        commit("addPoint", p);
      }
    }
  },
  updatePoint({ commit, state }, payload) {
    const index = state.points.findIndex((x) => x.id == payload.id);
    if (index >= 0) {
      if (state.points[index].updated !== true) {
        commit("archivePoint", index);
      }
      commit("updatePoint", {
        id: payload.id,
        description: payload.description,
      });
    }
  },
  cancelUpdates({ commit, state }) {
    const pts = state.points.filter((x) => x.deleted);
    pts.forEach((pt) => {
      commit("unDeletePoint", pt);
    });

    state.archivedPoints.forEach((pt) => {
      commit("updatePoint", {
        id: pt.id,
        description: pt.description,
        restore: true,
      });
    });
    state.archivedPoints = [];
  },
};

const mutations = {
  // Add a point to the store, no chech for duplicate is perfoemed, the 
  // action 'storePoints' is asumed to have taken care of this
  addPoint(state, point) {
    state.points.push(point);
  },

  // Update a point with new description and set the updated flag
  // todo: update other properties of the point.
  updatePoint(state, payload) {
    const index = state.points.findIndex((x) => x.id === payload.id);
    if (index >= 0) {
      state.points[index].description = payload.description;
      state.points[index].updated = !payload.restore;  // If restoring the point clear the updated flag
    }
  },

  // Mark a point as deleted, i.e. soft delete
  deletePoint(state, pointId) {
    const index = state.points.findIndex((x) => x.id === pointId);
    state.points[index].deleted = true; // Soft delete
  },

  // Remore the deleted marker from a point, efectivly undeleting it.
  unDeletePoint(state, point) {
    const index = state.points.findIndex((x) => x.id === point.id);
    state.points[index].deleted = false; // Undelete
  },

  // Take a clone of a point so it can be restored if the edit is canceled.
  archivePoint(state, index) {
    const cloanedPoint = { ...state.points[index] }; // make a shalow cloan
    state.archivedPoints.push(cloanedPoint);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
