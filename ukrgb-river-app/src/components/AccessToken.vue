<script setup>
import { ref, watch } from "vue";
import axios from "axios";

let tokenExpiresIn = 0; // Inital length of validity of access token (seconds)

const callbackURL = document.getElementById("app").getAttribute("callback");

watch(accessToken, async () => {
  // Access Token has been update sleep until time to refresh the token again
  const sleep = function (x) {
    return new Promise((resolve) => setTimeout(resolve, x * 1000));
  };
  await sleep(tokenExpiresIn - 7);
  getAccessToken();
});
getAccessToken();

function getAccessToken() {
  // Get the access token and the users authentication status (logged in or not)
  axios
    .get(callbackURL, {
      params: {
        task: "authenticate",
        cb: Date.now(),
      },
    })
    .then((response) => {
      authenticated.value = response.data.userId > 0; // Authenticated user if userId > 0
      tokenExpiresIn = response.data.expiresIn;
      accessToken.value = response.data.accessToken;
    })
    .catch((error) => {
      authenticated.value = false;
      console.log(error);
    });
}
</script>
<script>
export const accessToken = ref("");
export const authenticated = ref(false);
</script>
<template><div></div></template>
