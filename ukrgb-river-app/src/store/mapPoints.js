
const state = () => ({
    markers: [],
  })

const mutations = {
    addMarker(state, marker) {
      state.markers.push(marker);
    }
  };

export default {
    namespaced: true,
    state,
    //getters,
    //actions,
    mutations
  }