const express = require('express');
app = express();
const path = require('path');
const bodyParser = require('body-parser');
let multer=require('multer');

const cors = require('cors');

var multerS3 = require('multer-s3');
const aws = require('aws-sdk');

aws.config.update({
	secretAccessKey: '9I9u8omiUz2tHyp9hYiXYOxAE3Sa/27pfvafAqCM',
	accessKeyId: 'AKIAJTLG7MDDMDYFY3NQ',
	region: 'ap-south-1'

});
const s3 = new aws.S3();

const upload = multer({ storage: multerS3({
    s3: s3,
    bucket: 'ebutormedia-test',
    acl: 'public-read',
    metadata: function (req, file, cb) {
      cb(null, {fieldName: file.fieldname});
    },
    key: function (req, file, cb) {
      cb(null, Date.now().toString()+'.'+file.originalname.split('.')[1])
    }
  })
});

module.exports = upload;

app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({
  extended: true,
  limit: '50mb'

}));

app.use(function (req, res, next) {
  app.use(cors());

  // Website you wish to allow to connect
  res.setHeader('Access-Control-Allow-Origin', '*');

  // Request methods you wish to allow
  res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

  // Request headers you wish to allow
  res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type,x-xsrf-token');

  // Set to true if you need the website to include cookies in the requests sent
  // to the API (e.g. in case you use sessions)
  res.setHeader('Access-Control-Allow-Credentials', true);

  // Pass to next layer of middleware
  next();
});

const mysql = require('mysql');
const config = require('./config/config.json');
const db = require('./dbConnection');
const redis = require('./redisConnection');
app.use(upload.fields([]));
app.listen(config.PORT, () => {
  console.log('server is listening at ' + config.PORT);
});
app.timeout = 500000;

const bannerRoute = require('./app/routes/bannerroute');
app.use('/banner', bannerRoute);

const bannerDetailsRoute = require('./app/routes/detailsroute');
// console.log(bannerDetailsRoute.stack);
app.use('/banner', bannerDetailsRoute);

const promotionroutes = require('./app/routes/commonroute');
app.use('/', promotionroutes);

const ffmroutes = require('./app/routes/salespjproute');
app.use('/schedulepjp', ffmroutes);
