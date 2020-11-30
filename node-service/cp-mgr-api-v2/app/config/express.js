var express = require('express');
var path = require('path');
var logger = require('morgan');
var bodyParser = require('body-parser');
var cors = require('cors');
var compression = require('compression');
var upload = require('../config/s3Config')
var jwt = require('jwt-simple');

// view engine setup
module.exports = function (app, config) {
    app.use(cors());
    app.use(bodyParser.json());
    app.use(bodyParser.urlencoded({ extended: true }));
    app.use(upload.fields([{ name: 'img' }, { name: 'doc_url' }, { name: 'profile_picture' }, { name: 'feedback_pic' }, { name: 'feedback_audio' }, { name: "fssai_doc" }, { name: "gst_doc" }]));//used for s3 file upload
    app.use(compression());
    //  app.use(express.static(path.join(config.rootPath, 'public')));
}