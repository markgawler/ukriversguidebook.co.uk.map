import axios from "axios";
import { store } from "../store/store";

/* Call API to get any map point that within a radius.
 */
function getPointsByRadius(center, radius) {
  const callbackUrl = store.state.mapAccess.callbackUrl;
  axios
    .get(callbackUrl, {
      params: {
        task: "mappoint",
        radius: radius,
        lat: center.lat,
        lng: center.lng,
      },
    })
    .then((response) => {
      // success
      store.dispatch("mapPoints/storePoints", response.data);
    })
    .catch((error) => {
      // error
      console.log(error);
    });
}

async function savePoints(points) {
  const callbackUrl = store.state.mapAccess.callbackUrl;
  return await axios
    .post(callbackUrl + "&task=savemappoints", points, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })
    .then(function () {
      return true;
    })
    .catch(function (error) {
      console.log(error);
      return false;
    });
}

export { getPointsByRadius, savePoints };
