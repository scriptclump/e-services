const categoryModel = require('../model/categoryModel');
const cpMessage = require('../../../config/cpMessage');
var cache = require('../../../config/redis');//rediscache connection file 
const _ = require('underscore');//used to get specific feild from an array


/*
Purpose : getcategories is used to get product details based on brand , manufactures .If we won,t send any of categories then it will result all available products.
author : Deepak Tiwari
Request : Require user_id
Response : Returns productDetails,
*/
module.exports.getCategories = function (req, res) {
     console.log("hello")
     try {
          let beatId = 0;
          let appKeyData = process.env.DATABASE_NAME;
          let cachekeyString = appKeyData;
          let sortId;
          let le_wh_id;
          let customerType;
          let segmentId;
          let brandId;
          let offset;
          let offsetLimit;
          let customerToken;
          let cacheProductList = [];
          let Cache_Time = 60;
          let productId;
          let manufacturerId;
          let cacheResponse;
          let categoryId;
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               //sortId using for sorting purpose
               if (typeof data.sort_id != 'undefined' && data.sort_id != '') {
                    sortId = data.sort_id;
               } else {
                    sortId = "";
               }
               //le_wh_id
               if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
                    le_wh_id = data.le_wh_id;
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
               }
               //customertype
               customerType = typeof data.customer_type != 'undefined' ? data.customer_type : '';
               cachekeyString = cachekeyString.concat('_' + customerType + 'le_wh_id' + le_wh_id);
               if (typeof data.segment_id != 'undefined' && data.segment_id != '') {
                    segmentId = data.segment_id;
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptySegmentId })
               }

               //block of code is used to get product related to brands
               if (typeof data.brand_id != 'undefined' && data.brand_id != '') {
                    //validating brand id
                    categoryModel.checkBrandId(data.brand_id).then(checkBrandId => {
                         if (checkBrandId < 1) {
                              res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidBrandId });
                         } else {
                              //valid brandId
                              brandId = data.brand_id;
                              //offset
                              if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
                                   offset = data.offset;
                              } else {
                                   res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
                              }
                              //offsetlimit
                              if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
                                   offsetLimit = data.offset_limit;
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
                              }
                              //customer token
                              if (typeof data.customer_token != 'undefined') {
                                   categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                                        console.log("checkcustomertoken", checkCustomerToken);
                                        if (checkCustomerToken > 0) {
                                             customerToken = data.customer_token;
                                             categoryModel.getBeatByUserId(customerToken).then(beat => {
                                                  console.log("=====>76 beatInfo", beat);
                                                  if (beat == '' || beat == [] || beat == null) {
                                                       res.status(200).json({ 'status': 'failed', 'message': cpMessage.NoBeatAssign });
                                                  } else {
                                                       console.log("====>normal beat", beat);
                                                       beatId = beat.split("'");
                                                       console.log("======>After split", beatId);
                                                       beatId = beatId.replace(',', '_')
                                                       console.log("=====>replace beat ", beatId);
                                                       if (brandId != '') {
                                                            if (sortId == '') {
                                                                 sortId = -1
                                                            }
                                                            let keyString = cachekeyString.concat('getbrands');
                                                            console.log('keystring', keyString);
                                                            cache.get(keyString, async function (error, response) {
                                                                 if (error) {
                                                                      console.log(error);
                                                                 } else if (typeof response != 'undefined' && response != '') {
                                                                      cacheProductList = await (response != '' ? JSON.parse(JSON.stringify(response)) : []);
                                                                 }
                                                                 categoryModel.getBlockedData(customerToken).then(blockList => {
                                                                      console.log("======>101 blockList", blockList);
                                                                      categoryModel.getProductIdsList(brandId, offsetLimit, offset, flag = 1, blockList, customerType, le_wh_id).then(productData => {
                                                                           console.log("=====>102 prodcutid", productData);
                                                                           if (productData != '' && typeof productData[0] != 'undefined') {
                                                                                productData = productData[0];
                                                                           }
                                                                           console.log("productData", productData);
                                                                           //cache 
                                                                           cacheProductList[beatId][brandId][offset] = productData;
                                                                           console.log("cacheProductList===> 109", cacheProductList);
                                                                           cache.set(keyString, cacheProductList, Cache_Time, async function (err, rows) {
                                                                                if (err) {
                                                                                     console.log(err);
                                                                                     res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                                } else {
                                                                                     console.log("<===> 116", rows);
                                                                                }
                                                                           })
                                                                           console.log("<====120====>", productData)
                                                                           if (productData != '') {
                                                                                if (typeof productData != 'object') {
                                                                                     productId = productData.product_id;
                                                                                } else {
                                                                                     productId = productData.product_id;
                                                                                }
                                                                                //productId
                                                                                if (productId) {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'Brand Products', ' data': productData })
                                                                                } else {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'Brand Products' })
                                                                                }

                                                                           } else {
                                                                                res.status(200).json({ 'status': 'success', 'success': 'Brand Products' })
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                           res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                      })
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                 })

                                                            })

                                                       }
                                                  }
                                             }).catch(err => {
                                                  console.log("=====>78", err);
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             })
                                        } else {
                                             customerToken = "";
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })

                              } else {
                                   customerToken = "";
                              }


                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': "failed", "message": cpMessage.internalCatch });
                    })
               }

               // to get manufacturer products
               if (typeof data.manufacturer_id != 'undefined' && data.manufacturer_id != '') {
                    //valid brandId
                    manufacturerId = data.manufacturer_id;
                    //offset
                    if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
                         offset = data.offset;
                    } else {
                         res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
                    }
                    //offsetlimit
                    if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
                         offsetLimit = data.offset_limit;
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
                    }
                    //customer token
                    if (typeof data.customer_token != 'undefined') {
                         categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                              console.log("checkcustomertoken", checkCustomerToken);
                              if (checkCustomerToken > 0) {
                                   customerToken = data.customer_token;
                                   categoryModel.getBeatByUserId(customerToken).then(beat => {
                                        console.log("=====>76 beatInfo", beat);
                                        if (beat == '' || beat == [] || beat == null) {
                                             res.status(200).json({ 'status': 'failed', 'message': cpMessage.NoBeatAssign });
                                        } else {
                                             console.log("====>normal beat", beat);
                                             beatId = beat.split("'");
                                             console.log("======>After split", beatId);
                                             beatId = beatId.replace(',', '_')
                                             console.log("=====>replace beat ", beatId);
                                             if (manufacturerId != '') {
                                                  if (sortId == '') {
                                                       sortId = -1
                                                  }
                                                  let keyString = cachekeyString.concat('getmanufacturers');
                                                  console.log('keystring', keyString);
                                                  cache.get(keyString, async function (error, response) {
                                                       if (error) {
                                                            console.log(error);
                                                       } else if (typeof response != 'undefined' && response != '') {
                                                            cacheProductList = await (response != '' ? JSON.parse(JSON.stringify(response)) : []);
                                                       }
                                                       categoryModel.getBlockedData(customerToken).then(blockList => {
                                                            console.log("======>101 blockList", blockList);
                                                            categoryModel.getProductIdsList(manufacturerId, offsetLimit, offset, flag = 1, blockList, customerType, le_wh_id).then(productData => {
                                                                 console.log("=====>102 prodcutid", productData);
                                                                 if (productData != '' && typeof productData[0] != 'undefined') {
                                                                      productData = productData[0];
                                                                 }
                                                                 console.log("productData", productData);
                                                                 //cache 
                                                                 cacheProductList[beatId][manufacturerId][offset] = productData;
                                                                 console.log("cacheProductList===> 109", cacheProductList);
                                                                 cache.set(keyString, cacheProductList, Cache_Time, async function (err, rows) {
                                                                      if (err) {
                                                                           console.log(err);
                                                                           res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                      } else {
                                                                           console.log("<===> 116", rows);
                                                                      }
                                                                 })
                                                                 console.log("<====120====>", productData)
                                                                 if (productData != '') {
                                                                      if (typeof productData != 'object') {
                                                                           productId = productData.product_id;
                                                                      } else {
                                                                           productId = productData.product_id;
                                                                      }
                                                                      //productId
                                                                      if (productId) {
                                                                           res.status(200).json({ 'status': 'success', 'success': 'Manufacturer Product', ' data': productData })
                                                                      } else {
                                                                           res.status(200).json({ 'status': 'success', 'success': 'Manufacturer Product' })
                                                                      }

                                                                 } else {
                                                                      res.status(200).json({ 'status': 'success', 'success': 'Brand Products' })
                                                                 }
                                                            }).catch(err => {
                                                                 console.log(err);
                                                                 res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                            })
                                                       }).catch(err => {
                                                            console.log(err);
                                                            res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                       })

                                                  })

                                             }
                                        }
                                   }).catch(err => {
                                        console.log("=====>78", err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })
                              } else {
                                   customerToken = "";
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         })

                    } else {
                         customerToken = "";
                    }

               }

               //used to getCategories details
               if (typeof data.category_id != 'undefined' && data.category_id != '') {
                    categoryModel.checkCategoryId(data.category_id).then(checkCategoryId => {//invalid category id 
                         if (checkCategoryId < 1) {
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidCategoryId })
                         } else {
                              categoryId = data.category_id
                              //offset
                              if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
                                   offset = data.offset;
                              } else {
                                   res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
                              }
                              //offsetlimit
                              if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
                                   offsetLimit = data.offset_limit;
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
                              }
                              //customer token
                              if (typeof data.customer_token != 'undefined') {
                                   categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                                        console.log("checkcustomertoken", checkCustomerToken);
                                        if (checkCustomerToken > 0) {
                                             customerToken = data.customer_token;
                                             categoryModel.getBeatByUserId(customerToken).then(beat => {//fetching beatId based on customer_token
                                                  console.log("=====>314 beatInfo", beat);
                                                  if (beat == '' || beat == [] || beat == null) {
                                                       res.status(200).json({ 'status': 'failed', 'message': cpMessage.NoBeatAssign });
                                                  } else {
                                                       console.log("====>normal 319 beat", beat);
                                                       beatId = beat.split("'");
                                                       console.log("======>After 320split", beatId);
                                                       beatId = beatId[0].replace(',', '_')
                                                       console.log("=====>replace 322 beat ", beatId);
                                                       if (categoryId != '') {//categoryId is not equal to null
                                                            if (sortId == '') {
                                                                 sortId = -1
                                                            }
                                                            let keyString = cachekeyString.concat('getcategories');
                                                            console.log('keystring', keyString);
                                                            cache.get(keyString, async function (error, response) {
                                                                 if (error) {
                                                                      console.log(error);
                                                                 } else if (typeof response != 'undefined' && response != '') {
                                                                      cacheProductList = await (response != '' ? JSON.parse(JSON.stringify(response)) : []);
                                                                 }
                                                                 console.log("=====>336", cacheProductList)
                                                                 categoryModel.getBlockedData(customerToken).then(blockList => {
                                                                      console.log("======>336 blockList", blockList);
                                                                      categoryModel.getProductIdsList(categoryId, offsetLimit, offset, flag = 1, blockList, customerType, le_wh_id).then(productData => {
                                                                           console.log("=====>338 productId", productData);
                                                                           if (productData != '' && typeof productData[0] != 'undefined') {
                                                                                productData = productData[0];
                                                                           }
                                                                           console.log("productData", productData, beatId, categoryId, offset);
                                                                           //cache 
                                                                           cacheProductList = { beatId: JSON.stringify(productData), categoryId: JSON.stringify(productData), offset: JSON.stringify(productData) }
                                                                           //   cacheProductList = [[beatId][categoryId][offset]].push(productData)
                                                                           //cacheProductList[[beatId]].push(productData)
                                                                           //  cacheProductList[[beatId]].push(productData);
                                                                           console.log("cacheProductList===> 345", cacheProductList);
                                                                           cache.set(keyString, Object.entries(cacheProductList), Cache_Time, async function (err, rows) {
                                                                                if (err) {
                                                                                     console.log(err);
                                                                                     res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                                } else {
                                                                                     console.log("<===> 351", rows);
                                                                                }
                                                                           })
                                                                           console.log("<====354====>", productData)
                                                                           if (productData != '') {
                                                                                if (typeof productData != 'object') {
                                                                                     productId = productData.product_id;
                                                                                } else {
                                                                                     productId = productData.product_id;
                                                                                }
                                                                                //productId
                                                                                if (productId) {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'category Product', ' data': { 'product_id': productData.product_id, 'count': productData.count, 'is_subcategory': '-1' } })
                                                                                } else {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'category product' })
                                                                                }
                                                                           } else {
                                                                                res.status(200).json({ 'status': 'success', 'success': 'category Products' })
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                           res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                      })
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                 })

                                                            })

                                                       } else {
                                                            let splitleWhId = le_wh_id.split("'");
                                                            keyString = 'categories_' + segmentId + 'le_wh_id' + splitleWhId;
                                                            console.log('keystring', keyString);
                                                            cache.get(keyString, function (err, result) {
                                                                 if (err) {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                 } else {
                                                                      console.log("result===>389", result);
                                                                      if (result != '') {
                                                                           cacheResponse = result;
                                                                      } else {
                                                                           //if we got empty response from cache
                                                                           categoryModel.getCategories(segmentId, le_wh_id).then(categories => {
                                                                                console.log("====>396 categories", categories);
                                                                                cacheResponse = categories;
                                                                                cache.set(keyString, cacheResponse, Cache_Time, function (err, rows) {
                                                                                     if (err) {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                                     } else {
                                                                                          console.log("cacheResponse", rows);
                                                                                     }
                                                                                })
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                           })
                                                                      }
                                                                 }
                                                            })
                                                            res.status(200).json({ 'status': 'success', 'message': 'getCategories' });
                                                       }
                                                  }
                                             }).catch(err => {
                                                  console.log("=====>385", err);
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             })
                                        } else {
                                             customerToken = "";
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })

                              } else {
                                   customerToken = "";
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               } else {
                    category_id = '';
                    categoryModel.checkCategoryId(data.category_id).then(checkCategoryId => {//invalid category id 
                         if (checkCategoryId < 1) {
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidCategoryId })
                         } else {
                              categoryId = data.category_id
                              //offset
                              if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
                                   offset = data.offset;
                              } else {
                                   res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
                              }
                              //offsetlimit
                              if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
                                   offsetLimit = data.offset_limit;
                              } else {
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
                              }
                              //customer token
                              if (typeof data.customer_token != 'undefined') {
                                   categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                                        console.log("checkcustomertoken", checkCustomerToken);
                                        if (checkCustomerToken > 0) {
                                             customerToken = data.customer_token;
                                             categoryModel.getBeatByUserId(customerToken).then(beat => {//fetching beatId based on customer_token
                                                  console.log("=====>314 beatInfo", beat);
                                                  if (beat == '' || beat == [] || beat == null) {
                                                       res.status(200).json({ 'status': 'failed', 'message': cpMessage.NoBeatAssign });
                                                  } else {
                                                       console.log("====>normal 319 beat", beat);
                                                       beatId = beat.split("'");
                                                       console.log("======>After 320split", beatId);
                                                       beatId = beatId.replace(',', '_')
                                                       console.log("=====>replace 322 beat ", beatId);
                                                       if (categoryId != '') {//categoryId is not equal to null
                                                            if (sortId == '') {
                                                                 sortId = -1
                                                            }
                                                            let keyString = cachekeyString.concat('getcategories');
                                                            console.log('keystring', keyString);
                                                            cache.get(keyString, async function (error, response) {
                                                                 if (error) {
                                                                      console.log(error);
                                                                 } else if (typeof response != 'undefined' && response != '') {
                                                                      cacheProductList = await (response != '' ? JSON.parse(JSON.stringify(response)) : []);
                                                                 }
                                                                 categoryModel.getBlockedData(customerToken).then(blockList => {
                                                                      console.log("======>336 blockList", blockList);
                                                                      categoryModel.getProductIdsList(categoryId, offsetLimit, offset, flag = 1, blockList, customerType, le_wh_id).then(productData => {
                                                                           console.log("=====>338 prodcutid", productData);
                                                                           if (productData != '' && typeof productData[0] != 'undefined') {
                                                                                productData = productData[0];
                                                                           }
                                                                           console.log("productData", productData);
                                                                           //cache 
                                                                           cacheProductList[beatId][categoryId][offset] = productData;
                                                                           console.log("cacheProductList===> 345", cacheProductList);
                                                                           cache.set(keyString, cacheProductList, Cache_Time, async function (err, rows) {
                                                                                if (err) {
                                                                                     console.log(err);
                                                                                     res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                                } else {
                                                                                     console.log("<===> 351", rows);
                                                                                }
                                                                           })
                                                                           console.log("<====354====>", productData)
                                                                           if (productData != '') {
                                                                                if (typeof productData != 'object') {
                                                                                     productId = productData.product_id;
                                                                                } else {
                                                                                     productId = productData.product_id;
                                                                                }
                                                                                //productId
                                                                                if (productId) {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'category Product', ' data': { 'product_id': productData.product_id, 'count': productData.count, 'is_subcategory': '-1' } })
                                                                                } else {
                                                                                     res.status(200).json({ 'status': 'success', 'success': 'category product' })
                                                                                }
                                                                           } else {
                                                                                res.status(200).json({ 'status': 'success', 'success': 'category Products' })
                                                                           }
                                                                      }).catch(err => {
                                                                           console.log(err);
                                                                           res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                      })
                                                                 }).catch(err => {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                 })

                                                            })

                                                       } else {
                                                            let splitleWhId = le_wh_id.split("'");
                                                            keyString = 'categories_' + segmentId + 'le_wh_id' + splitleWhId;
                                                            console.log('keystring', keyString);
                                                            cache.get(keyString, function (err, result) {
                                                                 if (err) {
                                                                      console.log(err);
                                                                      res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                 } else {
                                                                      console.log("result===>389", result);
                                                                      if (result != '') {
                                                                           cacheResponse = result;
                                                                      } else {
                                                                           //if we got empty response from cache
                                                                           categoryModel.getCategories(segmentId, le_wh_id).then(categories => {
                                                                                console.log("====>396 categories", categories);
                                                                                cacheResponse = categories;
                                                                                cache.set(keyString, cacheResponse, Cache_Time, function (err, rows) {
                                                                                     if (err) {
                                                                                          console.log(err);
                                                                                          res.status(200).json({ 'status': 'failed', 'message': cpMessage.cacheIsuuses });
                                                                                     } else {
                                                                                          console.log("cacheResponse", rows);
                                                                                     }
                                                                                })
                                                                           }).catch(err => {
                                                                                console.log(err);
                                                                                res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                                                           })
                                                                      }
                                                                 }
                                                            })
                                                            res.status(200).json({ 'status': 'success', 'message': 'getCategories' });
                                                       }
                                                  }
                                             }).catch(err => {
                                                  console.log("=====>385", err);
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             })
                                        } else {
                                             customerToken = "";
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })

                              } else {
                                   customerToken = "";
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
          }
     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}


