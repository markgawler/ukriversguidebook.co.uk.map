const state = () => ({
  points: [],
  archivedPoints: [],
});

const getters = {
  getPointById: (state) => (id) => {
    return state.points.find((x) => x.id === id && !x.deleted);
  },

  getPointsByGuideId: (state) => (guideId) =>
    state.points.filter((x) => x.riverguide === guideId && !x.deleted),
};

const actions = {
  storePoints({ commit, state }, pts) {
    // Add new point to the store
    for (const p of pts) {
      // Check if the point exists
      if (!state.points.find((x) => x.id === p.id)) {
        commit("addPoint", p);
      }
    }
  },
  updatePoint({ commit, state }, payload) {
    const index = state.points.findIndex((x) => x.id === payload.id);
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
    // undelete soft delete of points
    const pts = state.points.filter((x) => x.deleted);
    pts.forEach((pt) => {
      commit("unDeletePoint", pt);
    });
    // restore points from archive
    state.archivedPoints.forEach((pt) => {
      commit("updatePoint", {
        id: pt.id,
        description: pt.description,
        restore: true,
      });
    });
    commit('deleteArchive')
  },
  saveUpdates({ commit, state }) {
    state.points.forEach((pt, index) => {
      if (pt.deleted){
        commit("deletePoint", index);
      }
      if (pt.updated){
        commit("updatePointCommit", index);
      }
      commit('deleteArchive')
    });
  },
};

const mutations = {
  /* Add a point to the store, no check for duplicate is performed, the
     action 'storePoints' is assumed to have taken care of this
  */
  addPoint(state, point) {
    state.points.push(point);
  },

  // Update a point with new description and set the updated flag
  // todo: update other properties of the point.
  updatePoint(state, payload) {
    const index = state.points.findIndex((x) => x.id === payload.id);
    if (index >= 0) {
      state.points[index].description = payload.description;
      state.points[index].updated = !payload.restore; // If restoring the point clear the updated flag
    }
  },

  /* Commit the update by clearing the updated flag, this means the cancel action cannot atempt to 
     undo the update.
  */
  updatePointCommit(state, index) {
    state.points[index].updated = false; // Clear the updated flag, to indicate save committed.
  },

  // Mark a point as deleted, i.e. soft delete
  softDeletePoint(state, pointId) {
    const index = state.points.findIndex((x) => x.id === pointId);
    state.points[index].deleted = true; // Soft delete
  },

  // Hard delete the point
  deletePoint(state, index) {
    //const index = state.points.findIndex((x) => x.id === pointId);
    state.points.splice(index, 1);
  },

  // Remove the deleted marker from a point, effectively undeleting it.
  unDeletePoint(state, point) {
    const index = state.points.findIndex((x) => x.id === point.id);
    state.points[index].deleted = false; // Undelete
  },

  // Take a clone of a point, so it can be restored if the edit is canceled.
  archivePoint(state, index) {
    const pt = { ...state.points[index] }; // make a shallow clone
    state.archivedPoints.push(pt);
  },

  // Clear the archived points store. Used after a cancel or save operation
  deleteArchive(state){
    state.archivedPoints = [];
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
