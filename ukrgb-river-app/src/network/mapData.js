import axios from "axios";
import { store } from "../store/store";

/* Call API to get any map point that within a radius.
 * Specifying the MapId calls a special form on the get by radius where the following is fetched:
*  - Eveything for the Map 
*  - Access point for other maps
 */
function getPointsByRadius(center, radius, mapId, version, disableCache = false) {
  const callbackUrl = store.state.mapAccess.callbackUrl;

  // Disabling cacheing is required when debuging 
  const headders = disableCache ? {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
  } : {};
  axios
    .get(callbackUrl,
      {
        headers: headders,
        params: {
        task: "mappoint",
        radius: radius,
        lat: center.lat,
        lng: center.lng,
        mapid: mapId,
        version: version
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
