/* 
 * Connect mongo library
 * 
 * Connect mongo integration for stroing session in database.
 * 
 * @package Sleek.js
 * @version 1.0
 * @require connect-mongo, express-session, mongodb
 * 
 * The MIT License (MIT)

 * Copyright Cubet Techno Labs, Cochin (c) 2014 <info@cubettech.com>

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author Robin C Samuel <robin@cubettech.com> <@robincsamuel>
 * @Date 16-06-2014
 */
// Please add this library to config libs, in config.js. example global.sleekConfig.configLibs = ['session'];

var session = require('express-session');
var path = require('path');
var MongoStore = require('connect-mongo')(session);
var config = require(path.join(appPath, 'application/config','mongodb.js'));

//remove existing session middleware
for(var i =0; i < app._router.stack.length; i++) {
  if (app._router.stack[i].handle.name === 'session') {
      app._router.stack.splice(i, 1);
  }
}
 
//register new middleware
app.use(session({
    secret: 'CubetSleek',
    store: new MongoStore({
      url : 'mongodb://'+ (config.dbHost ? config.dbHost : 'localhost') + ':'+(config.dbPort ? config.dbPort : '27017') +'/' + config.dbName
    }),
    saveUninitialized: true,
    resave: true
}));