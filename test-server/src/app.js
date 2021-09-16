const express = require('express')
const app = express()
const cors = require('cors')
const request = require('request');
const dotenv = require('dotenv')

dotenv.config()
const port = 3000
const key = process.env.APIKEY
const secret = process.env.APISECRET

app.use(cors())
app.get('/index.php', (req, res) => {

  const query = req.query
  const task = query.task
  if (query.hasOwnProperty('task')) { 
    console.log('Task:', task)
  }
  switch (task) {
    case 'authenticate':
      const osApiUrl = 'https://api.os.uk/oauth2/token/v1'

      request({
        method: 'POST',
        url: osApiUrl,
        auth: {
            username: key,
            password: secret
        },
        form: {
            grant_type: 'client_credentials'
        },
        encoding: null
      }, (error, response, buffer) => {
        if(error) {
            res.status(500).send('Failed to get access token, check the API key and secret')
        }

        const contentType = response.headers['content-type'];
        const statusCode = response.statusCode;
        res.set('Content-Type', contentType);
        res.status(statusCode);
        const client_credentials = JSON.parse(buffer.toString())
        const responce = {
          'userId' : 0,
          'accessToken' : client_credentials.access_token,
          'expiresIn' :  client_credentials.expires_in
        } 
        res.send(responce);      
      });
    
      break
    case 'mappoint':

      data = [
        {
            "id": "2",
            "riverguide": "2",
            "X": "-1.7068402332556",
            "Y": "52.184963439156",
            "type": "0",
            "description": "River Avon - Stratford on Avon Weir"
        },
        {
            "id": "3",
            "riverguide": "2",
            "X": "-1.6950409068549",
            "Y": "52.199318248033",
            "type": "0",
            "description": "River Avon - Stratford on Avon Weir"
        }
      ]
      res.send(data)

      break
    default:
      console.log('Unknown task:',task)
  }

})

app.listen(port, () => {
  console.log(`Example app listening at http://localhost:${port}`)
})