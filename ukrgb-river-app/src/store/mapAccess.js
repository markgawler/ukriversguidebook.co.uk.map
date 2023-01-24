
const state = () => ({
    accessToken: "",
    userId: 0
  })

 const mutations = {
    updateAccessToken(state,token) {
        state.accessToken = token;
    },
    setUserId(state,id) {
        state.userId = id
    }
  };

export default {
    namespaced: true,
    state,
    //getters,
    //actions,
    mutations
  }