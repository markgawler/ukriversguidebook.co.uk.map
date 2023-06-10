const state = () => ({
  mapId: 0,
  points: [],
  archivedPoints: [],
  nextPointId: -1, // Points not in the DB have -ve ID
});

const getters = {
  getPointById: (state) => (id) => {
    return state.points.find((x) => x.id === id && !x.deleted);
  },

  getPointsByMapId: (state) => (mapId) =>
    state.points.filter((x) => parseInt(x.mapid) === mapId && !x.deleted),

  getMapId: (state) => state.mapId,
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
        X: payload.X,
        Y: payload.Y,
        description: payload.description,
      });
    }
  },
  addNewPoint({ commit, state }, payload) {
    // Add a new MapPoint
    commit("addPoint", {
      id: state.nextPointId,
      description: payload.description,
      X: payload.X,
      Y: payload.Y,
      new: true,
      type: 0, // TODO implement mappoint type
      mapid: state.mapId,
    });
    state.nextPointId--; // decrement next ID to keep the IDs uniqe
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
        X: pt.X,
        Y: pt.Y,
        restore: true,
      });
    });
    commit("deleteNewPoints"); // Remove any new mapPoints
    commit("deleteArchive"); // Clear the Archive as this holds the unmodified points wich have been restored
  },
  async saveUpdates({ commit, state }, saveCallback) {
    const data = {
      mapId: state.mapId,
      update: state.points.filter((pt) => pt.updated && !pt.new), // Aray of the updated points (exclude new)
      delete: state.points.filter((pt) => pt.deleted).map((p) => p.id), // array of the id's of the deleted points
      new: state.points.filter((pt) => pt.new), // Aray of new Mappoints
    };
    // Call the callback to save the data in an external store, if the save is sucsessfull
    //
    if (await saveCallback(data)) {
      // Remore all the deleted points from the store
      commit("hardDeletePoints");
      commit("updatePointCommit");
      commit("deleteArchive");
      commit("deleteNewPoints"); // Delete the new points from the local map as they nao have real IDs in the DB
      commit("reloadPoints"); // dummy mutation to triger reload
    }
  },
};

const mutations = {
  /* store the Map Id of the curent map */
  storeMapId(state, id) {
    state.mapId = id;
  },
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
      if (payload.description != null) {
        state.points[index].description = payload.description;
      }
      if (payload.X != null) {
        state.points[index].X = payload.X;
        state.points[index].Y = payload.Y;
      }
      state.points[index].updated = !payload.restore; // If restoring the point clear the updated flag
    }
  },

  /* Commit the update by clearing the updated flag, this means the cancel action cannot atempt to 
     undo the update.
  */
  updatePointCommit(state) {
    const updates = state.points.filter((pt) => pt.updated);
    updates.map((pt) => (pt.updated = false)); // Clear the updated flag, to indicate save committed.
  },

  // Mark a point as deleted, i.e. soft delete
  softDeletePoint(state, pointId) {
    const index = state.points.findIndex((x) => x.id === pointId);
    state.points[index].deleted = true; // Soft delete
  },

  // Hard delete the points
  hardDeletePoints(state) {
    const result = state.points.filter((pt) => !pt.deleted);
    state.points = result;
  },

  // Delete new points i.e. points not commited to the backend DB
  deleteNewPoints(state) {
    const result = state.points.filter((pt) => !pt.new);
    state.points = result;
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
  deleteArchive(state) {
    state.archivedPoints = [];
  },

  // Dummy mutation which can be subscribed to to triger actions when Points should be reloaded
  // from backend
  reloadPoints() {},
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
