/**
 * User.js
 *
 * @description :: TODO: You might write a short summary of how this model works and what it represents here.
 * @docs        :: http://sailsjs.org/documentation/concepts/models-and-orm/models
 */
var dateFormat = require('moment-timezone');
var moment = require('moment');
const db = require('../../dbConnection');

module.exports = {
    getUserGroupsbyId: function (user_id, callback) {
        var sql = "SELECT *,getMastLookupValue(ug.group_id) as grp_name"
            sql +=" ,(SELECT comments FROM chat_tickets as cf where cf.feedback_group_type=ug.group_id ORDER BY fid DESC LIMIT 0,1) as last_msg";
            sql +=" ,(SELECT count(fid) FROM chat_tickets as cf where cf.feedback_group_type=ug.group_id and JSON_CONTAINS(cf.read_json->'$[*].user_"+user_id+"',json_array(0)) LIMIT 0,1) as un_msg_count";
            sql +=" FROM chat_user_groups as ug where ug.user_id = ? ";
            console.log(sql);
            console.log(user_id);
        db.query(sql, [user_id], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    sendMessage:function(ticket_data,res_toblast,res){
        console.log("new messgae send");    
        console.log(res_toblast.group);
        if(res_toblast.room && res_toblast.room!=''){
            module.exports.saveTicket(ticket_data, function (chat_res) {
                //var created_date=moment().format('YYYY-MM-DD H:mm:ss');
                var created_date = dateFormat().tz("Asia/Kolkata").format("YYYY-MM-DD HH:mm:ss");
                console.log('Created at ' + created_date + "\n");
                res_toblast['ticket_id'] = chat_res.insertId;
                res_toblast['ticket_date'] = created_date;
                if (ticket_data.parent_id && ticket_data.parent_id != '' && ticket_data.parent_id != 0) {
                    module.exports.getTicketDetails(ticket_data.parent_id, function (parentdata) {
                        console.log('parent ticket data');
                        console.log(parentdata);
                        res_toblast['parent_ticket_id'] = parentdata[0].ticket_id;
                        res_toblast['parent_ticket_type'] = parentdata[0].ticket_type;
                        res_toblast['parent_message'] = parentdata[0].comments;
                        res_toblast['parent_picture'] = parentdata[0].picture;
                        res_toblast['parent_message_type'] = parentdata[0].message_type;
                        res_toblast['parent_msg_user'] = parentdata[0].user_name;
                        res_toblast['parent_status_name'] = parentdata[0].status_name;
                        res_toblast['parent_user_id'] = parentdata[0].created_by;
                        console.log('inside details');
                        console.log(res_toblast);
                        sails.sockets.broadcast(res_toblast.room, 'new_message', res_toblast);
                        //notification for group persons
                        module.exports.sendNotificationTogroupIds(res_toblast.group,ticket_data.created_by,ticket_data.picture,res_toblast.message_type, function(noti_res){
                            console.log("response1");
                            console.log("--------\n");
                            console.log(noti_res);
                            var userDataList = [];
                            for (var i = 0; i < noti_res.length; i++) {
                               userDataList.push(noti_res[i].registration_id);
                            }
                            console.log(userDataList);
                            module.exports.pushnotification(userDataList,res_toblast.message,res_toblast.group,res_toblast.mobile_no,noti_res[0].master_lookup_name,ticket_data.picture,res_toblast.message_type,function(res){

                            });

                        });
                        console.log('ticket data');
                        console.log(ticket_data);
                        if(res_toblast.myticket_reply && res_toblast.myticket_reply==1){
                            res_toblast['room'] = ticket_data.created_by;
                            console.log('my ticket reply');
                            console.log(ticket_data.created_by);
                            sails.sockets.broadcast(ticket_data.created_by, 'new_message', res_toblast);
                            
                            console.log('my ticket message broadcast completed');
                        }
                    });
                } else {
                    console.log('outside details');
                    console.log(res_toblast);
                    console.log("after all");
                    console.log(res_toblast.message);
        console.log("-------------------------------\n");
                    sails.sockets.broadcast(res_toblast.room, 'new_message', res_toblast);
                    //notification for group persons

                    module.exports.sendNotificationTogroupIds(res_toblast.group,ticket_data.created_by,res_toblast.picture,res_toblast.message_type,function(noti_res){
                    
                            console.log("response");
                            console.log("--------\n");
                            console.log(noti_res);  
                            var userDataList = [];
                            for (var i = 0; i < noti_res.length; i++) {
                
                               userDataList.push(noti_res[i].registration_id);
                            }
                            console.log(userDataList);
                            module.exports.pushnotification(userDataList,res_toblast.message,res_toblast.group,res_toblast.mobile_no,noti_res[0].master_lookup_name,res_toblast.picture,res_toblast.message_type,function(res){

                            });

                        });
                }
            });
        }else{
            sails.sockets.blast('new_message', res_toblast); 
        }
    },
    saveTicket: function (ticket_data, callback) {
        module.exports.getGroupUsers(ticket_data.feedback_group_type, function (users) {
            console.log('group users');
            console.log(users);
            read_json = '';
            if (ticket_data.feedback_group_type != '' && users.length > 0) {
                read_json = '[';
                for (var i = 0; i < users.length; i++) {
                    read_json += '{"user_'+ users[i].user_id + '":0},';
                }
                read_json = read_json.slice(0, -1);
                read_json += ']';
            }
            if(read_json!='')
            ticket_data['read_json']= read_json;
            var sql = "INSERT INTO chat_tickets SET ?";
            db.query(sql, ticket_data, function (err, result) {
                if (err) {
                    sails.log(err);
                    return err;
                }
                callback(result);
            });
        });
    },
    updateUserUnread: function (user_id,group, callback) {
        var sql = "SELECT fid FROM chat_tickets as cf where cf.feedback_group_type=? and JSON_CONTAINS(cf.read_json->'$[*].user_"+user_id+"',json_array(0))"
        console.log(sql);
        db.query(sql, [group], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            if (result.length > 0) {
                for (var i = 0; i < result.length; i++) {
                    console.log(result[i].fid);
                    module.exports.updateTicketRead(result[i].fid,user_id, function (ticket_result){ });
                }
            }
        });
    },
    updateTicketRead: function (ticket_id,user_id, callback) {
        module.exports.getTicketReadStatus(ticket_id, function (read_json) {
            read_json = JSON.parse(read_json[0].read_json);
            console.log(read_json); 
            newread_json = '';
            if (read_json.length > 0) {
                newread_json = '[';
                for (var i = 0; i < read_json.length; i++) {
                    var key = Object.keys(read_json[i])[0];
                    console.log(key);
                    var key_arr = key.split("_");
                    console.log(key_arr);
                    console.log(key_arr[1]);
                    var json_usrId = (key_arr[1])?key_arr[1]:'';
                    is_read = (json_usrId == user_id) ? 1 : read_json[i][key];
                    newread_json += '{"user_'+json_usrId + '":' + is_read + '},';
                }
                newread_json = newread_json.slice(0, -1);
                newread_json += ']';
                console.log(newread_json);
                console.log(ticket_id);
                var sql = "UPDATE chat_tickets SET read_json=? where fid=? ";
                console.log(sql);
                db.query(sql, [newread_json,ticket_id], function (err, result) {
                    if (err) {
                        sails.log(err);
                        return err;
                    }
                    callback(result);
                });
            }
        });
    },
    getGroupUsers: function (group, callback) {
        var sql = "SELECT user_id";
            sql +=" FROM chat_user_groups as cug WHERE cug.group_id = ?";
        console.log(group); 
        console.log(sql);
        db.query(sql, [group], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    updateTicket: function (data, callback) {
        var sql = "Update chat_tickets SET assigned_to=?,assigned_by=? WHERE fid=? ";
        console.log(sql);
        console.log(data.assigned_to);
        db.query(sql, [data.assigned_to,data.assigned_by,data.fid], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    updateTicketStatus: function (data, callback) {
        var sql = "Update chat_tickets SET ticket_status=? WHERE fid=? OR main_ticket_id=?";
        console.log(sql);
        console.log(data.ticket_status);
        db.query(sql, [data.ticket_status,data.fid,data.fid], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getTicketDetails: function (ticket_id, callback) {
        var sql = "SELECT fid as ticket_id,parent_id,main_ticket_id,ticket_type,ticket_status,feedback_group_type,feedback_type,comments,picture,";
            sql +="message_type,created_by,created_at,DATE_FORMAT(cf.created_at,'%Y-%m-%d %H:%i:%s') as ticket_date,";
            sql +=" GetUserName(cf.created_by,2) as user_name ,GetUserName(JSON_EXTRACT(cf.assigned_to, '$[0].id'),2) as assigned_name,GetUserName(cf.assigned_by,2) as assigned_by_name,getMastLookupValue(cf.ticket_status) as status_name ";
            sql +=" FROM chat_tickets as cf WHERE fid = ? LIMIT 0,1";
        console.log(ticket_id); 
        console.log(sql);
        db.query(sql, [ticket_id], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getTicketReadStatus: function (ticket_id, callback) {
        var sql = "SELECT read_json";
            sql +=" FROM chat_tickets as cf WHERE fid = ? LIMIT 0,1";
        console.log(ticket_id); 
        console.log(sql);
        db.query(sql, [ticket_id], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    userListByGroup: function (data, callback) {
        var sql = "SELECT users.user_id,IF(users.user_id=?,'Me',GetUserName(users.user_id,2)) as user_name,users.mobile_no FROM chat_user_groups cug JOIN users on users.user_id=cug.user_id WHERE cug.group_id = ?";
        console.log(sql);
        db.query(sql, [data.user_id,data.group_id], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getTickets: function (param, callback) {
        var sql = "SELECT cf.fid as ticket_id,cf.parent_id,cf.feedback_group_type as room,cf.feedback_group_type as `group`,cf.feedback_type,cf.comments as message,cf.picture,cf.created_by as user_id,DATE_FORMAT(cf.created_at,'%Y-%m-%d %H:%i:%s') as ticket_date,";
        sql +="GetUserName(cf.created_by,2) as user_name,GetUserName(JSON_EXTRACT(cf.assigned_to, '$[0].id'),2) as assigned_name,GetUserName(cf.assigned_by,2) as assigned_by_name, cf.message_type,cf.picture,cf.main_ticket_id,cf.ticket_type,cf.ticket_status,getMastLookupValue(cf.ticket_status) as status_name,";
        sql +=" pcf.fid AS parent_ticket_id,pcf.comments AS parent_message, GetUserName(pcf.created_by,2) as parent_msg_user,";
        sql +=" pcf.created_by AS parent_user_id,pcf.message_type AS parent_message_type,pcf.picture AS parent_picture,pcf.main_ticket_id AS parent_main_ticket_id,";
        sql += " pcf.ticket_type as parent_ticket_type,pcf.ticket_status as parent_ticket_status,getMastLookupValue(pcf.ticket_status) as parent_status_name ";
        sql +=" FROM chat_tickets as cf  LEFT JOIN chat_tickets AS pcf ON cf.parent_id=pcf.fid ";
        sql +="WHERE ";
        if(typeof param.single_ticket_id !== 'undefined' && param.single_ticket_id != -1){
            sql += " and cf.parent_id = ? "; 
            val.push(param.single_ticket_id);
        }
        var val = [];
        if(param.my_tickets==1){
            sql += " JSON_CONTAINS(cf.assigned_to->'$[*].id',json_array(?))";
            sql += " OR ( cf.main_ticket_id IN ( SELECT acf.fid FROM chat_tickets as acf WHERE JSON_CONTAINS(acf.assigned_to->'$[*].id',json_array(?)) ) )";
            val = [param.user_id];
            val.push(param.user_id);
        }else if(param.my_tickets==2){
            sql += " cf.assigned_by=?";
            val = [param.user_id];
        }else{
            sql += "cf.feedback_group_type = ?"; 
            val = [param.group];
        }
        sql += " Order By cf.fid DESC ";
        sql +="LIMIT "+(param.offset)+","+param.limit;
        console.log(sql);
        console.log(val);
        db.query(sql, val, function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getTicketsByStatus: function (param, callback) {
        var sql = "SELECT cf.fid as ticket_id,cf.parent_id,cf.main_ticket_id,cf.ticket_type,cf.ticket_status,cf.feedback_group_type as room,cf.feedback_type,cf.comments as message,cf.picture,cf.created_by as user_id,DATE_FORMAT(cf.created_at,'%Y-%m-%d %H:%i:%s') as ticket_date,";
        sql +=" GetUserName(cf.created_by,2) as user_name, cf.message_type,cf.picture ";
        sql +=" FROM chat_tickets as cf ";
        sql +="WHERE ";
        sql += "cf.ticket_status = ? and feedback_group_type = ? ";
        sql += " Order By cf.fid DESC ";
        //sql +="LIMIT "+(param.offset)+","+param.limit;
        console.log(sql);
        console.log(param);
        db.query(sql, [param.status,param.group], function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getMasterlookupByCat: function (param, callback) {
        var sql = "SELECT ml.mas_cat_id,ml.master_lookup_name,ml.value,ml.description ";
        sql +=" FROM master_lookup as ml ";
        sql +=" JOIN master_lookup_categories as mlc on mlc.mas_cat_id=ml.mas_cat_id ";
        sql +="WHERE ";
        sql += "mlc.mas_cat_name = ? ";
        var val=[param.type];
        if(param.exclude_option!=''){
            sql += " AND ml.value != ? ";
            val.push(param.exclude_option);
        }
        //sql +="LIMIT "+(param.offset)+","+param.limit;
        console.log(sql);
        console.log(val);
        db.query(sql, val, function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },
    getTicketCount: function (param, callback) {
        var sql = "SELECT COUNT(cf.fid) as ticket_total ";
        sql +=" FROM chat_tickets as cf  ";
        sql +="WHERE ";
        if(param.single_ticket_id!=-1){
            sql += " cf.parent_id = "+param.single_ticket_id+" and "; 
        }
        var val = [];
        if(param.my_tickets==1){
            sql += " JSON_CONTAINS(cf.assigned_to->'$[*].id',json_array(?))";
            sql += " OR ( cf.main_ticket_id IN ( SELECT acf.fid FROM chat_tickets as acf WHERE JSON_CONTAINS(acf.assigned_to->'$[*].id',json_array(?)) ) )";
            val = [param.user_id];
            val.push(param.user_id);
        }else if(param.my_tickets==2){
            sql += " cf.assigned_by=?";
            val = [param.user_id];
        }else{
            sql += "cf.feedback_group_type = ?"; 
            val = [param.group];
        }
        console.log(sql);
        db.query(sql, val, function (err, result) {
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);
        });
    },

    sendNotificationTogroupIds:function(groupid,createdby,picture,messgetype,callback){
        var sql = "SELECT dd.registration_id,ml.master_lookup_name FROM chat_user_groups AS cug JOIN device_details AS dd ON dd.user_id=cug.user_id JOIN master_lookup as ml on ml.value=cug.group_id WHERE cug.group_id = ? AND cug.user_id!=?";
        
        db.query(sql, [groupid,createdby],function (err, result) {
            console.log(result);
            if (err) {
                sails.log(err);
                return err;
            }
            callback(result);

        });


    },
    pushnotification:function(ids,message,groupid,mobileno,name,picture,messagetype){
        sails.services.pushnotification.sendGCMNotification(ids, {
            data         : {
             groupid:groupid,
             image  :picture,
             messagetype:messagetype,
            },
            notification: {
              title: "Ebutor"+' - '+name,
              icon: "ic_launcher",
              body: mobileno + ':'  +message
            }
          }, true, function (err, results)
          {

            console.log(err, results);
          });
          
    },
};

