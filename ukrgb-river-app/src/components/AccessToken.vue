<script setup>
import { onMounted } from "vue";
import { useStore } from "vuex";
import axios from "axios";

const store = useStore();
let tokenExpiresIn = 0; // Inital length of validity of access token (seconds)

const callbackURL = document.getElementById("app").getAttribute("callback");
let cancelPolling = false;

async function doTokenPolling() {
  while (!cancelPolling) {
    await getAccessToken();
    const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
    await delay((tokenExpiresIn - 5) * 1000);
  }
}

onMounted(() => {
  doTokenPolling();
});

function getAccessToken() {
  // Get the access token and the users authentication status (logged in or not)
  return axios
    .get(callbackURL, {
      params: {
        task: "authenticate",
        cb: Date.now(),
      },
    })
    .then((response) => {
      authenticated.value = response.data.userId > 0; // Authenticated user if userId > 0
      tokenExpiresIn = response.data.expiresIn;
      console.log("Token: ",response.data.accessToken)
      store.commit("updateAccessToken", response.data.accessToken);
      console.log('Stored Token:',store.state.accessToken)
    })
    .catch((error) => {
      authenticated.value = false;
      console.log(error);
      cancelPolling = true;
    });
}
</script>
<script>
import { ref } from "vue";

export const accessToken = ref("");
export const authenticated = ref(false);
</script>
<template><div></div></template>
