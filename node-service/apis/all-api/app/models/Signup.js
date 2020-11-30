/**
 * signup.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
const db = require('../../dbConnection');

module.exports = {
    checkbusinesslegalName: function(data, callback) {
        var data = JSON.parse(data);
        var legalname = data.business_legal_name;
        var email = data.email;
        var sql = "SELECT * FROM users AS u WHERE u.`email_id` = ? ";
        db.query(sql, [email], function(err, result) {
            if (err) {
                console.log(err);
                return err;
            }
            callback(result);
        });
    },
    saveIntolegalEntity: function(data, picture_upload, callback) {
        var data = JSON.parse(data);
        var lecode = "CALL prc_reference_no('" + data.state_code + "','ST')";
        db.query(lecode, {}, function(err, result) {
            if (err) {
                return err;
            } else {
                var virtual = 0;
                if (data.hasOwnProperty('is_virtual')) {
                    virtual = 1;
                }
                var selfTax = 0;
                if (data.hasOwnProperty('is_self_tax')) {
                    selfTax = 1;
                }
                var sql = "Insert Into legal_entities (business_legal_name,display_name,address1,city,state_id,country,pincode,gstin,legal_entity_type_id,business_type_id,pan_number,tin_number,website_url,logo,le_code,created_by,parent_le_id,is_virtual,is_self_tax,address2,fssai) VALUES ('" + data.business_legal_name + "','" + data.display_Name + "','" + data.address + "','" + data.city + "','" + data.state_id + "',99,'" + data.pincode + "','" + data.gstin_number + "','" + data.DC_FC_id + "','47001','NULL','" + data.gstin_number + "','NULL','" + picture_upload + "','" + result[0][0].ref_no + "','" + data.created_by + "','" + data.parent_le_id + "','" + virtual + "','" + selfTax + "','" + data.address_2 + "','" + data.lic_num + "')";
                db.query(sql, {}, function(err, result) {
                    if (err) {
                        console.log(err);
                        return err;
                    } else {
                        callback(result.insertId);
                    }
                });
            }
        });
    },
    saveIntoWarehouseTable: function(entity_id, data, callback) {
        var data = JSON.parse(data);
        var lp_wh_name = data.display_Name;
        var contact_name = data.first_name + ' ' + data.last_name;
        var phone_no = data.phone_number;
        var email = data.email;
        var address1 = data.address;
        var address2 = data.address_2;
        var pincode = data.pincode;
        var state = data.state_id;
        var city = data.city;
        var tin_number = data.gstin_number;
        var insertId = 0;
        var costCenter = data.cost_center;
        var dcfc_legalentitytype = data.dcfc_legalentitytype;
        var i_state_code = data.i_state_code;
        var i_city_code = data.city_code;
        var sql = "Insert Into legalentity_warehouses (legal_entity_id,lp_name,dc_type,lp_wh_name,contact_name,phone_no,email,address1,state,country,pincode,city,lp_id,le_wh_code,display_name,tin_number,address2,status,cost_centre) VALUES ('" + entity_id + "','Custom',118001,'" + lp_wh_name + " DC','" + contact_name + "','" + phone_no + "','" + email + "','" + address1 + "','" + state + "',99,'" + pincode + "','" + city + "',0,'" + data.Warehouse_Code + "','" + lp_wh_name + " DC','" + tin_number + "','" + address2 + "',1,'" + costCenter + "'),('" + entity_id + "','Custom',118002,'" + lp_wh_name + " HUB','" + contact_name + "','" + phone_no + "','" + email + "','" + address1 + "','" + state + "',99,'" + pincode + "','" + city + "',0,'" + data.Warehouse_Code + " HUB','" + lp_wh_name + " HUB','" + tin_number + "','" + address2 + "',1,'" + costCenter + "')";
        db.query(sql, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                insertId = result.insertId;
                if (dcfc_legalentitytype == 'DC') {
                    var dcfccode_increment = "UPDATE state_city_codes SET dc_inc_id = (dc_inc_id + 1) WHERE state_code = '" + i_state_code + "' AND city_code = '" + i_city_code + "'";
                } else {
                    var dcfccode_increment = "UPDATE state_city_codes SET fc_inc_id = (fc_inc_id + 1) WHERE state_code = '" + i_state_code + "' AND city_code = '" + i_city_code + "'";
                }
                db.query(dcfccode_increment, {}, function(errorincrement, incrementresult) {
                    if (errorincrement) {
                        console.log(errorincrement);
                        return errorincrement;
                    } else {
                        var gettdataFromlewh = "select le_wh_id from legalentity_warehouses where dc_type=118001 and legal_entity_id=" + entity_id;
                        db.query(gettdataFromlewh, {}, function(errorle1, resultle1) {
                            if (errorle1) {
                                console.log(err);
                                return err;
                            } else {
                                var dc_id = resultle1[0].le_wh_id;
                                var getHubIdFromlewh = "select le_wh_id from legalentity_warehouses where dc_type=118002 and legal_entity_id=" + entity_id;
                                db.query(getHubIdFromlewh, {}, function(errorle2, resultle2) {
                                    if (errorle2) {
                                        console.log(err);
                                        return err;
                                    } else {
                                        var hub_id = resultle2[0].le_wh_id;
                                        var insertHubQuery = "Insert Into dc_hub_mapping (legal_entity_id,dc_id,hub_id,is_active,dc_name,hub_name,dc_code,hub_code) VALUES (" + entity_id + "," + dc_id + "," + hub_id + ",1,'" + lp_wh_name + " DC','" + lp_wh_name + " HUB','" + data.Warehouse_Code + "','" + data.Warehouse_Code + " HUB')";
                                        db.query(insertHubQuery, {}, function(errorle3, resultle3) {
                                            if (errorle3) {
                                                console.log(errorle3);
                                                return errorle3;
                                            } else {
                                                var spokeQuery = "Insert Into spokes (spoke_name,le_wh_id) VALUES ('" + lp_wh_name + "'," + hub_id + ")";
                                                db.query(spokeQuery, {}, function(errorle4, resultle4) {
                                                    if (errorle4) {
                                                        console.log(errorle4);
                                                        return errorle4;
                                                    } else {
                                                        if (resultle4.hasOwnProperty('insertId')) {
                                                            var spoke_id = resultle4.insertId;
                                                        } else {
                                                            var spoke_id = 0;
                                                        }
                                                        var beatQuery = "Insert Into pjp_pincode_area (pjp_name,le_wh_id,spoke_id) VALUES ('" + lp_wh_name + "'," + hub_id + "," + spoke_id + ")";
                                                        db.query(beatQuery, {}, function(errorle5, resultle5) {
                                                            if (errorle5) {
                                                                console.log(errorle5);
                                                                return errorle5;
                                                            } else {
                                                                if (resultle5.hasOwnProperty('insertId')) {
                                                                    var beat_id = resultle5.insertId;
                                                                } else {
                                                                    var beat_id = 0;
                                                                }
                                                                var customersQuery = "Insert Into customers (le_id,hub_id,spoke_id,beat_id,area_id) VALUES (" + entity_id + "," + hub_id + "," + spoke_id + "," + beat_id + ",0)";
                                                                db.query(customersQuery, {}, function(errorle6, resultle6) {
                                                                    if (errorle6) {
                                                                        console.log(errorle6);
                                                                        return errorle6;
                                                                    } else {
                                                                        var master_lookup = "select value from master_lookup where mas_cat_id=3";
                                                                        db.query(master_lookup, {}, function(err, result) {
                                                                            if (err) {
                                                                                console.log(err);
                                                                                return err;
                                                                            } else {
                                                                                var ecash_sql = "Insert Into ecash_creditlimit(state_id,dc_id,customer_type) VALUES ";
                                                                                result.forEach(function(item, index) {
                                                                                    ecash_sql += (result.length - 1 == index) ? '(' + state + ',' + insertId + ',' + item.value + ')' : '(' + state + ',' + insertId + ',' + item.value + '),'
                                                                                });
                                                                                db.query(ecash_sql, {}, function(err, result) {
                                                                                    if (err) {
                                                                                        console.log(err);
                                                                                        return err;
                                                                                    } else {
                                                                                        callback({
                                                                                            res_wh_id: insertId,
                                                                                            beat_id,
                                                                                            spoke_id,
                                                                                            hub_id
                                                                                        });
                                                                                    }
                                                                                });
                                                                            }
                                                                        });
                                                                    }
                                                                })
                                                            }
                                                        })
                                                    }
                                                })
                                            }
                                        })

                                    }
                                })
                            }
                        })
                    }
                });

            }
        });
    },
    saveIntoDCFCMappingTable: function(data, le_id, le_wh_id, callback) {
        var data = JSON.parse(data);
        var leleID = data.legalentityid;
        var sqlQuery = "Insert Into dc_fc_mapping (dc_le_wh_id,dc_le_id,fc_le_wh_id,fc_le_id,status) VALUES ('" + data.dcs + "','" + leleID + "','" + le_wh_id + "','" + le_id + "',1)";
        if (data.DC_FC_id == 1014) {
            db.query(sqlQuery, {}, function(err, result) {
                if (err) {
                    console.log(err);
                    return err;
                } else {
                    callback(result.insertId);
                }
            });
        } else {
            callback(0)
        }
    },
    saveStockistPriceMappingTable: function(le_id, data, callback) {
        var wareHouseQuery = "SELECT * FROM legalentity_warehouses WHERE dc_type=118001 and legal_entity_id = " + le_id;
        db.query(wareHouseQuery, function(err, wareHouseResult) {
            if (err) {
                console.log(err);
                return err;
            } else {
                if (wareHouseResult.length > 0) {
                    let le_wh_id = wareHouseResult[0].le_wh_id;
                    var priceMapping = "Insert Into stockist_price_mapping (legal_entity_id,le_wh_id,stockist_price_group_id) VALUES ('" + le_id + "','" + le_wh_id + "',3014)";
                    module.exports.saveIntoDCFCMappingTable(data, le_id, le_wh_id, function(dc_fc_mapping) {
                        db.query(priceMapping, {}, function(err, priceMappingres) {
                            if (err) {
                                console.log(err);
                                return err;
                            } else {
                                callback(priceMappingres);
                            }
                        });
                    })
                }
            }
        });
    },
    saveStockistIndentConfTable: function(le_id, callback) {
        var leWareQuery = "SELECT le_wh_id FROM legalentity_warehouses WHERE dc_type=118001 and legal_entity_id = " + le_id;
        db.query(leWareQuery, function(err, wareResult) {
            if (err) {
                console.log(err);
                return err;
            } else {
                if (wareResult.length > 0) {
                    let le_wh_id = wareResult[0].le_wh_id;
                    var stIndentQuery = "Insert Into stockist_indent_conf (legal_entity_id,le_wh_id,stock_days,stock_norm) VALUES ('" + le_id + "','" + le_wh_id + "',0,0)";
                    db.query(stIndentQuery, {}, function(err, stIndentQuery) {
                        if (err) {
                            console.log(err);
                            return err;
                        } else {
                            callback(stIndentQuery);
                        }
                    });
                }
            }
        });
    },
    saveIntoBusinessUnitsTable: function(entity_id, data, userId, callback) {
        var data = JSON.parse(data);
        var lp_wh_name = data.business_legal_name;
        var costCenter = data.cost_center;
        var parentBU = data.parent_bu;
        var sql = "Insert Into business_units(bu_name,description,legal_entity_id,is_active,cost_center,parent_bu_id) VALUES ('" + lp_wh_name + " DC BU','" + lp_wh_name + " DC BU','" + entity_id + "',1,'" + costCenter + "','" + parentBU + "')";
        db.query(sql, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                var bu_inserted_id = result.insertId;
                var bu_hub_query = "Insert Into business_units(bu_name,description,legal_entity_id,is_active,cost_center,parent_bu_id) VALUES ('" + lp_wh_name + " HUB BU','" + lp_wh_name + " HUB BU','" + entity_id + "',1,'" + costCenter + "H1','" + bu_inserted_id + "')";
                db.query(bu_hub_query, {}, function(err, parent_result) {
                    if (err) {
                        console.log(err);
                        return err;
                    } else {
                        var query2 = "SELECT bu_id FROM business_units WHERE legal_entity_id = " + entity_id;
                        db.query(query2, {}, function(err, resultQuery) {
                            if (err) {
                                console.log(err);
                                return err;
                            }
                            ret = JSON.parse(JSON.stringify(resultQuery));
                            var i = 1;
                            ret.forEach(function(element) {
                                if (i == 2) var updatesql = "update legalentity_warehouses set bu_id = '" + element.bu_id + "', cost_centre = '" + costCenter + "H1'  where legal_entity_id = '" + entity_id + "' and dc_type=118002";
                                else var updatesql = "update legalentity_warehouses set bu_id = '" + element.bu_id + "', cost_centre = '" + costCenter + "'  where legal_entity_id = '" + entity_id + "' and dc_type=118001";
                                db.query(updatesql, {}, function(err, result) {
                                    if (err) {
                                        console.log('err2');
                                        console.log(err);
                                        return err;
                                    } else {}
                                });
                                i++;
                            });
                            var getBuquery = "select * from business_units where legal_entity_id=" + entity_id;
                            db.query(getBuquery, {}, function(buerr, bures) {
                                if (buerr) {
                                    console.log(buerr);
                                    return buerr;
                                } else {
                                    var insertUserPermsiion = "insert into user_permssion (permission_level_id,user_id,object_id) values (6," + userId + ",?),(6," + userId + ",?)";
                                    db.query(insertUserPermsiion, [bures[0].bu_id, bures[1].bu_id], function(pererr, perres) {
                                        if (pererr) {
                                            return pererr
                                        } else {
                                            var business_units_id = bures[0].bu_id;
                                            var upDateUser = "update users set business_unit_id = '" + business_units_id + "'where legal_entity_id = '" + entity_id + "'";
                                            db.query(upDateUser, {}, function(userError, userResultData) {
                                                if (userError) {
                                                    return userError
                                                } else {

                                                }
                                            });

                                        }
                                    });
                                }
                            })
                            callback(1);
                        });
                    }
                });

            }
        });
    },
    saveIntoUsersTable: function(legal_id, data, callback) {
        try {
            var data = JSON.parse(data);
            var password = "57d2be4ca4e5f73caa4e99b29efd69c7";
            var firstname = data.first_name;
            var lastname = data.last_name;
            var email_id = data.email;
            var mobile_no = data.phone_number;
            var is_active = 1;
            var legal_entity_id = legal_id;
            var sql = "Insert Into users (password,firstname,lastname,email_id,mobile_no,is_active,legal_entity_id,is_parent) VALUES ('" + password + "','" + firstname + "','" + lastname + "','" + email_id + "','" + mobile_no + "','" + is_active + "','" + legal_entity_id + "',1)";
            db.query(sql, {}, function(err, result) {
                if (err) {
                    console.error('Internal Error: ' + err + "\n");
                    response = {
                        "Status": 500,
                        "Message": "Internal Server Error",
                        "ResponseBody": "Error: " + err
                    };
                    callback(response);
                } else {
                    callback(result.insertId);
                }
            });
        } catch (err) {
            console.error('Internal Error: ' + err + "\n");
            response = {
                "Status": 500,
                "Message": "Internal Server Error",
                "ResponseBody": "Error: " + err
            };
            return res.send(response);
        }
    },
    saveIntoUserrolesTable: function(roleid, userid, callback) {
        var role_table_id = roleid.main_role_id;
        var sql = "Insert Into user_roles (role_id,user_id) VALUES ('" + role_table_id + "','" + userid + "'),(74," + userid + ")";
        db.query(sql, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                var updatesql = "update roles set parent_role_id = '117' where role_id = '" + role_table_id + "'";
                db.query(updatesql, {}, function(err, result) {
                    if (err) {
                        console.log(err);
                        return err;
                    } else {
                        callback(result);
                    }
                })
                //callback(result);
            }
        });
    },
    saveIntoRolesTable: function(res, userid, data, callback) {
        var data = JSON.parse(data);
        var legalentity_type_id = data.DC_FC_id;
        if (legalentity_type_id == 1014) {
            var name = data.business_legal_name + " Business Head FC";
        } else {
            var name = data.business_legal_name + " Business Head DC";
        }
        var sql = "Insert Into roles (name,description,legal_entity_id,is_active,created_by) VALUES('" + name + "','" + name + "','" + res + "',1,'" + userid + "')";
        db.query(sql, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                // console.log("parentrole_id " +result.insertId);

                var parentrole_id = result.insertId;
                // console.log(parentrole_id,"roleididididididi");
                var role_name = data.business_legal_name + " DO";
                var rolesTable = "Insert Into roles (name,description,legal_entity_id,is_active,created_by,parent_role_id) VALUES('" + role_name + "','" + role_name + "','" + res + "',1,'" + userid + "','" + result.insertId + "')";

                // console.log(rolesTable,'3453556667777');
                db.query(rolesTable, {}, function(error, rolesTableResult) {
                    if (error) {
                        console.log(error);
                        return error;
                    } else {
                        var arr = {
                            main_role_id: result.insertId,
                            del_role_id: rolesTableResult.insertId
                        };
                        callback(arr);
                    }
                });
            }
        });
    },
    saveIntoRoleAccess: function(roleid, callback) {
        // console.log(roleid,"9948470115");
        var role_id = roleid.main_role_id;
        var sql = "INSERT INTO role_access (role_id,feature_id) SELECT '" + role_id + "' AS role_id, role_access.`feature_id` FROM role_access WHERE role_id=117";
        db.query(sql, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                callback(result.insertId);
            }
        });
    },
    saveRetailerFlat: function(data, id, hub_id, beat_id, spoke_id, callback) {
        var UserData = JSON.parse(data);
        var Name = UserData.first_name + UserData.last_name;
        var mob = UserData.phone_number;
        var typeId = UserData.DC_FC_id;
        var le_query = "select * from legal_entities where legal_entity_id='" + id + "'";
        db.query(le_query, {}, function(err, result) {
            if (err) {
                console.log(err);
                return err;
            } else {
                var jsPass = JSON.stringify(result);
                var leResult = JSON.parse(jsPass);
                var leResult = leResult[0];
                var masData = "SELECT * FROM master_lookup WHERE mas_cat_id=1 AND VALUE='" + typeId + "'";
                db.query(masData, {}, function(err, loopresult) {
                    if (err) {
                        console.log(err);
                        return err;
                    } else {
                        var massPass = JSON.stringify(loopresult);
                        var upResult = JSON.parse(massPass);
                        var upResult = upResult[0];
                        var retailerQuery = "Insert Into retailer_flat (legal_entity_id,le_code,parent_le_id,business_legal_name,legal_entity_type_id,legal_entity_type,business_type_id,business_type,name,mobile_no,address,address1,address2,city,state_id,country,pincode,hub_id,beat_id,spoke_id,area) VALUES ('" + leResult.legal_entity_id + "','" + leResult.le_code + "','" + leResult.parent_le_id + "','" + leResult.business_legal_name + "','" + leResult.legal_entity_type_id + "','" + upResult.master_lookup_name + "','" + leResult.business_type_id + "','" + upResult.description + "','" + Name + "','" + mob + "','" + leResult.address1 + "','" + leResult.address1 + "','" + leResult.address2 + "','" + leResult.city + "','" + leResult.state_id + "','" + leResult.country + "','" + leResult.pincode + "','" + hub_id + "','" + beat_id + "','" + spoke_id + "',0)";
                        db.query(retailerQuery, {}, function(err, insertresult) {
                            if (err) {
                                console.log(err);
                                return err;
                            } else {
                                callback(insertresult.insertId);
                            }
                        });
                    }
                });
            }
        });
    },
    userEcashCreditlimit: function(le_id, user_id, callback) {
        var userID = user_id;
        var leID = le_id;
        var ecashSql = "Insert Into user_ecash_creditlimit (user_id,le_id) VALUES ('" + userID + "','" + leID + "')";
        db.query(ecashSql, {}, function(err, ecashResult) {
            if (err) {
                console.log(err);
                return err;
            } else {
                callback(ecashResult);
            }
        });
    },
    apobIntoWarehouseTable: function(entity_id, data, callback) {
        var data = JSON.parse(data);
        var dcID = data.DC_FC_id;
        var costCenter = data.cost_center;
        var costCenter2 = costCenter.slice(-1);
        var costCenter3 = parseInt(costCenter2) + 1;
        var cost_centre = costCenter.slice(0, -1);
        var costCenter4 = cost_centre + costCenter3;
        var lp_wh_name = data.display_Name;
        var contact_name = data.first_name + ' ' + data.last_name;
        var phone_no = data.phone_number;
        var email = data.email;
        var address1 = data.address;
        var address2 = data.address_2;
        var pincode = data.pincode;
        var state = data.state_id;
        var city = data.city;
        var tin_number = data.gstin_number;
        var parentBU = data.parent_bu;
        var wareHouse = data.Warehouse_Code
        var lastWord = wareHouse.slice(-1);
        var add = parseInt(lastWord) + 1;
        var added = wareHouse.substring(2);
        var apob = 'APOB';
        var apob_Warehouse_Code = apob.concat(added);
        var current_datetime = new Date();
        var firstWord = current_datetime.getFullYear() + "" + ('0' + (current_datetime.getMonth() + 1)).slice(-2);
        var presentday = firstWord.slice(2);
        if (dcID == 1016) {
            var apobSql = "Insert Into business_units(bu_name,description,legal_entity_id,is_active,cost_center,parent_bu_id) VALUES ('" + lp_wh_name + " APOB DC BU','" + lp_wh_name + " APOB DC BU',2,1,'" + costCenter4 + "','" + parentBU + "')";
            db.query(apobSql, {}, function(wrong, apobSqlResult) {
                if (wrong) {
                    console.log(wrong);
                    return wrong;
                } else {
                    var dcBUID = apobSqlResult.insertId;
                    var apobQuery = "Insert Into legalentity_warehouses (legal_entity_id,lp_name,dc_type,lp_wh_name,contact_name,phone_no,email,address1,state,country,pincode,city,lp_id,le_wh_code,display_name,tin_number,address2,bu_id,status,cost_centre,is_apob) VALUES (2,'Custom',118001,'" + lp_wh_name + " APOB','" + contact_name + "','" + phone_no + "','" + email + "','" + address1 + "','" + state + "',99,'" + pincode + "','" + city + "',0,'" + apob_Warehouse_Code + "','" + lp_wh_name + " APOB DC', NULL, '" + address2 + "','" + dcBUID + "',1,'" + costCenter4 + "','1')";
                    db.query(apobQuery, {}, function(Ewrong, apobQuery) {
                        if (Ewrong) {
                            console.log(Ewrong);
                            return Ewrong;
                        } else {
                            var apobHubSql = "Insert Into business_units(bu_name,description,legal_entity_id,is_active,cost_center,parent_bu_id) VALUES ('" + lp_wh_name + " APOB HUB BU','" + lp_wh_name + " APOB HUB BU',2,1,'" + costCenter4 + "H1','" + dcBUID + "')";

                            db.query(apobHubSql, {}, function(erwrong, apobHubSql) {
                                if (erwrong) {
                                    console.log(erwrong);
                                    return erwrong;
                                } else {
                                    var apobHUID = apobHubSql.insertId;
                                    var apobHubQuery = "Insert Into legalentity_warehouses (legal_entity_id,lp_name,dc_type,lp_wh_name,contact_name,phone_no,email,address1,state,country,pincode,city,lp_id,le_wh_code,display_name,tin_number,address2,bu_id,status,cost_centre,is_apob) VALUES (2,'Custom',118002,'" + lp_wh_name + " APOB HUB','" + contact_name + "','" + phone_no + "','" + email + "','" + address1 + "','" + state + "',99,'" + pincode + "','" + city + "',0,'" + apob_Warehouse_Code + " HUB','" + lp_wh_name + " APOB HUB', NULL, '" + address2 + "','" + apobHUID + "',1,'" + costCenter4 + "','1')";
                                    db.query(apobHubQuery, {}, function(erwrong, apobHubQuery) {
                                        if (erwrong) {
                                            console.log(erwrong);
                                            return erwrong;
                                        } else {
                                            var dcMPTable = apobQuery.insertId;
                                            var hubMPTable = apobHubQuery.insertId;
                                            var mappingTableQuery = "Insert Into dc_hub_mapping (legal_entity_id,dc_id,hub_id,is_active,dc_name,hub_name,dc_code,hub_code) VALUES (2," + dcMPTable + "," + hubMPTable + ",1,'" + lp_wh_name + " DC ','" + lp_wh_name + " HUB','" + apob_Warehouse_Code + "','" + apob_Warehouse_Code + " HUB')";
                                            db.query(mappingTableQuery, {}, function(erwrong, mappingTableQuery) {
                                                if (erwrong) {
                                                    console.log(erwrong);
                                                    return erwrong;
                                                } else {
                                                    var dc_warehouse_query = "SELECT le_wh_id FROM `legalentity_warehouses` WHERE legal_entity_id = " + entity_id + " AND dc_type=118001";
                                                    db.query(dc_warehouse_query, {}, function(wareHouseError, wareHouseQueryResult) {
                                                        if (wareHouseError) {
                                                            console.log(wareHouseError);
                                                            return wareHouseError;
                                                        } else {
                                                            var dc_warehouse_id = JSON.stringify(wareHouseQueryResult);
                                                            var jsonResult = JSON.parse(dc_warehouse_id);
                                                            var wareHouseID = jsonResult[0];
                                                            var ID = wareHouseID.le_wh_id;
                                                            var dc_le_wh_id_inserted = apobQuery.insertId;
                                                            var apob_DC_FC_Query = "Insert Into dc_fc_mapping(dc_le_wh_id,dc_le_id,fc_le_wh_id,fc_le_id,status) VALUES ('" + dc_le_wh_id_inserted + "','2','" + ID + "','" + entity_id + "',1)";
                                                            db.query(apob_DC_FC_Query, {}, function(dc_fc_error, result_DC_FC_Query) {
                                                                if (dc_fc_error) {
                                                                    console.log(dc_fc_error);
                                                                    return dc_fc_error;
                                                                } else {
                                                                    var serialNoInvoice = "SELECT COUNT(*)  AS val from  serial_no_invoice where state_code='" + data.state_code + "' AND le_id=2";
                                                                    db.query(serialNoInvoice, {}, function(err, invoice) {
                                                                        if (err) {
                                                                            console.log(err);
                                                                            return err;
                                                                        } else {
                                                                            if (invoice[0].val == 0) {
                                                                                var wh_short_code = "CALL getWhShortCode()";
                                                                                db.query(wh_short_code, {}, function(err, result) {
                                                                                    if (err) {
                                                                                        console.log(err);
                                                                                        return err;
                                                                                    } else {
                                                                                        var insertSerialTable = "Insert Into serial_no_invoice (state_code,le_id,prefix,date,le_code) VALUES ('" + data.state_code + "',2,'IV','" + presentday + "','" + result[0][0].short_code + "')";
                                                                                        db.query(insertSerialTable, {}, function(err, inserQuery) {
                                                                                            if (err) {
                                                                                                return err;
                                                                                            } else {
                                                                                                callback(inserQuery.insertId);
                                                                                            }
                                                                                        });
                                                                                    }
                                                                                });

                                                                            }
                                                                            callback(result_DC_FC_Query.insertId);
                                                                        }
                                                                    });
                                                                }
                                                            });

                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                            /*}
                            })*/
                        }
                    });
                }
            });
        } else {
            callback(1);
        }
    },
    creatingWorkFlowsForUsers: function(entity_id, data, roleid, callback) {
        var data = JSON.parse(data);
        var do_role_id = roleid.del_role_id;
        var bussinessRole_id = roleid.main_role_id;
        var master_lookup = "SELECT * FROM `master_lookup` WHERE VALUE = 173001";
        db.query(master_lookup, {}, function(err, master_lookup_result) {
            if (err) {
                return err;
            } else {
                var workflowId = master_lookup_result[0].description;
                var workflow_status_new = "CALL insertWorkflowStatus('" + workflowId + "','" + entity_id + "','" + do_role_id + "','" + bussinessRole_id + "')";
                db.query(workflow_status_new, {}, function(errstatus, workflow_status) {
                    if (errstatus) {
                        return errstatus;
                    } else {
                        callback(workflow_status);
                    }
                });
            }
        });

    },
    serialNoInvoice: function(entityID, data, callback) {
        var data = JSON.parse(data);
        var current_datetime = new Date();
        var firstWord = current_datetime.getFullYear() + "" + ('0' + (current_datetime.getMonth() + 1)).slice(-2);
        var presentday = firstWord.slice(2);
        var wh_short_code = "CALL getWhShortCode()";
        db.query(wh_short_code, {}, function(err1, result1) {
            if (err1) {
                return err1;
            } else {
                var serial_no_invoice = "Insert Into serial_no_invoice (state_code,le_id,prefix,date,le_code) VALUES ('" + data.state_code + "','" + entityID + "','IV','" + presentday + "','" + result1[0][0].short_code + "')";
                db.query(serial_no_invoice, {}, function(err, result) {
                    if (err) {
                        return err;
                    } else {
                        callback(result);
                    }
                });
            }
        });
    },
}
