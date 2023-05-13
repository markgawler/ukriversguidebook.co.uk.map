const express = require("express");
const app = express();
const cors = require("cors");
const request = require("request");
const dotenv = require("dotenv");

dotenv.config();
const port = 3000;
const key = process.env.APIKEY;
const secret = process.env.APISECRET;

app.use(cors());
app.get("/index.php", (req, res) => {
  const query = req.query;
  const task = query.task;
  if (query.hasOwnProperty("task")) {
    console.log("Task:", task);
    // console.log(" query", query);
  }
  switch (task) {
    case "authenticate":
      const osApiUrl = "https://api.os.uk/oauth2/token/v1";

      request(
        {
          method: "POST",
          url: osApiUrl,
          auth: {
            username: key,
            password: secret,
          },
          form: {
            grant_type: "client_credentials",
          },
          encoding: null,
        },
        (error, response, buffer) => {
          if (error) {
            res
              .status(500)
              .send("Failed to get access token, check the API key and secret");
          }

          const contentType = response.headers["content-type"];
          const statusCode = response.statusCode;
          res.set("Content-Type", contentType);
          res.status(statusCode);
          const client_credentials = JSON.parse(buffer.toString());
          const responce = {
            userId: 0,
            accessToken: client_credentials.access_token,
            expiresIn: client_credentials.expires_in,
          };
          res.send(responce);
        }
      );

      break;
    case "mappoint":
      points = [
        {
          id: 1,
          riverguide: 2293,
          X: -3.4427595700536,
          Y: 54.468013454711,
          type: 1,
          mapid: 7,
          description: "River Calder - Thornholme Farm to Sellafield",
        },
        {
          id: 2,
          riverguide: 2293,
          X: -3.4777318000052,
          Y: 54.440806743921,
          type: 1,
          mapid: 7,
          description: "River Calder - Thornholme Farm to Sellafield",
        },
        {
          id: 3,
          riverguide: 2293,
          X: -3.4874053130213,
          Y: 54.431160754276,
          type: 1,
          mapid: 7,
          description: "River Calder - Thornholme Farm to Sellafield",
        },
        {
          id: 4,
          riverguide: 2293,
          X: -3.5042250694883,
          Y: 54.410549631663,
          type: 1,
          mapid: 7,
          description: "River Calder - Thornholme Farm to Sellafield",
        },
        {
          id: 5,
          riverguide: 3,
          X: -3.4299054559252,
          Y: 54.502050382119,
          type: 0,
          mapid: 2,
          description: "River Calder - Lankrigg Moss to Thornholme Farm",
        },
        {
          id: 6,
          riverguide: 3,
          X: -3.4429461661822,
          Y: 54.467615760092,
          type: 0,
          mapid: 2,
          description: "River Calder - Lankrigg Moss to Thornholme Farm",
        },
      ];
      data = [];
      if (query.guideid != null) {
        console.log('- guideid: ',query.guideid)
        for (const p of points) {
          if (p.riverguide == query.guideid) {
            data.push(p);
          }
        }
      } else if (query.type != null) {
        console.log('- type:',query.type)
        for (const p of points) {
          if (p.type == query.type) {
            data.push(p);
          }
        }
      } else if (query.radius != null) {
        console.log('- radius', query.radius, 'lat: ', query.lat, 'lng: ', query.lng)
        for (const p of points) {
          // fudge it, we are biging to need a better test server :-(
          if (p.id <= 5 ) {
            data.push(p);
          }
        }
      } else {
        console.log('- UNKNOWN', query)

      }
      res.send(data);

      break;
    default:
      console.log("Unknown task:", task);
  }
});

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`);
});
