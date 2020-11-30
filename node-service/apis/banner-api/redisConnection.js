const redis=require('redis');
const config = require('./config/config.json');

redisClient = redis.createClient({host : config['REDIS_HOST'], port : config['REDIS_PORT'], password: config['REDIS_PASSWORD']});
redisClient.on('error',(err)=>{
	console.log('error');
});
redisClient.on('connect',(err,res)=>{
	console.log('connected to redis');
});

module.exports=redisClient;