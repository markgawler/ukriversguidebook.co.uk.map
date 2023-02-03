import axios from "axios";
import { store } from "../store/store";

/* Call API to get any map point that within a radius.
 */
function getPointsByRadius(center, radius, callbackURL) {
  axios
    .get(callbackURL, {
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
  console.log(points);
}

export { getPointsByRadius, savePoints };