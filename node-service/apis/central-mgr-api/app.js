//require our needs
var express = require('express'),
http = require('http'),
path = require('path'),
exphbs  = require('express3-handlebars'),
helmet  = require('helmet'),
cookieParser = require('cookie-parser'),
session      = require('express-session'),
favicon = require('serve-favicon'),
json        = require('json'),
urlencoded   = require('urlencode'),
bodyParser = require('body-parser'),
methodOverride = require('method-override');
global.app = express();

global.sleekConfig = {};
require(path.join(__dirname,'application/config/config.js'));
   
app.set('env', sleekConfig.env);
app.set('x-powered-by', 'Sleek.js');
// all environments
app.set('port', process.env.PORT || sleekConfig.appPort);
app.set('host', sleekConfig.appHost ? sleekConfig.appHost : 'localhost');
app.set('views', path.join(__dirname, 'application/views'));
app.set('view engine', 'handlebars');
app.engine('html',  exphbs({defaultLayout: 'default',
                            layoutsDir: path.join(__dirname, 'application/layouts/'), extname:".html"})
            ); 
app.use(favicon(path.join(__dirname, 'public/favicon.ico'))); 

app.use(bodyParser());
app.use(function(err,req,res,next){
if(err){
    console.log("Input Data Error:"+err);
}
next();
});
app.use(bodyParser.urlencoded({
  extended: true
}));
app.use(bodyParser.json());
app.use(helmet.xframe());
app.use(helmet.iexss());
app.use(helmet.contentTypeOptions());
app.use(helmet.cacheControl());
app.use(methodOverride());
app.use(cookieParser());
app.use(session({secret: 'CubEtNoDeSlEek', 
                 saveUninitialized: true,
                 resave: true}));
app.use(express.static(path.join(__dirname, 'public')));


// Middleware added to check the Header Authentication
/*var headerAuthCheck = function (req, res, next) {
  
 // check the Header Authentication here
  var api_key = req.headers.api_key;
  var api_secret = req.headers.api_secret;

  console.log('-------------------------------------------------');

 global.clientIPAddress = req.headers['x-forwarded-for'] || 
     req.connection.remoteAddress || 
     req.socket.remoteAddress ||
     req.connection.socket.remoteAddress;

    // check for headers
    if(!api_key || !api_secret){
     console.log(clientIPAddress + " : Bad Request received.");
     res.json(sleekConfig.badRequest);
    }else{
	
		console.log(clientIPAddress + " : Checking Authentication..");
     
		var collection = mongodb.collection('lp_apiusers');
            // Locate all the entries using find
		collection.find({"api_key":api_key, "api_secret":api_secret}).toArray(function(err, results) {

         	var results = JSON.stringify(results);
         	results = results.substring(1, results .length-1);

         	if(results!='') {
          		console.log(clientIPAddress + "Authentication succeded..");
          		next();
         	}else{
          		console.log(clientIPAddress + "Authentication failed..");
          		res.json(sleekConfig.unAuthorized);
         	}
        });
    }
};
app.use(headerAuthCheck);*/


app.set('strict routing');

//set Site url
global.sleekConfig.siteUrl = 'http://'+app.get('host')+':'+app.get('port');
//get configs
require('./system/core/sleek.js')(app);
// development only
if ('development' === app.get('env')) {
   
} 

var server = http.createServer(app);
try {
    server.listen(app.get('port'),app.get('host'), function(){
      console.log('server listening on port ' + sleekConfig.siteUrl);
    });
} catch (e) {
    system.log(e);
}