/*
Purpose : productDetails function is used to handle the request of getting the product related data.
author : Deepak Tiwari
Request : Require user_id
Response : Returns productDetails,
*/
module.exports.productDetails = function (req, res) {
     try {
          if (typeof req.body.data != 'undefined') {
               let data = JSON.parse(req.body.data);
               let segmentId;
               let le_wh_id;
               let productId;
               let customerToken;
               //segmentid
               if (typeof data.segment_id != 'undefined' && data.segment_id != '') {
                    segmentId = data.segment_id;
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptySegmentId })
               }
               //le_wh_id
               if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
                    le_wh_id = data.le_wh_id;
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
               }
               //product id
               if (typeof data.product_id != 'undefined' && data.product_id != '') {
                    categoryModel.checkProductId(data.product_id).then(checkProductId => {
                         if (checkProductId < 1) {
                              res.status(200).json({ 'status': "failed", 'message': cpMessage.InvalidProductId });
                         } else {
                              productId = data.product_id;
                              //customer token
                              if (typeof data.customer_token != 'undefined') {
                                   categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                                        console.log("checkcustomertoken ==> 636", checkCustomerToken);
                                        if (checkCustomerToken > 0) {
                                             customerToken = data.customer_token;
                                             if (productId) {
                                                  categoryModel.getProductDetails(productId, offset = '', offset_limit = '', sort_id = '', customerToken, api = 2, prodData, le_wh_id, segmentId).then(productData => {//model is not yet completed
                                                       res.status(200).json({ "status": "success", "message": "product details", "data": productData })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             }
                                        } else {
                                             customerToken = "";
                                             if (productId) {
                                                  categoryModel.getProductDetails(productId, offset = '', offset_limit = '', sort_id = '', customerToken, api = 2, prodData, le_wh_id, segmentId).then(productData => {//model is not yet completed
                                                       res.status(200).json({ "status": "success", "message": "product details", "data": productData })
                                                  }).catch(err => {
                                                       console.log(err);
                                                  })
                                             }
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                   })
                              } else {
                                   customerToken = "";
                                   if (productId) {
                                        categoryModel.getProductDetails(productId, offset = '', offset_limit = '', sort_id = '', customerToken, api = 2, prodData, le_wh_id, segmentId).then(productData => {
                                             res.status(200).json({ "status": "success", "message": "product details", "data": productData })
                                        }).catch(err => {
                                             console.log(err);
                                        })
                                   }
                              }
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               } else {
                    res.status(200).json({ 'status': "failed", 'message': cpMessage.EmptyProductId });
               }
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.invalidRequestBody });
          }

     } catch (err) {
          console.log(err);
          res.status(200).json({ 'status': "failed", 'message': cpMessage.serverError });
     }

}

