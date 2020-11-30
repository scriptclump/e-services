const express = require('express');
app = express();
const path = require('path');
const bodyParser = require('body-parser');
const config = require("../tracking-api/config/config.json")

let multer = require('multer');
let upload = multer();


app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
  extended: true
}));

app.use(upload.fields([]));
app.listen(config.PORT, () => {
  console.log('server is listening at ' + config.PORT);
});
app.timeout = 500000;

const cproutes = require('./routes');
app.use('/', cproutes);
