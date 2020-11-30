var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var bodyParser = require('body-parser');
app.use( bodyParser.json() );       // to support JSON-encoded bodies
app.use(bodyParser.urlencoded({     // to support URL-encoded bodies
  extended: true
})); 
app.get('/', function(req, res){
  //res.sendfile('index.html');
  res.send(JSON.stringify({ 'message': 'Socket running', 'status': 0 }));
});
app.post('/post', function(req, res, socket){
	console.log(req.body);
	var msg = req.body;
	io.sockets.emit('dashboard-channel', { data: msg});
	res.setHeader('Content-Type', 'application/json');
	res.send(JSON.stringify({ 'message': 'success', 'status': 0 }));
});

var clients = 0;

// io.on('connection', function(socket){
// 	 clients++;
// socket.emit('newclientconnect',{ description: 'Hey, welcome!'});
// socket.broadcast.emit('newclientconnect',{ description: clients + ' clients connected!'})
//  socket.on('disconnect', function () {
// 	       clients--;
// 		socket.broadcast.emit('newclientconnect',{ description: clients + ' clients connected!'})
//              });
// });

http.listen(3000, function(){
  console.log('listening on localhost:3022');
});