var sequelize = require('../../../config/sequelize');//sequlize connection file
var moment = require('moment');//used to return date in required format
const Sequelize = require('sequelize');//sequelize reference
const _ = require('underscore');//used to get specific feild from an array
const cache = require('../../../config/redis');//used to get access to cache
const mongoConnection = require('../../../config/mongodb');//used to get mongoConnection instance

module.exports.getDetails = function (le_wh_id,segment_id,offset_limit,offset,customer_type,manufacture,brand,state) {
     return new Promise((resolve, reject) => {
          try {
               let bu_state_id = state;
               let status = 1;
               let final_response = [];
                    query = "CALL getCPCategories_ByCust(" + le_wh_id + ',' + segment_id + ',' + offset_limit + ',' + offset  + ',' + customer_type + ',' + state  + ',' + bu_state_id + ")";
                    sequelize.query(query).then(result => {
                         let categoryArray = _.pluck(result, 'id');
                         let response = JSON.parse(JSON.stringify(result[0]));
                         final_response.push({type:'CircleList',item_type:'Category',key:'category_id',list:[response]});
                              query2 = "CALL getCPManufactuers_ByCust(" + le_wh_id + ',' + segment_id + ',' + offset_limit + ',' + offset  + ',' + manufacture + ',' + customer_type + ',' + state  + ',' + bu_state_id + ")";
                              sequelize.query(query2).then(result => {
                                   let response2 = JSON.parse(JSON.stringify(result[0]));
                                   final_response.push({type:'RectangleList',item_type:'Manufacturer',key:'manufacturer_id',list:[response2]});
                                        query3 = "CALL getCPBrands_ByCust(" + le_wh_id + ',' + segment_id + ',' + offset_limit + ',' + offset  + ',' + brand + ',' + customer_type + ',' + state  + ',' + bu_state_id + ")";
                                        sequelize.query(query3).then(result => {
                                             let response3 = JSON.parse(JSON.stringify(result[0]));
                                             final_response.push({type:'RectangleGrid',item_type:'Brand',key:'brand_id',list:[response3]});
                                             query4 = "select product_id,primary_image from products where category_id IN(" + categoryArray + ")";
                                             sequelize.query(query4).then(result => {
                                                  let response4 = JSON.parse(JSON.stringify(result[0]));
                                                  final_response.push({type:'VerticalProductList',item_type:'Product',key:'product_id',list:[response4][0]});
                                                  let curDate = moment().format("YYYY-MM-DD"),
                                                  query5 = "select banner_id,banner_url,navigator_objects as image from banner where le_wh_id =" + le_wh_id + " and is_active =" + status+ " and " +curDate + " between  from_date and to_date";
                                                  sequelize.query(query5).then(result => {
                                                       let response5 = JSON.parse(JSON.stringify(result[0]));
                                                       final_response.push({type:'SliderWidget',item_type:'Banners',key:'banner_id',list:[response5][0]});
                                                       final_response.push({type:'Space',value:'10'});
                                                       final_response.push({type: "Header",title: "Shop By Brands",text_color: "#FF0000",bg_color: "#ff00ff"});
                                                       resolve(final_response);
                                                  }).catch(err => {
                                                       console.log(err);
                                                       final_response.push({type:'Error',item_type:'Error',key:'Error',list:[]});
                                                       resolve(final_response);
                                                  })
                                             }).catch(err => {
                                                  console.log(err);
                                                  final_response.push({type:'Error',item_type:'Error',key:'Error',list:[]});
                                                  resolve(final_response);
                                             })
                                        }).catch(err => {
                                             console.log(err);
                                             final_response.push({type:'Error',item_type:'Error',key:'Error',list:[]});
                                             resolve(final_response);
                                        })
                              }).catch(err => {
                                   console.log(err);
                                   final_response.push({type:'Error',item_type:'Error',key:'Error',list:[]});
                                   resolve(final_response);
                              })
                    }).catch(err => {
                         console.log(err);
                         final_response.push({type:'Error',item_type:'Error',key:'Error',list:[]});
                         resolve(final_response);
                    })

          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}
module.exports.getState = function (le_wh_id) {
     return new Promise((resolve, reject) => {
          try {
               let query;
               let seller_state = {};
               query = "select state from legalentity_warehouses where le_wh_id =" + le_wh_id ;
               sequelize.query(query).then(response => {
                    seller_state = response[0][0].state;
                    resolve(seller_state);
               }).catch(err => {
                    console.log(err);
                    reject(err);
               })
          } catch (err) {
               console.log(err);
               reject(err);
          }
     })
}