/*
Purpose : productDetails function is used to handle the request of getting the product related data.
author : Deepak Tiwari
Request : Require user_id
Response : Returns productDetails,
*/
module.exports.addReviewRating = function (req, res) {
     try {
          let data;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }
          //customerToken validation
          if (typeof data.customer_token != 'undefined') {
               categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    if (checkCustomerToken > 0) {//valid token
                         categoryModel.getUserId(data.customer_token).then(customerId => {
                              categoryModel.addReviewRating(customerId.user_id, data, customerId.firstname, customerId.lastname).then(result => {
                                   if (result.status == 1) {
                                        res.status(200).json({ 'status': 'success', 'message': cpMessage.addReviewRating, 'data': result })
                                   } else {
                                        res.status(200).json({ 'status': 'failed', 'message': cpMessage.alreadyRated })
                                   }
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         })
                    } else {
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidToken });
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
               })
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.tokenNotPassed });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
Purpose : getMediaDesc function is used to get multiple product details based on product_id.
author : Deepak Tiwari
Request : Require customer_token , product_id , le_Wh_id, pincode
Response : Returns product image  ,description.
*/
module.exports.getMediaDesc = function (req, res) {
     try {
          let data;
          let productId;
          let getMedia = {};
          let getDescription = {};
          let finalArray;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }

          //product id
          if (typeof data.product_id != 'undefined' && data.product_id != '') {
               categoryModel.checkProductId(data.product_id).then(checkProductId => {
                    if (checkProductId < 1) {
                         res.status(200).json({ 'status': "failed", 'message': cpMessage.InvalidProductId });
                    } else {
                         productId = data.product_id;
                         categoryModel.getMedia(productId).then(images => {
                              getMedia = { 'images': images };
                              categoryModel.getDescription(productId).then(description => {
                                   getDescription = { 'description': description };
                                   finalArray = Object.assign(getMedia, getDescription);
                                   res.status(200).json({ 'status': 'success', 'message': 'Product Description', 'data': finalArray });
                              }).catch(er => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.EmptyProductId });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/*
Purpose : getProductSlabs function is used to fetch the slab rates of the product_id passed.
author : Deepak Tiwari
Request : Require customer_token , product_id , le_Wh_id, pincode
Response : Returns product image  ,description.
*/
module.exports.getProductSlabs = function (req, res) {
     try {
          let data;
          let userId
          let productId;
          let le_Wh_id;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }

          let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 'NUll';

          //le_wh_id
          if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
               le_wh_id = data.le_wh_id;
               console.log("====>844", le_wh_id);
               le_wh_id = "'" + le_wh_id + "'";
               console.log("====>846", le_wh_id);
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
          }

          //customerToken validation
          if (typeof data.customer_token != 'undefined' && data.customer_token != "") {
               categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    if (checkCustomerToken > 0) {//valid token
                         categoryModel.getUserId(data.customer_token).then(customerId => {
                              userId = customerId[0].user_id;
                              console.log("=====>810", userId);
                              //product id
                              if (typeof data.product_id != 'undefined' && data.product_id != '') {
                                   categoryModel.checkProductId(data.product_id).then(checkProductId => {//checking productId
                                        if (checkProductId < 1) {
                                             res.status(200).json({ 'status': "failed", 'message': cpMessage.InvalidProductId });
                                        } else {
                                             productId = data.product_id;
                                             console.log("====>product_id", productId)
                                             //Fetching slap pricing based on product_id  , le_Wh_id , user_id , customerType
                                             categoryModel.getPricing(productId, le_wh_id, userId, customerType).then(pricingData => {
                                                  res.status(200).json({ 'status': 'success', 'message': 'Available ProductSlabs', 'data': pricingData });
                                             }).catch(err => {
                                                  console.log(err);
                                                  res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                                             })
                                        }
                                   }).catch(err => {
                                        console.log(err);
                                   })
                              } else {
                                   res.status(200).json({ 'status': "failed", 'message': cpMessage.EmptyProductId });
                              }
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         })
                    } else {
                         userId = 0;
                         res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidToken });
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
               })
          } else {
               userId = 0;
               //product id
               if (typeof data.product_id != 'undefined' && data.product_id != '') {
                    categoryModel.checkProductId(data.product_id).then(checkProductId => {//checking productId
                         if (checkProductId < 1) {
                              res.status(200).json({ 'status': "failed", 'message': cpMessage.InvalidProductId });
                         } else {
                              productId = data.product_id;
                              console.log("====>product_id", productId)
                              //Fetching slap pricing based on product_id  , le_Wh_id , user_id , customerType
                              categoryModel.getPricing(productId, le_wh_id, userId, customerType).then(pricingData => {
                                   res.status(200).json({ 'status': 'success', 'message': 'Available ProductSlabs', 'data': pricingData });
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         }
                    }).catch(err => {
                         console.log(err);
                    })
               } else {
                    res.status(200).json({ 'status': "failed", 'message': cpMessage.EmptyProductId });
               }
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/**
 * purpose :  getofflineproducts used to get productDetails based on productId
 * author  :  Deepak tiwari
 * Request :  Require  productId , le_eh_id , customerId 
 * Response :  Returns ProductDetails
 */
module.exports.getOfflineProducts = async function (req, res) {
     try {
          let data;
          let leWhId;
          let FFId;
          let productId;
          let customerType;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
               let skip =   typeof data.skip != 'undefined' ? data.skip : 0;
               let BeatId = typeof data.beat_id != 'undefined' ? data.beat_id : '';
               //validating le_wh_id 
               if (typeof data.le_wh_id != 'undefined' && data.le_wh_id != '') {
                    leWhId = "'" + data.le_wh_id + "'";
               } else {
                    leWhId = '1';//assigning default dc as DCRamanthapur
               }

               //ff_id 
               if (typeof data.ff_id != 'undefined' && data.ff_id != '') {
                    FFId = data.ff_id;
               } else {
                    FFId = 0;//assigning default dc as DCRamanthapur
               }

               if((typeof BeatId =='undefined' || BeatId == '')  && (typeof  FFId =='undefined' ||  FFId =='')){
                    skip =  1;
               }
               //customerId
               customerType = typeof data.customer_type != 'undefined' ? data.customer_type : '';
               //product Id
               if (typeof data.product_ids != 'undefined') {
                    productId = "'" + data.product_ids + "'";
                    categoryModel.getOfflineProducts(productId, leWhId, customerType).then(async (products) => {//used to get products details
                           for (let i = 0; i < products.length; i++) {
                              let categoryName = await categoryModel.getCategoryName(products[i].category_id);
                              if (categoryName != '') {
                                   products[i].categoryName = categoryName;
                              } else {
                                   products[i].categoryName = '';
                              }
                              //Used to get Prodcut point for each line items
                              let productPoints = await categoryModel.getProductPoint(products[i].product_id, leWhId);
                              if ((productPoints)) {
                                   products[i].product_point = productPoints[0].points;
                              } else {
                                   products[i].product_point = 0;
                              }
                              //Used to get warehouse details based on category id
                              if ((products[i].key_value_index != 'Q9' && skip !=  1)) {
                                   //In case when we get freebiee product whne jump that condition
                                   if (leWhId.split(",").length > 1) {
                                        let HubDetails = await categoryModel.getWareHouseBasedOnCategories(products[i].category_id, data.le_wh_id.split(","), BeatId, FFId);
                                        if ((HubDetails)) {
                                             products[i].le_wh_id = HubDetails[0].dc_id;
                                        } else {
                                             products = ''; //products[i].le_wh_id = leWhId.replace(/'/g, '');
                                        }
                                   } else {
                                        let HubDetails = await categoryModel.getWareHouseBasedOnCategories(products[i].category_id, leWhId, BeatId, FFId);
                                        if ((HubDetails)) {
                                             products[i].le_wh_id = HubDetails[0].dc_id;
                                        } else {
                                             products = '';
                                        }
                                   }
                                   //minimun order limit calculation
                                   if (products.length > 0) {
                                        let minimunOrderLimit = await categoryModel.getOrderLimitBasedONProduct(products[i].le_wh_id, data.customer_type, data.ff_id);
                                        if ((minimunOrderLimit)) {
                                             products[i].minimun_order_value = minimunOrderLimit.minOrderValue != null ? minimunOrderLimit.minOrderValue : 0;
                                             products[i].minimun_order_count = minimunOrderLimit.movOrdercount;
                                        } else {
                                             products[i].minimun_order_value = 0;
                                             products[i].minimun_order_count = 0;
                                        }

                                   }
                              } else {
                                   products[i].minimun_order_value = 0;
                                   products[i].minimun_order_count = 0;
                                   products[i].le_wh_id = data.le_wh_id;
                              }

                         }

                         if (products) {
                              res.status(200).json({ 'status': 'success', 'message': 'Available Products', "data": { 'data': products } })
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.ProductNotFound, "data": { 'data': products } });
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(500).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                    })
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidProductId })
               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.invalidRequestBody });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}


/**
 * purpose :  getreviewSpecification is used to get product reviews and specification
 * author:Deepak tiwari
 * request :  product_id 
 * response :  Return product specifications , product rating 
 */
module.exports.getReviewSpecification = function (req, res) {
     try {
          let data;
          let productId;
          let finalArray;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }

          //product id
          if (typeof data.product_id != 'undefined' && data.product_id != '') {
               categoryModel.checkProductId(data.product_id).then(checkProductId => {
                    if (checkProductId < 1) {
                         res.status(200).json({ 'status': "failed", 'message': cpMessage.InvalidProductId });
                    } else {
                         productId = data.product_id;
                         categoryModel.getProductSpecifications(productId).then(productSpecification => {//used to get Product specification based on product_id
                              let specification = { 'specifications': productSpecification };
                              categoryModel.getReviews(productId).then(productReviews => {
                                   let reviews = { 'reviews': productReviews };
                                   finalArray = Object.assign(specification, reviews);
                                   res.status(200).json({ 'status': 'success', 'message': 'Product reviews and specification', 'data': finalArray });
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                              })
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
                         })
                    }
               }).catch(err => {
                    console.log(err);
               })
          } else {
               res.status(200).json({ 'status': "failed", 'message': cpMessage.EmptyProductId });
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}


Array.prototype.unique = function () {
     return this.filter(function (value, index, self) {
          return self.lastIndexOf(value) === index;
     });
}

/**
 * [getOfferProductDetails description]: This function is used to result the response of getOfferproduct Api
 *
 * @param   {[type]}  allProductIds  [allProductIds description]: List of ProductId
 */
function getOfferProductDetails(allProductIds, res) {
     try {
          if (allProductIds != '') {
               let productCount;
               if (typeof allProductIds.TotalProducts == 'undefined') {
                    productCount = 0;
               } else {
                    productCount = allProductIds.TotalProducts;
               }
               let productId = _.pluck(allProductIds.data, 'product_id');//fetching product_id from totalProduct array using underScore npm
               let finalArray = { 'product_id': productId.join(), 'count': productCount };
               res.status(200).json({ 'status': 'success', 'message': 'Available products detail', 'data': finalArray });
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'No products found' })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError });
     }
}

/**
 * [OfferProductBasedOnFlagValue description] : This function calls getofferProductDetails internally to get offerproduct  based on flag value
 *
 * @param   {[type]}  data           [data description] : request  body
 * @param   {[type]}  sortId         [sortId description]: Using this keys for sorting.
 * @param   {[type]}  customerToken  [customerToken description] : Session handling
 */
function OfferProductBasedOnFlagValue(data, sortId, segmentId, leWhId, offset, offsetLimit, customerToken, res) {
     try {
          //flag
          let flag;
          if (typeof data.flag != 'undefined') {
               flag = data.flag;
               let temp = [];
               let topRatedProductIds;
               let allProdId;
               if (flag == 55004) {//Top Rated products
                    console.log("You are in if condition")
                    categoryModel.getTopRatedProduct(offset, offsetLimit, segmentId).then(topRated => {
                         allProdId = topRated;
                         if (typeof allProdId != 'undefined') {
                              topRatedProductIds = _.pluck(allProdId, 'entity_id');//used to fetch product_id from an array
                              allProdId = topRatedProductIds.unique();//to get unique productId
                              categoryModel.getProducts(category_id = 4, offset, offsetLimit, sortId = '', customerToken, api = 3, allProdId, leWhId, segmentId).then(products => {
                                   allProdId = JSON.parse(JSON.stringify(products)).data;
                                   allProdId = Object.assign(allProdId, {
                                        TotalProducts: allProdId.length,//keeping product count in totalProducts
                                        data: allProdId
                                   })
                                   getOfferProductDetails(allProdId, res);//function will return the response getOfferProduct api
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                              })
                         } else {
                              allProdId = [];
                              temp = [];
                              allProdId = Object.assign(allProdId, {
                                   TotalProducts: allProdId.length,//keeping product count in totalProducts
                                   data: allProdId
                              })
                              getOfferProductDetails(allProdId, res);//function will return the response getOfferProduct api
                         }
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                    })
               } else {//working fine
                    console.log("====You are in else block")
                    categoryModel.getBlockedData(customerToken).then(blockList => {//Used to get brands and manufactures details based on scopeType and scopeId(scopetype is nothing but dc, fc , hub , beat or spoke)
                         let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 0;
                         categoryModel.getProductsByKPI(flag, offset, offsetLimit, segmentId, leWhId, sortId, blockList, customerType).then(productsByKpi => {
                              allProdId = productsByKpi;
                              getOfferProductDetails(allProdId, res);
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                         })
                    }).catch(err => {
                         console.log(err);
                         res.status(200).json({ 'status': "failed", 'message': cpMessage.internalCatch });
                    })

               }
          } else {
               res.status(200).json({ 'status': 'failed', 'message': 'Flag is required' })
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

/**
 * purpose :getOfferProduct is used to get offer on  products (we were fetching offer based on flag value like 55001 , 55002 , 55004 , 55003)
 * author : Deepak tiwari
 * request : product_id  , customer_token,
 * response :  return Offer product
 */
module.exports.getOfferProducts = function (req, res) {
     try {
          let data;
          let sortId;
          let segmentId;
          let offset;
          let offsetLimit;
          let customerToken;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }

          //sortId using for sorting purpose
          if (typeof data.sort_id != 'undefined' && data.sort_id != '') {
               sortId = data.sort_id;
          } else {
               sortId = "-1";
          }

          //segmentid
          if (typeof data.segment_id != 'undefined' && data.segment_id != '') {
               segmentId = data.segment_id;
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptySegmentId })
          }

          //offset
          if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
               offset = data.offset;
          } else {
               res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
          }

          //offsetlimit
          if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
               offsetLimit = data.offset_limit;
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
          }
          //customer token
          if (typeof data.customer_token != 'undefined') {
               categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    console.log("checkcustomertoken", checkCustomerToken);
                    if (checkCustomerToken > 0) {
                         customerToken = data.customer_token;
                         leWhId = "'" + data.le_wh_id + "'";
                         OfferProductBasedOnFlagValue(data, sortId, segmentId, leWhId, offset, offsetLimit, customerToken, res);
                    } else {
                         console.log("hello")
                         customerToken = "";
                         //le_wh_id
                         if (typeof data.le_wh_id != 'undefined') {
                              leWhId = "'" + data.le_wh_id + "'";
                              OfferProductBasedOnFlagValue(data, sortId, segmentId, leWhId, offset, offsetLimit, customerToken, res);
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
                         }
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
               })

          } else {
               customerToken = "";
               //le_wh_id
               if (typeof data.le_wh_id != 'undefined') {
                    leWhId = "'" + data.le_wh_id + "'";
                    OfferProductBasedOnFlagValue(data, sortId, segmentId, leWhId, offset, offsetLimit, customerToken, res);
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
               }
          }
     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }
}

