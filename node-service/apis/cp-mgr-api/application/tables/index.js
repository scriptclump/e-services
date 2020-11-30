const config= require('../../config/config.json');
const mysql=require('mysql');
var fs = require("fs");
var path = require("path");

const Sequelize = require('sequelize');
var sequelize = new Sequelize(config['database'],config['username'],config['password'],{
	host:config['host'],
	port:'3306',
	dialect: 'mysql',
  define: {
        timestamps: false
  }
});
var db = {};
 
 
fs.readdirSync(__dirname)
    .filter(function(file) {
        return (file.indexOf(".") !== 0) && (file !== "index.js");
    })
    .forEach(function(file) {
        var model = sequelize.import(path.join(__dirname, file));
        db[model.name] = model;
    });
 
Object.keys(db).forEach(function(modelName) {
    if ("associate" in db[modelName]) {
        db[modelName].associate(db);
    }
});
 
 
db.sequelize = sequelize;
db.Sequelize = Sequelize;
 
module.exports = db;

//console.log('SQQQ',sequelize);

/*const connection = mysql.createConnection({
  host: config['host'],
  user: config['username'],
  password: config['password'],
  database: config['database']
});

connection.connect(function(err) {
  if (err) throw err
  console.log('You are now connected...')
})
*/
//module.exports=sequelize;