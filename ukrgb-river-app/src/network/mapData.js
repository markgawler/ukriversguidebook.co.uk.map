import axios from "axios";
import { store } from "../store/store";

/* Call API to get any map point that within a radius.
 * Specifying the MapId calls a special form on the get by radius where the following is fetched:
*  - Eveything for the Map 
*  - Access point for other maps
 */
function getPointsByRadius(center, radius, mapId, disableCache) {
  const callbackUrl = store.state.mapAccess.callbackUrl;

  // Disabling cacheing is required when editing
  const headders = disableCache ? {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
  } : {};
  console.log (headders)
  axios
    .get(callbackUrl,
      {
        headers: headders,
        params: {
        task: "mappoint",
        radius: radius,
        lat: center.lat,
        lng: center.lng,
        mapid: mapId
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
  const token = store.state.mapAccess.token;
  return await axios
    .post(callbackUrl + "&task=savemappoints", points, {
      headers: {
        "Content-Type": "multipart/form-data",
        "X-CSRF-TOKEN": token,
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
