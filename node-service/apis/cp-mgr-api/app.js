const express = require('express');
app = express();
require('../cp-mgr-api/config/mongoose');
const bodyParser = require('body-parser');
var compression = require('compression');
let multer = require('multer');
const multers3 = require('multer-s3');
const aws = require('aws-sdk');
const path = require('path');
const config = require('./config/config.json')
let current_datetime = new Date();
let formatted_date = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate();

aws.config.update({
  accessKeyId: config.S3AccessKeyId,
  secretAccessKey: config.S3AecretAccessKey,
  region: 'ap-south-1'
});
const s3 = new aws.S3;
const upload = multer({
  storage: multers3({
    s3: s3,
    bucket: 'ebutormedia-test',
    acl: 'public-read',
    metadata: function (req, file, cb) {
      cb(null, { fieldName: file.fieldname });
    },
    key: function (req, file, cb) {
      cb(null, Date.now().toString() + '_' + file.originalname)
    }
  })
});



app.use(compression());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
  extended: true
}));

app.use(upload.fields([{ name: 'img' }, { name: 'doc_url' }, { name: 'profile_picture' }, { name: 'gst_doc' }, { name: 'fssai_doc' }]));
app.listen(config.PORT, () => {
  console.log('server is listening at ' + config.PORT);
});


app.timeout = 500000;
const cproutes = require('./application/routes/cproutes');
app.use('/mobileapi', cproutes);

module.exports = upload;
