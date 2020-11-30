/**
 * ChatController
 *
 * @description :: Server-side logic for managing Ebutor Chat related oprations
 * @help    ();    :: See http://sailsjs.org/#!/documentation/concepts/Controllers
 */
var dateFormat = require('moment-timezone');
var moment = require('moment');
const User = require('../models/User');
const Chat = require('../models/Chat');

module.exports = {
    userGroups: function (req, res) {
        console.log(req.body);
        var data = req.body.data;
        console.log(data);
        var socketId = '';
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {"Status": 400, message: "Please send user token", "ResponseBody": "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: 'failed',message:'User is not Active', ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var user_id = userInfo[0].user_id;
                Chat.getUserGroupsbyId(user_id, function (groups) {
                    if (groups == null || groups.length == 0) {
                        var res_toblast = {Status: 200,status: 'success', user_id: user_id, user_name: userInfo[0].user_name, message: 'No groups found', data: []};
                    } else {
                        var res_toblast = {Status: 200,status: 'success', user_id: user_id, user_name: userInfo[0].user_name, data: groups};
                    }
                    console.log(res_toblast);
                    //sails.sockets.broadcast(socketId,'my_groups', res_toblast);
                    return res.json(200, res_toblast);
                });
            }
        });
    },
    joinUser: function (req, res) {
        var data = req.body.data;
        console.log(data);
        var sockId = '';
        if (data.socket_id) {
            var sockId = data.socket_id;
        }
        if (req.isSocket) {
            console.log('sockid== ' + sails.sockets.getId(req));
            var sockId = sails.sockets.getId(req);
        }
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {"Status": 400,status: 'failed', "message": "Please send user token", "ResponseBody": "Bad Request"};
            return res.json(response);
        }

        User.getUserInfoByToken(user_token, function (userInfo) {
            console.log(userInfo);
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: 'failed',message:'User is not Active', ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                console.log('here');
                if (data.room && sockId !== '') {
                    var room = data.room;
                    var join_type = (data.join_type)?data.join_type:0;
                    if(join_type==1){
                        sails.sockets.leave(sockId, room, function (err) {
                            if (err) {
                                var response = {Status: 201,status: 'failed', message: "Could not Unsubscribe the room", ResponseBody: "Could not Unsubscribe the room"};
                                return res.send(response);
                            }
                            var response = {Status: 200,status: 'success', message: "Successfully Unsubscribed to group " + room, ResponseBody: "Successfully Unsubscribed to group " + room};
                            console.log(response);
                            return res.send(response);
                        });
                    }else{
                        sails.sockets.join(sockId, room, function (err) {
                            if (err) {
                                var response = {Status: 201,status: 'failed', message: "Could not join the room", ResponseBody: "Could not join the room"};
                                return res.send(response);
                            }
                            var response = {Status: 200,status: 'success', message: "Successfully subscribed to group " + room, ResponseBody: "Successfully subscribed to group " + room};
                            console.log(response);
                            return res.send(response);
                        });
                    }
                } else {
                    var response = {Status: 201,status: 'failed', message: "Room/Socket id should not be empty", ResponseBody: "Room/Socket id should not be empty"};
                    console.log(response);
                    return res.send(response);
                }
            }
        });
    },
    chatServer: function (req, res) {
        console.log('1');
        console.log(req.body);
        var data = req.body.data;
        //console.log(req.file('upload_file')._files[0].stream.filename); 
        //var filename = req.file('upload_file')._files[0].stream.filename;
        //var filename = req.file('upload_file')._files[0].stream.path;
        var s3_data = sails.config.s3.aws_s3;
        data = JSON.parse(data);
        console.log('2');
        console.log(data);
        console.log('s3_data');
        console.log(s3_data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            console.log('3');
            console.log(userInfo);
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var res_toblast = {message: data.message, room: '',group:data.group, user_id: userInfo[0].user_id, user_name: userInfo[0].user_name, parent_ticket_id: '', parent_message: '', parent_msg_user: '', parent_user_id: '', parent_picture: '', picture: '',myticket_reply:'',mobile_no:userInfo[0].mobile_no};
                if (data.room && data.room != '') {
                    console.log('4');
                    console.log(data.room);
                    res_toblast['room'] = data.room;
                }
                var ticket_data = {parent_id: 0, comments: data.message, feedback_group_type: data.group, feedback_type: data.type, legal_entity_id: userInfo[0].legal_entity_id, created_by: userInfo[0].user_id};
                if (data.parent_id && data.parent_id != '') {
                    ticket_data['parent_id'] = data.parent_id;
                }
                if (data.myticket_reply) {
                    res_toblast['myticket_reply'] = data.myticket_reply;
                }
                if (!data.message_type || data.message_type == '') {
                    var response = {Status: 201,status: "failed", message: "Message Type should not be empty", ResponseBody: "Message Type should not be empty"};
                    return res.send(response);
                }
                if (!data.ticket_type || data.ticket_type == '') {
                    var response = {Status: 201,status: "failed", message: "Ticket Type should not be empty", ResponseBody: "Ticket Type should not be empty"};
                    return res.send(response);
                }
                ticket_data['message_type'] = data.message_type;
                res_toblast['message_type'] = data.message_type;
                ticket_data['ticket_type'] = data.ticket_type;
                res_toblast['ticket_type'] = data.ticket_type;
                ticket_data['main_ticket_id'] = (data.main_ticket_id)?data.main_ticket_id:0;
                res_toblast['main_ticket_id'] = (data.main_ticket_id)?data.main_ticket_id:0;
                ticket_data['ticket_status'] = 157001;
                res_toblast['ticket_status'] = 157001;
                res_toblast['status_name'] = 'Open';
                if(data.message_type==5){
                    ticket_data['picture'] = (data.location)?data.location:'';
                    res_toblast['picture'] = (data.location)?data.location:'';
                }
                req.file('upload_file').upload({
                    adapter: require('skipper-better-s3'),
                    key: s3_data.key,
                    secret: s3_data.secret,
                    bucket: s3_data.bucket,
                    region: s3_data.region,
                    dirname: 'feedback',
                    saveAs: function (file, handler) {
                        var d = new Date();
                        var extension = file.filename.split('.').pop();
                        // generating unique filename with extension
                        var filename = d.getTime() + "." + extension;
                        handler(null, filename);
                    }
                }, function (err, filesUploaded) {
                    if (err) {
                        console.log('s3 errorr');
                        //return res.json(500, err);
                        return res.json(200, {Status: 201,status: "failed", message: "Unable to Upload File", ResponseBody: ""});
                    } else if (filesUploaded.length === 0) {
                        console.log('5');
                        Chat.sendMessage(ticket_data, res_toblast, function (res) {
                        });
                        res_toblast['Status'] =200;
                        res_toblast['status'] ='success';
                        res_toblast['Msg'] ="Message sent successfully";
                        res_toblast['Body'] =[];
                        return res.json(200, res_toblast);
                    } else {
                        console.log('6');
                        console.log(filesUploaded[0].extra.Location);
                        if (filesUploaded[0].extra.Location) {
                            console.log('urlll');
                            ticket_data['picture'] = filesUploaded[0].extra.Location;
                            res_toblast['picture'] = filesUploaded[0].extra.Location;
                        }
                        Chat.sendMessage(ticket_data, res_toblast, function (res) {
                        });
                        res_toblast['Status'] =200;
                        res_toblast['status'] ='success';
                        res_toblast['Msg'] ="Message sent successfully";
                        res_toblast['Body'] =[];
                        return res.json(200, res_toblast);
                    }
                });
            }
        });
    },
    ticketHistory: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        //data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            console.log(userInfo);
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                //if (data.group && data.group != '') {
                    var group = (data.group)?data.group:0;
                    var my_tickets = (data.my_tickets)?data.my_tickets:0;
                    var user_id = userInfo[0].user_id;
                    var limit = (data.limit) ? data.limit : 10;
                    var offset = (data.offset) ? data.offset : 0;

                    // Optional Single Ticket ID
                    var single_ticket_id = (data.single_ticket_id)? data.single_ticket_id : -1;
                    var param = {group: group, limit: limit, offset: offset,my_tickets:my_tickets,user_id:user_id,single_ticket_id:single_ticket_id};
                    if(my_tickets==0){
                        Chat.updateUserUnread(user_id,group, function (ticket_result) { });
                    }
                    Chat.getTickets(param, function (ticket_result) {
                        if (ticket_result == null || ticket_result.length == 0) {
                            return res.json(200, {Status: 200,status: "success", message: "No history found", ResponseBody: "", data: []});
                        } else {
                            Chat.getTicketCount(param, function (ticket_count) {
                                var ticket_total = (ticket_count[0].ticket_total) ? ticket_count[0].ticket_total : 0;
                                return res.json(200, {Status: 200,status: "success", message: "Success", ResponseBody: "", Total: ticket_total, data: ticket_result});
                            });
                        }
                    });
                //} else {
                  //  return res.json(200, {Status: 201, Message: "Please send group", ResponseBody: ""});
                //}

            }
        });
    },
    groupUsers: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            console.log(userInfo);
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var user_id = userInfo[0].user_id;
                var group_id = (data.group_id)?data.group_id:0;
                if(group_id>0 && group_id>0){
                    var user_data = {group_id: group_id, user_id: user_id};
                    Chat.userListByGroup(user_data, function (users) {
                        var response = {Status: 200,status: "success", message: "Group User List", data: users};
                        return res.send(response);
                    });
                }else{
                    var response = {Status: 201,status: "failed", message: "Invalid Ticket/User Id", ResponseBody: ""};
                    return res.send(response);
                }
            }
        });
    },
    assignTicket: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            console.log(userInfo);
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var user_id = userInfo[0].user_id;
                var ticket_id = (data.ticket_id)?data.ticket_id:0;
                var assigned_to = (data.assigned_to)?data.assigned_to:[];
                console.log(assigned_to);
                assigned_to = assigned_to.split(',');
                console.log(assigned_to);
                if(ticket_id>0 && assigned_to.length>0){
                    assigned_text='[';
                    for (var i = 0; i < assigned_to.length; i++) {
                        assigned_text +='{"id":'+ assigned_to[i] +'},';
                    }
                    assigned_text=assigned_text.slice(0, -1);
                    assigned_text+=']';
                    var ticket_data = {assigned_to: assigned_text,assigned_by:user_id, fid: ticket_id};
                    Chat.updateTicket(ticket_data, function (chat_res) {
                        Chat.getTicketDetails(ticket_id, function (ticketdata) {
                            var res_toblast = {
                                ticket_id: ticketdata[0].ticket_id,
                                ticket_type: ticketdata[0].ticket_type,
                                main_ticket_id: ticketdata[0].main_ticket_id,
                                ticket_status: ticketdata[0].ticket_status,
                                status_name: ticketdata[0].status_name,
                                message: ticketdata[0].comments,
                                room: ticketdata[0].feedback_group_type,
                                group: ticketdata[0].feedback_group_type,
                                user_id: ticketdata[0].created_by,
                                user_name: ticketdata[0].user_name,
                                ticket_date: ticketdata[0].ticket_date,
                                parent_ticket_id: '',
                                assigned_msg: 'yes',
                                parent_message: '', 
                                parent_message_type: '',
                                parent_picture: '',
                                parent_msg_user: '', 
                                parent_user_id: '',
                                picture: ticketdata[0].picture,
                                message_type: ticketdata[0].message_type
                            };
                            for (var i = 0; i < assigned_to.length; i++) {
                                console.log('asign message');
                                res_toblast['room'] = assigned_to[i];
                                sails.sockets.broadcast(assigned_to[i], 'new_message', res_toblast);
                            }
                        });
                    });                    
                    var response = {Status: 200,status: "success", message: "Ticket Assigned Successfully", ResponseBody: ""};
                    return res.send(response);
                }else{
                    var response = {Status: 201,status: "failed", message: "Invalid Ticket/User Id", ResponseBody: ""};
                    return res.send(response);
                }
            }
        });
    },
    changeTicketStatus: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var ticket_id = (data.ticket_id)?data.ticket_id:0;
                var status = (data.status)?data.status:'';
                if(ticket_id>0 && status!=''){
                    var ticket_data = {ticket_status: status, fid: ticket_id};
                    Chat.updateTicketStatus(ticket_data, function (chat_res) {
                    });                    
                    var response = {Status: 200,status: "success", message: "Ticket Status Updated Successfully", ResponseBody: ""};
                    return res.send(response);
                }else{
                    var response = {Status: 201,status: "failed", message: "Invalid Ticket/Status", ResponseBody: ""};
                    return res.send(response);
                }
            }
        });
    },
    getTicketsByStatus: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var group = (data.group)?data.group:0;
                var status = (data.status) ? data.status : 0;
                var user_id = userInfo[0].user_id;
                var limit = (data.limit) ? data.limit : 10;
                var offset = (data.offset) ? data.offset : 0;
                var param = {group: group, limit: limit, offset: offset, user_id: user_id,status:status};
                Chat.getTicketsByStatus(param, function (ticket_result) {
                    console.log(ticket_result);
                    if (ticket_result == null || ticket_result.length == 0) {
                        return res.json(200, {Status: 200,status: "success", message: "No history found", ResponseBody: "", data: []});
                    } else {
                       // Chat.getTicketCount(param, function (ticket_count) {
                       //     var ticket_total = (ticket_count[0].ticket_total) ? ticket_count[0].ticket_total : 0;
                            return res.json(200, {Status: 200,status: "success", message: "Success", ResponseBody: "", Total: 0, data: ticket_result});
                        //});
                    }
                });
            }
        });
    },
    getOptions: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var type = (data.type)?data.type:0;
                var exclude_option = (data.exclude_option)?data.exclude_option:'';
                var param = {type:type,exclude_option:exclude_option};
                Chat.getMasterlookupByCat(param, function (options) {
                    if (options == null || options.length == 0) {
                        return res.json(200, {Status: 200,status: "success", message: "No options found", ResponseBody: "", data: []});
                    } else {
                        return res.json(200, {Status: 200,status: "success", message: "Success", ResponseBody: "", Total: 0, data: options});
                    }
                });
            }
        });
    },
    updateReadStatus: function (req, res) {
        console.log(req.body.data);
        var data = req.body.data;
        data = JSON.parse(data);
        if (data.user_token) {
            var user_token = data.user_token;
        } else {
            response = {Status: 400,status: "failed", message: "Please send user token", ResponseBody: "Please send user token"};
            return res.json(response);
        }
        User.getUserInfoByToken(user_token, function (userInfo) {
            if (userInfo == null || userInfo.length == 0) {
                var response = {Status: 201,status: "session",message:"You have already logged into the Ebutor System", ResponseBody: "Invalid user token"};
                return res.send(response);
            } else if (userInfo[0].is_active == 0) {
                var response = {Status: 201,status: "failed", message: "User is not Active", ResponseBody: "User is not Active"};
                return res.send(response);
            } else {
                var ticket_id = (data.ticket_id)?data.ticket_id:0;
                console.log(ticket_id);
                var user_id = userInfo[0].user_id;
                if(ticket_id>0){
                    Chat.updateTicketRead(ticket_id,user_id,function(read_json){
                    });
                    var response = {Status: 200,status: "success", message: "Ticket Read Status Updated Successfully", ResponseBody: ""};
                    return res.send(response);
                }else{
                    var response = {Status: 201,status: "failed", message: "Invalid Ticket", ResponseBody: ""};
                    return res.send(response);
                }
            }
        });
    },
    chatClient: function (req, res) {
        var room = '';
        var user_token = '';
        if (req.param('room')) {
            room = req.param('room');
            //user_token = req.param('user_token');
        }
        user_token = req.param('user_token');
        console.log(user_token);
        //return res.send("The room is: " + room);
        return res.view('chat', {room: room, user_token: user_token});
    },
};