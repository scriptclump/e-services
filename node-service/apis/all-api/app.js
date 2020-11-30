const express = require('express');
app = express();
const path = require('path');
const bodyParser = require('body-parser');

let multer = require('multer');

const multers3 = require('multer-s3');
const aws = require('aws-sdk');
const config = require('../all-api/config/config.json');

aws.config.update({
  accessKeyId: config.S3AccessKeyId,
  secretAccessKey: config.S3AecretAccessKey,
  region: 'ap-south-1'
});
const s3 = new aws.S3;

const upload = multer({
  storage: multers3({
    s3: s3,
    bucket: config.S3BucketName,
    acl: 'public-read',
    metadata: function (req, file, cb) {
      cb(null, { fieldName: file.fieldname });
    },
    key: function (req, file, cb) {

      cb(null, Date.now().toString() + path.extname(file.originalname))
    }

  })

});

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({
  extended: true
}));

app.use(upload.fields([{ name: 'logo[]' }, { name: 'upload_file' }]));

app.listen(config.PORT, () => {
  console.log('server is listening at ' + config.PORT);
});
app.timeout = 500000;
const cproutes = require('./routes');
app.use('/', cproutes);

module.exports = upload;
