
const state = () => ({
    accessToken: "", // Access token for OSMap data
    callbackUrl: "", // The Site URL
    token: ""        // Joomla Sesion Token
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
    },
    setToken(state,url) {
      state.token = url;
    }
  };

export default {
    namespaced: true,
    state,
    //getters,
    //actions,
    mutations
  }