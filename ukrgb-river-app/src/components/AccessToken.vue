<script setup>
import { onMounted, computed } from "vue";
import { useStore } from "vuex";
import axios from "axios";

const store = useStore();
let tokenExpiresIn = 0; // Initial length of validity of access token (seconds)

const callbackUrl = computed(() => store.state.mapAccess.callbackUrl);
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
    .get(callbackUrl.value, {
      params: {
        task: "authenticate",
        cb: Date.now(),
      },
    })
    .then((response) => {
      tokenExpiresIn = response.data.expiresIn;
      store.commit("mapAccess/updateAccessToken", response.data.accessToken);
      store.commit("mapAccess/setUserId", response.data.userId);
    })
    .catch((error) => {
      store.commit("mapAccess/setUserId", 0);
      store.commit("mapAccess/updateAccessToken", "");
      console.log(error);
      cancelPolling = true;
    });
}
</script>

<template><div></div></template>
