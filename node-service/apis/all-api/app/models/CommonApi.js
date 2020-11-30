/**
 * comonapi.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {
  getStockistDashboardData:function(data,callback){
   var da = JSON.parse(data);  
    var user_id = (da.user_id) ? da.user_id : 0;
    var flag = (da.flag) ? da.flag : 2;
    var from_date = da.from_date;
    var to_date = da.to_date;
    var le_id ='NULL';
    var manuf_id=(da.manuf_id) ? da.manuf_id : 'NULL';
    var brand_id= (da.brand_id)? da.brand_id : 'NULL';
    var product_grup = (da.product_grup) ? da.product_grup : 'NULL';
    var cat_id = (da.cat_id) ? da.cat_id : 'NULL';
    //console.log('aaa');
    module.exports.getBusinessUnits(user_id,da).then(res=>{
     // console.log('666666666666666');
           //console.log(res,"aaaaaaaaaaaaaaa");
      var bu_id=res;
      //console.log('le_id',le_id);
      //console.log('from_date',from_date);
      //console.log('to_date',to_date);
      //console.log('flag',flag);
      //console.log('brand_id',brand_id);
      //console.log('manuf_id',manuf_id);
      //console.log('product_grup',product_grup);
      //console.log('bu_id',bu_id);
      //console.log('cat_id',cat_id);
      var sql = "CALL getStockistDashboardByBU_web("+le_id+",'"+from_date+"','"+to_date+"',"+flag+","+brand_id+","+manuf_id+","+product_grup+","+bu_id+","+cat_id+")";
      //console.log(sql);
        db.query(sql,{},function (err, result) {
            if (err) {
                console.log(err);
                return err;
            }
            if(result.fieldCount != 0){
              callback(result[0][0].Stockist_Dashboard);
            }else{
              callback("No data found");
            }          
        });

    }).catch(err=>{
      console.log('err',err);


    });
  },
 
 getStockistDetailsdata:function(data,callback){
  var da = JSON.parse(data);  
    var from_date = da.from_date;
    var to_date = da.to_date;

     var sql = "select * from vw_stockist_retailer_orders where Order_Date between '"+from_date+"' and '"+to_date+"'";
        db.query(sql,{},function (err, result) {
            if (err) {
                console.log(err);
                return err;
            }
            callback(result);

        });
  },

    getHrmsDashboarddata:function(data,callback){
  var da = JSON.parse(data);  
    var user_id = da.user_id;
    var sql = "SELECT e.emp_code FROM users u JOIN employee e ON u.emp_id = e.emp_id WHERE u.user_id= ? ";

    db.query(sql,[user_id],function (err, result) {
            if (err) {
                console.log(err);
                return err;
            }else{
    if(result[0]){
    var sql = "CALL get_employeeDynamicDashboard('"+result[0].emp_code+"')";
      db.query(sql,{},function (err, result) {
            if (err) {
                console.log(err);
                return err;
            }
            console.log(result);
            callback(result[0][0].Emp_Dashboard);

        });
    }else{
      callback("No data found");
    }

            }
            
        })
  },


  getBusinessUnits:function(user_id,da){
      //console.log('222222222222222222222222');
      return new Promise((resolve,reject)=>{
        if(da.bu_id){
          var bu_id=da.bu_id;
          resolve(bu_id);
        }else{
           var get_buids="select object_id from user_permssion where user_id="+user_id+" and permission_level_id=6 order by object_id asc";
           //console.log('3333333333333333333333333333333');
           db.query(get_buids,{},function (err,result){
            if(err) {
              console.log(err);
            }
             //console.log(result);
            if(result.length!=0){
              //console.log('44444444444444444444444');
              if(result[0].object_id==0){
                module.exports.getParentBusinessUnit(result[0].object_id).then(data=>{
                  //console.log('reddyyyyyyyyyyyyyyyyyyy'+data);
                   resolve(data);
                }).catch(err=>{
                  console.log('err',err);


                });          
              }else{
                var bu_id=result[0].object_id;
                 resolve(bu_id);
              }
            }else{
              resolve('');
            }
           });
        }
    });
  },

  getParentBusinessUnit:function(parentbuid){

   return new Promise((resolve,reject)=>{
    //console.log('55555555555555555555555');

     var get_costcenter_id="select bu_id from business_units where parent_bu_id="+parentbuid;     

            db.query(get_costcenter_id,{}, function (err,result2){

              if(err) {
                console.log(err);
              }
              var bu_id=result2[0].bu_id;
              //console.log(bu_id+'nasakda');
              resolve(bu_id);
            });

    });
  }


  
};

