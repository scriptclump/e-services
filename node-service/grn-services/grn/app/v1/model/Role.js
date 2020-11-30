'user strict';

const Sequelize = require('sequelize');
var sequelize = require('../../config/mysql');
var database = require('../../config/mysqldb');
var rolerepo = require('../model/Rolerepo');
let db = database.DB;

module.exports = {
	getFilterData: async function (req, res) {
		return new Promise((resolve, reject) => {
			try {
				//console.log(req);
				var response = [];
				if (req.hasOwnProperty('permissionLevelId') && req.permissionLevelId > 0) {
					if (req.hasOwnProperty('user_id')) {
						var currentUserId = req.user_id
					} else {
						res.send('Please Send UserID');
					}
					module.exports.getPermissionLevelData(req.permissionLevelId).then(data1 => {
						switch (data1) {
							case 'brand':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
							case 'category':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
							case 'manufacturer':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
							case 'supplier':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
							case 'products':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
							case 'sbu':
								var sbuparams = { 'user_id': currentUserId, 'permission_level': req.permissionLevelId };
								module.exports.getWarehouseData(sbuparams).then(data2 => {
									// console.log('data2', sbuparams,);
									var warehousekeyvalue = { 'sbu': data2 };
									response.push(warehousekeyvalue);
									return resolve(response);
								});
								break;
							case 'customer':
								module.exports.getLegalEntityId(currentUserId).then(data2 => {

								});
								break;
						}
					})
				} else {
				}
			} catch (err) {
				console.error(err);
				return err;
			}
		});
	},
	getPermissionLevelData: function (req, res) {
		return new Promise((resolve, reject) => {
			try {
				var permissionName = '';
				if (req > 0) {
					var qry = "select name from permission_level where permission_level_id=" + req;
					db.query(qry, {}, function (err, rows) {
						if (err) {
							return reject(err);
						}
						if (rows.length > 0) {
							// console.log('permissionNameee', rows);
							permissionName = rows[0].name;
							return resolve(permissionName);
						}
						else {
							return resolve(permissionName);
						}
					});
				}
			} catch (err) {
				console.error(err);
				return reject("No Results found..")
			}
		});
	},
	getLegalEntityId: function (req, res) {
		return new Promise(async (resolve, reject) => {
			try {
				var legalEntityId = 0;

				//console.log(req);
				if (req > 0) {
					var qry1 = "select legal_entity_id from users where user_id=" + req + " limit 1";
					db.query(qry1, {}, async function (err, rows) {
						if (err) {
							reject(err);
						}

						if (Object.keys(rows).length > 0) {

							legalEntityId = rows[0].hasOwnProperty('legal_entity_id') ? rows[0].legal_entity_id : 0;
						}
						resolve(legalEntityId);
					});
				}
			} catch (err) {
				console.error(err);
				return reject("No Results found..")
			}
		});
	},

	getWarehouseData: async function (req, res) {
		return new Promise(async (resolve, reject) => {
			try {
				var resultset = {};
				var objectIds = [];

				if (req.user_id > 0 && req.permission_level > 0) {
					//console.log('=========================');
					var globalFeature = await rolerepo.checkPermissionByFeatureCode('GLB0001', req.user_id);
					var inActiveDCAccess = await rolerepo.checkPermissionByFeatureCode('GLBWH0001', req.user_id);

					if (req.hasOwnProperty('active') && req.active == 0) {
						inActiveDCAccess = 1;
					}

					var qry2 = "select group_concat(object_id) as object_id from user_permssion where user_id=" + req.user_id + " and permission_level_id=" + req.permission_level + " group by object_id";
					db.query(qry2, {}, function (err, rows) {
						if (err) {
							return reject(err);
						}

						if (rows.length > 0) {
							rows.forEach(objId => {
								objectIds.push(objId.object_id); //getting the object Id values into objectId array
							})


							if (!globalFeature) {
								var qry3 = `SET SESSION group_concat_max_len = 100000;select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0 AND is_disabled = 0 `;
								// if(inActiveDCAccess)
								// {
								// 	qry3 +=' and status=1';
								// }
								if (objectIds.length == 1 || objectIds.includes('0')) {
									qry3 += " and dc_type in (118001,118002)";
								} else {
									qry3 += " and dc_type in " + objectIds;
								}
								qry3 += " group by dc_type";
							} else if (globalFeature) {
								var qry3 = "SET SESSION group_concat_max_len = 100000;select GROUP_CONCAT(le_wh_id) as le_wh_id,dc_type from legalentity_warehouses where dc_type > 0 AND is_disabled = 0";
								// if(inActiveDCAccess==0)
								// {
								// 	qry3 +=' and status=1';
								// }
								qry3 += " group by dc_type";
							}

							// console.log('qry3', qry3);

							db.query(qry3, {}, async function (err1, rows1) {
								if (err1) {
									return reject(err1);
								}
								if (Object.keys(rows1).length > 0) {
									// console.log("outpurrr", rows1);
									let rows = rows1[1];
									for (var i = 0; i < rows.length; i++) {
										//dc_typeWarehouseSet={'key':rows1[i].dc_type,'value': rows1[i].le_wh_id};
										resultset[rows[i].dc_type] = rows[i].le_wh_id;
										//resultset.push(dc_typeWarehouseSet);
									}
									// console.log(resultset,'ressssssssssssult');
									resolve(resultset);
								}
								else {
									return reject("No Results found..")
								}
							});
						}
					});


				} else {
					console.log('++++++++++++++++++++++++++++++++++++++++++');
				}
			} catch (err) {
				console.error(err);
				return reject("No Results found..")
			}
		});
	},


	// 	 getWarehouseData : async function(currentUserId, permissionLevelId, active = 1) {//changes required
	// 		return new Promise(async (resolve, reject) => {
	// 			 try {
	// 				  let response = [];
	// 				  let globalFeatures;
	// 				  let globalFeature;
	// 				  let inActiveDCAccess;
	// 				  let inActiveDCAcc;
	// 				  let query = [];
	// 				  if (currentUserId > 0 && permissionLevelId > 0) {
	// 					   //checking for global access features
	// 					//    globalFeatures = await ConfirmOtpcheckPermissionByFeatureCode('GLB0001', currentUserId)
	// 					  globalFeatures = await rolerepo.checkPermissionByFeatureCode('GLB0001',currentUserId);
	// 					   console.log("globalacess ====>49", globalFeatures);
	// 					   if (globalFeatures) {
	// 							globalFeature = globalFeatures;
	// 					   } else {
	// 							globalFeature = 0;
	// 					   }
	// 					   //checking for inactiveAccess features
	// 					//    inActiveDCAcc = await ConfirmOtpcheckPermissionByFeatureCode('GLBWH0001', currentUserId)
	// 					inActiveDCAcc = await rolerepo.checkPermissionByFeatureCode('GLBWH0001',currentUserId);
	// 					   if (inActiveDCAcc) {
	// 							inActiveDCAccess = inActiveDCAcc;
	// 					   } else {
	// 							inActiveDCAccess = 0;
	// 					   }

	// 					   if (active == 0) {
	// 							inActiveDCAccess = 1;
	// 					   }
	// 					   let data = "select object_id from user_permssion where user_id =" + currentUserId + " && permission_level_id =" + permissionLevelId + " group by object_id";
	// 					   sequelize.query(data).then(resultData => {
	// 							let result = JSON.parse(JSON.stringify(resultData[0]))
	// 							if (result != '') {//!=
	// 								 let Data = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses  where dc_type  > 0";
	// 								 if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
	// 									  Data = Data.concat(" && status = 1");//query returns only active records
	// 								 }

	// 								 if (!globalFeature) {
	// 									  let testResult = [];
	// 									  result.forEach((element) => {
	// 										   testResult.push(element.object_id);
	// 									  })
	// 									  if (testResult.length == 1 || testResult.indexOf(0) != -1) {
	// 										   if (typeof testResult != 'undefined' && (testResult == 0 || testResult.indexOf(0) != -1)) {
	// 												Data = Data.concat(" && dc_type IN (118001, 118002) group by dc_type");
	// 										   } else {
	// 												Data = Data.concat(" && bu_id IN (" + testResult + ") group by dc_type");
	// 										   }
	// 									  } else {
	// 										   Data = Data.concat(" && bu_id IN (" + testResult + ") group by dc_type");
	// 									  }

	// 									  let update_query = "SET SESSION group_concat_max_len = 100000";
	// 									  sequelize.query(update_query).then(rows => {
	// 										   console.log("updated successfully");
	// 									  }).catch(err => {
	// 										   console.log(err);
	// 										   reject(err);
	// 									  })
	// 									  //  Data = Data.concat("select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type");
	// 									  // let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
	// 									  sequelize.query(Data).then(rows => {
	// 										   if (rows.length > 0) {
	// 												if (rows[0].length > 0) {
	// 													 rows[0].forEach((element) => {
	// 														  response.push({ [element.dc_type]: element.le_wh_id });
	// 													 })
	// 													 resolve(response);
	// 												}
	// 										   } else {
	// 												resolve(response);
	// 										   }
	// 									  }).catch(err => {
	// 										   console.log(err);
	// 									  })
	// 								 } else if (globalFeature) {
	// 									  //  let Data = "select GROUP_CONCAT(le_wh_id ORDER BY le_wh_id DESC) AS le_wh_id , dc_type from legalentity_warehouses  where dc_type  > 0  AND is_disabled = 0";
	// 									  let Data = "SELECT SUBSTRING_INDEX(GROUP_CONCAT(le_wh_id ORDER BY le_wh_id DESC),',',1) AS le_wh_id  , dc_type FROM legalentity_warehouses  JOIN legal_entities ON legalentity_warehouses.`legal_entity_id`=legal_entities.`legal_entity_id` WHERE dc_type  > 0 AND is_disabled = 0 AND legal_entities.`legal_entity_type_id` IN (1014)";
	// 									  if (inActiveDCAccess == 0) { // if user dont have access to inactive dc's
	// 										   Data = Data.concat(" AND status = 1  group by dc_type");
	// 									  } else {
	// 										   Data = Data.concat(" group by dc_type");
	// 									  }


	// 									  let update_query = "SET SESSION group_concat_max_len = 100000";
	// 									  sequelize.query(update_query).then(rows => {
	// 										   console.log("updated successfully=>138");
	// 									  }).catch(err => {
	// 										   console.log(err);
	// 										   reject(err);
	// 									  })
	// 									  // Data = Data.concat("select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type");
	// 									  // let data_1 = "select GROUP_CONCAT(le_wh_id) as le_wh_id , dc_type from legalentity_warehouses group by dc_type";
	// 									  sequelize.query(Data).then(rows => {
	// 										   if (rows.length > 0) {
	// 												if (rows[0].length > 0) {
	// 													 rows[0].forEach((element) => {
	// 														  response.push({ [element.dc_type]: element.le_wh_id });
	// 													 })
	// 													 resolve(response);
	// 												}
	// 										   } else {
	// 												resolve(response);
	// 										   }
	// 									  }).catch(err => {
	// 										   console.log(err);
	// 									  })
	// 								 }
	// 							} else {
	// 								 resolve('');
	// 							}
	// 					   }).catch(err => {
	// 							console.log(err);
	// 							reject(err);
	// 					   })

	// 				  } else {
	// 					   resolve('');
	// 				  }
	// 			 } catch (err) {
	// 				  console.log(err)
	// 				  reject(err);
	// 			 }
	// 		})
	//    },

	checkUserIsSupplier: async function (user_id) {
		return new Promise((resolve, reject) => {
			if (user_id) {
				let userData = "select * from users u join legal_entities l on u.`legal_entity_id`= l.legal_entity_id where l.`legal_entity_type_id` IN (1006,1002,89002) AND  u.is_active=1 and u.user_id=" + user_id;
				db.query(userData, {}, async function (err, rows) {
					if (err) {
						reject('error');
					}
					if (rows.length > 0) {
						//console.log('aaaaaaaaaaaaaaaa');
						resolve(rows.length);
					} else {
						// console.log('zzzzzzzzzzzzzzzzzzzzz', rows);
						resolve(0);
					}
				});
			} else {
				// let userData = array();
				resolve(0);
			}
		});
	},

	getAllAccessBrands: async function (user_id) {
		return new Promise((resolve, reject) => {
			var finalArray = {};
			var brands = "select GROUP_CONCAT(object_id) as brands from user_permssion where permission_level_id=7 and user_id=" + user_id;
			var manufacturer = "select GROUP_CONCAT(object_id) as manuf from user_permssion where permission_level_id=11 and user_id=" + user_id + " limit 1";
			db.query(manufacturer, {}, async function (err2, rows2) {
				if (err2) {
					reject('error');
				}
				if (rows2.length > 0) {
					db.query(brands, {}, function (err3, rows3) {
						if (err3) {
							reject('error');
						}

						// console.log(rows3.length > 0);
						if (rows3.length > 0 && rows3[0].hasOwnProperty('brands')) {
							// console.log("rows333",rows3[0]['brands']);
							// console.log("nowthisss",rows2[0].manuf);
							var brandsFromManufacturer = "select GROUP_CONCAT(brand_id) as brandsmanuf from brands where find_in_set(mfg_id,'" + rows2[0].manuf + "')";
							db.query(brandsFromManufacturer, {}, async function (err4, rows4) {
								if (err4) {
									reject('error');
								}
								// console.log("rwosss4", rows4);
								let brand_array = rows3[0].brands.split(',');
								let manf_array = rows4[0].brandsmanuf.split(',');
								if (rows4.length > 0) {
									finalArray = [...brand_array, ...manf_array];
									resolve(finalArray);
								} else {
									finalArray = brand_array;
									resolve(finalArray);
								}
							});
						} else {
							resolve('');
						}
					});
				}
			});
		});
	},
	suppliersbasedOnLegalEnitityID: async function (legalentity_id) {
		return new Promise((resolve, reject) => {
			var qry4 = "select legal_entity_id,business_legal_name from legal_entities where legal_entity_type_id=1002 and is_approved=1 and parent_id=" + legalentity_id;
			db.query(qry4, {}, async function (err, result) {
				if (err) {
					reject('error');
				}
				if (Object.keys(result).length > 0) {
					resolve(result);
				}
			});
		});
	},
	getDCFCData: async function (params) {
		var fields = params.fields;
		var le_id = params.legalentity;
		fields += ",(SELECT getMastLookupValue(legal_entities.legal_entity_type_id)) as btype";
		return new Promise((resolve, reject) => {
			var qry5 = "select " + fields + " from legal_entities left join dc_fc_mapping on dc_le_id=legal_entities.legal_entity_id where dc_fc_mapping.fc_le_id=" + le_id;
			db.query(qry5, {}, async function (err, result) {
				if (err) {
					reject('error');
				}
				if (Object.keys(result).length > 0) {
					//console.log('dcfc data'+result);
					resolve(result);
				} else {
					resolve([]);
				}
			})
		});
	},

	masterLookUpDescriptionByvalue: async function (masterlookupvalue) {
		return new Promise((resolve, reject) => {
			var masterlookupqry = "select description from master_lookup where value=" + masterlookupvalue;
			db.query(masterlookupqry, {}, async function (err, rows) {
				if (err) {
					reject('error');
				}
				if (rows.length > 0) {
					resolve(rows);
				}
			})
		})
	}
}