/**
 * purpose :getMustSkuProductList is used to sku product details  based on flag (180001)
 * author : Deepak tiwari
 * request : flag ,sortId , segmentId , customer_token , offset  , offert_limit , 
 * response :  return all sku productId
 */
module.exports.getMustSkuProductList = function (req, res) {
     try {
          let data;
          let sortId;
          let segmentId;
          let offset;
          let offsetLimit;
          let customerToken;
          let flag;
          let leWhId;
          if (typeof req.body.data != 'undefined') {
               data = JSON.parse(req.body.data);
          } else {
               data = "";
          }

          //sortId using for sorting purpose
          if (typeof data.sort_id != 'undefined' && data.sort_id != '') {
               sortId = data.sort_id;
          } else {
               sortId = "-1";
          }

          //segmentid
          if (typeof data.segment_id != 'undefined' && data.segment_id != '') {
               segmentId = data.segment_id;
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptySegmentId })
          }

          //offset
          if (typeof data.offset != 'undefined' && data.offset != '' && data.offset >= 0) {
               offset = data.offset;
          } else {
               res.status(200).json({ 'status': "failed", "message": cpMessage.InvalidOffset });
          }

          //offsetlimit
          if (typeof data.offset_limit != 'undefined' && data.offset_limit != '' && data.offset_limit >= 0) {
               offsetLimit = data.offset_limit;
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.InvalidOffsetLimit });
          }

          if (typeof data.flag != 'undefined' && data.flag != '') {
               flag = data.flag;
          } else {
               res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyFlagValue });//flag velue not passed in request body
          }
          //customer token
          if (typeof data.customer_token != 'undefined') {
               categoryModel.checkCustomerToken(data.customer_token).then(checkCustomerToken => {
                    console.log("checkcustomertoken", checkCustomerToken);
                    if (checkCustomerToken > 0) {
                         customerToken = data.customer_token;
                         leWhId = "'" + data.le_wh_id + "'";
                         if (flag == 180001) {
                              categoryModel.getBlockedData(customerToken).then(blockList => {//Used to get brands and manufactures details based on scopeType and scopeId(scopetype is nothing but dc, fc , hub , beat or spoke)
                                   let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 0;
                                   categoryModel.getCpMustSkuList(flag, offset, offsetLimit, segmentId, leWhId, sortId, blockList, customerType).then(productsByKpi => {
                                        allProdId = productsByKpi;
                                        getOfferProductDetails(allProdId, res);
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                                   })
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ 'status': "failed", 'message': cpMessage.internalCatch });
                              })
                         }
                    } else {
                         console.log("hello")
                         customerToken = "";
                         //le_wh_id
                         if (typeof data.le_wh_id != 'undefined') {
                              leWhId = "'" + data.le_wh_id + "'";
                              if (flag == 180001) {
                                   categoryModel.getBlockedData(customerToken).then(blockList => {//Used to get brands and manufactures details based on scopeType and scopeId(scopetype is nothing but dc, fc , hub , beat or spoke)
                                        let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 0;
                                        categoryModel.getCpMustskuList(flag, offset, offsetLimit, segmentId, leWhId, sortId, blockList, customerType).then(productsByKpi => {
                                             allProdId = productsByKpi;
                                             getOfferProductDetails(allProdId, res);
                                        }).catch(err => {
                                             console.log(err);
                                             res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                                        })
                                   }).catch(err => {
                                        console.log(err);
                                        res.status(200).json({ 'status': "failed", 'message': cpMessage.internalCatch });
                                   })
                              }
                         } else {
                              res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
                         }
                    }
               }).catch(err => {
                    console.log(err);
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.internalCatch });
               })

          } else {
               customerToken = "";
               //le_wh_id
               if (typeof data.le_wh_id != 'undefined') {
                    leWhId = "'" + data.le_wh_id + "'";
                    if (flag == 180001) {
                         categoryModel.getBlockedData(customerToken).then(blockList => {//Used to get brands and manufactures details based on scopeType and scopeId(scopetype is nothing but dc, fc , hub , beat or spoke)
                              let customerType = typeof data.customer_type != 'undefined' ? data.customer_type : 0;
                              categoryModel.getCpMustSkuList(flag, offset, offsetLimit, segmentId, leWhId, sortId, blockList, customerType).then(productsByKpi => {
                                   allProdId = productsByKpi;
                                   getOfferProductDetails(allProdId, res);
                              }).catch(err => {
                                   console.log(err);
                                   res.status(200).json({ "status": 'failed', 'message': cpMessage.internalCatch })
                              })
                         }).catch(err => {
                              console.log(err);
                              res.status(200).json({ 'status': "failed", 'message': cpMessage.internalCatch });
                         })
                    }
               } else {
                    res.status(200).json({ 'status': 'failed', 'message': cpMessage.EmptyLeWhId })
               }
          }

     } catch (err) {
          console.log(err);
          res.status(500).json({ 'status': 'failed', 'message': cpMessage.serverError })
     }

}
/**
 * purpose :getcategoryAndSubcategory is used to get category and subcategory list based on warehouse Details.
 * author : Deepak tiwari
 * request : Warehouse Details.
 * response :  Return category and subcategory.
 */
module.exports.getCategoryAndSubcategory = function(req, res) {
     try {
          if(typeof req.body.data != 'undefined' && req.body.data != '' ){
              let data =  JSON.parse(req.body.data);
              let warehouseId = (typeof data.le_wh_id !='undefined' &&  data.le_wh_id != '') ? data.le_wh_id : ''; 
              categoryModel.getCategoryList(warehouseId).then(response=>{
                    if(response) {
                         res.status(200).json({'status':'success','message':'Category List' , 'data':response});
                    } else {
                         res.status(200).json({'status':'failed','message':'Category Not Found'});
                    }
              }).catch(err=>{
                   console.log(err);
                   res.status(200).json({'status':'failed','message':cpMessage.internalCatch});
              })
          } else {
               res.status(200).json({'status':'failed','message':cpMessage.invalidRequestBody});
          }

     } catch(err) {
          console.log(err);
          res.status(500).json({'status':'failed','message':cpMessage.serverError});
     }
}