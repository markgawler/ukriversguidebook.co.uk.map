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
      tokenExpiresIn = response.data.expiresIn;
      store.commit("updateAccessToken", response.data.accessToken);
      store.commit("setUserId", response.data.userId);
    })
    .catch((error) => {
      store.commit("setUserId", 0);
      store.commit("updateAccessToken", "");
      console.log(error);
      cancelPolling = true;
    });
}
</script>

<template><div></div></template>
