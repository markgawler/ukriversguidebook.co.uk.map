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
//callbackURL
function savePoints(points) {
  const callbackUrl = store.state.mapAccess.callbackUrl;

  console.log("callbackUrl", callbackUrl);
  console.log(points);

  return false;
}

export { getPointsByRadius, savePoints };
