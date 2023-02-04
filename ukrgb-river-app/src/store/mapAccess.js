
const state = () => ({
    accessToken: "",
    callbackUrl: ""
  })


 const mutations = {
    updateAccessToken(state,token) {
        state.accessToken = token;
    },
    setUserId(state,id) {
        state.userId = id
    },
    setCallbackUrl(state,url) {
      state.callbackUrl = url;
    }
  };

export default {
    namespaced: true,
    state,
    //getters,
    //actions,
    mutations
  }