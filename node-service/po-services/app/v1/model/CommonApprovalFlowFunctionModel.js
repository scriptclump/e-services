const Sequelize = require('sequelize');
var sequelize = require('../../config/sequelize');
var express = require('express');
var router = express.Router();
var auth = require('../middleware/auth');
var database = require('../../config/mysqldb');
let db = database.DB;
let con = db;
let nodeMailer = require('nodemailer');

let current_datetime = new Date();
let presentday = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds();

module.exports={

	storeWorkFlowHistory:async function(flowType,flowTypeForID,currentStatusID,nextStatusId,flowComment,userID,new_title='')
	{
		return new Promise(async(resolve,reject)=>{
			try
			{
				userID=30;
				var legalEntiryID = await module.exports.getUserLegalEntity(userID).catch(err=>{console.log(err)});
				var flow_for_id = await module.exports.getFlowForID(flowType).catch(err=>{console.log(err)});//56015

				var nextLblRole = await module.exports.getNextLblRole(flow_for_id,nextStatusId,legalEntiryID).catch(err=>{console.log(err)});
				var nextLblRoleIDForHubData = [];
				await Promise.all(nextLblRole.map(async function(value){
					if(value.hub_data==1)
					{
						nextLblRoleIDForHubData.push(value.applied_role_id);
					}
				}));

				nextLblRoleIDForHubData=nextLblRoleIDForHubData.filter(function(elem, pos) {
								    return nextLblRoleIDForHubData.indexOf(elem) == pos;
								});
				var nextLblRoleID = [];

				await Promise.all(nextLblRole.map(async function(value){
					if(value.hub_data==1)
					{
						nextLblRoleID.push(value.applied_role_id)
					}
				}));

				nextLblRoleID=nextLblRoleID.filter(function(elem, pos) {
								    return nextLblRoleID.indexOf(elem) == pos;
								});
				var currentStatusData = await module.exports.getcurrentStatusDataForWorkFlowId(flow_for_id,currentStatusID,nextStatusId,legalEntiryID).catch(err=>{console.log(err)});
				var isFinalFlag = (currentStatusData.length>0)?currentStatusData[0].is_final:0;
				var conditionID = (currentStatusData.length>0)?currentStatusData[0].awf_condition_id:0;

				var getpreviousHistoryID= await module.exports.getPreviousHistoryID(flowTypeForID,flow_for_id).catch(err=>{console.log(err)});
				var previousHistoryID = (getpreviousHistoryID.length>0)?getpreviousHistoryID.awf_history_id:0;

				await module.exports.apprWorkflowHistoryUpdateisFinal(previousHistoryID).catch(err=>{console.log(err)});

				var ticketCrID = (getpreviousHistoryID.length>0)?getpreviousHistoryID.ticket_created_by:0;
				var ticketCrMgrID = (getpreviousHistoryID.length>0)?getpreviousHistoryID.created_by_manager:0;

				if(flow_for_id == 56022 || flow_for_id == 56024)
				{
					var get_role_id = await module.exports.getRoleId().catch(err=>{console.log(err)});
					if(currentStatusID != 57078 && ticketCrMgrID>0)
					{
						var checkTicket = module.exports.getApprovalWorkFlowTicketCount(ticketCrMgrID,get_role_id.role_id,flow_for_id).catch(err=>{console.log(err)});
						if(checkTicket.hasOwnproperty('checkTicket') && checkTicket.count==0){
							var unset_manager_role=await module.exports.unsetManagerRole(ticketCrMgrID,get_role_id.role_id).catch(err=>{console.log(err)});							
						}
					}
				}
				var datatosave 								= [];
				datatosave['awf_for_type']                  = flowType;
	            datatosave['awf_for_type_id']               = flow_for_id;
	            datatosave['awf_for_id']                    = flowTypeForID;
	            datatosave['awf_comment']		            = flowComment;
	            datatosave['status_from_id']	            = currentStatusID;
	            datatosave['status_to_id']		            = nextStatusId;
	            datatosave['user_id']			            = userID;
	            datatosave['next_lbl_role']                 = nextLblRoleID.join(',');
	            datatosave['is_final']                      = isFinalFlag;
	            datatosave['condition_id']                  = conditionID;
	            datatosave['ticket_created_by']             = ticketCrID;
	            datatosave['created_by_manager']            = ticketCrMgrID;
	            datatosave['created_by']		            = userID;

				var datatosaveapprHstry = await module.exports.dataToSaveInApprWorkFlowHstry(datatosave).catch(err=>{console.log(err)}); 
				if(datatosaveapprHstry)
				{
						if(flow_for_id==56017)
						{
							var split_dataToSave=datatosave['nextStatusId'].split(',');
							datatosave['status_to_id'] = split_dataToSave[0];
						}
						await module.exports.saveWorkflowModuleWise(datatosave,flowTypeForID,flow_for_id).catch(err=>{console.log(err)});

						var getApprovalDetails = await module.exports.getApprovalDetails(flow_for_id,legalEntiryID).catch(err=>{console.log(err)});

						var emailFlag = 0;
						var notificaionFlag = 0;
						var mobileNotifyFlag = 0;
						var redirectURL = "/approvalworkflow/approvalticket";

						if(getApprovalDetails)
						{
							emailFlag	=	getApprovalDetails[0].awf_email;
							notificaionFlag = getApprovalDetails[0].awf_notification;
							redirectURL = getApprovalDetails[0].redirect_url;
							// console.log('before');
							// console.log(redirectURL);
							redirectURL = redirectURL.replace('##',flowTypeForID);
							// console.log('after');
							// console.log(redirectURL);
							mobileNotifyFlag = getApprovalDetails[0].awf_mobile_notification; 
						}

						var getFirstRecord = await module.exports.getFirstRecordForNextRole(flow_for_id,nextStatusId,flowTypeForID).catch(err=>{ console.log(err)});
						var toEmails = [];
						var userIDs = [];

						var roleDetails = await module.exports.getroleDetails(getFirstRecord.next_lbl_role).catch(err=>{ console.log(err)});
						if(roleDetails)
						{
							if(roleDetails.name == 'ImmediateReporter')
							{
								var getUserForMail = await module.exports.getTicketCrUser(ticketCrMgrID).catch(err=>{ console.log(err)});

								if(getUserForMail.hasOwnproperty('email_id'))
								{
									toEmails.push(getUserForMail.email_id);
									userIDs.push(getUserForMail.user_id);
								}
							
							}else if(roleDetails.name == 'Initiator')
							{
								var getUserForMail = await module.exports.getTicketCrUser(ticketCrID).catch(err=>{ console.log(err)});

								if(getUserForMail.hasOwnproperty('email_id'))
									{
										toEmails.push(getUserForMail.email_id);
										userIDs.push(getUserForMail.user_id);
									}
							}else{

								if(nextLblRoleIDForHubData.length>0 && ticketCrID >0)
								{
									var userInformation = await module.exports.getHubWiseMailByRole(nextLblRoleIDForHubData,ticketCrID).catch(err=>{ console.log()});
								}else{
									var userInformation = await module.exports.getUserInformationByNxtStsAndLeId(flow_for_id,nextStatusId,legalEntiryID).catch(err=> console.log(err));

								}
								
								await Promise.all(userInformation.map(async function(value){
									
										toEmails.push(value.email_id);
										userIDs.push(value.user_id);
								}));
							}
						}
					toEmails=toEmails.filter(function(elem, pos) {
							    	return toEmails.indexOf(elem) == pos;
							});
					userIDs=userIDs.filter(function(elem, pos) {
							    	return userIDs.indexOf(elem) == pos;
							});
					var userName = await module.exports.getUserName(userID).catch(err=> console.log(err));
					userName = userName.hasOwnProperty('firstname')?userName.firstname:'Unknown User';					

					if(new_title!=""){
	                	flowType = new_title;
	            	}

	            var emailContent = "A Ticket has been raised for " + flowType + "(<a href='"+redirectURL+"'>"+flowTypeForID+"</a>)<br><br>";
	            emailContent += "Ticket No :  TKT" + flowTypeForID+"<br>";
	            emailContent += "Assigned By : " + userName + "<br><br>";
	            emailContent += "Please refer to <a href='/approvalworkflow/approvalticket'>Approval Ticket Page</a> for more details.<br><br> Thanks,<br><br>Team Ebutor.";
	            //==========================================================

	            var body = [];
	            body['template']='emails.approvalWorkflowNotificationMail';
	           	body['attachment']='';
	           	body['name']='Hello!';
	           	body['comment']=emailContent;

	            if(emailFlag == 1 ){
	    			if( toEmails.length>0 ){
	                    //Replaced email functionality with email queue
	                    subject='Your Approval Is Pending For - TKT' + flowTypeForID;
	                  
	    	          //  module.exports.sendMail(subject,toEmails,body).catch(err=>{console.log(err)});
	    			}
	            }
	            var finalFlowData =[];
	            finalFlowData['status'] = "1";
	            finalFlowData['message'] = "Flow found";
	            finalFlowData['emails'] = toEmails;
	            finalFlowData['userIDs'] = userIDs; 
	            var tableId = await module.exports.insertApprworkflowcallDetils(finalFlowData,flowType,userID,datatosave); 
	            return resolve(true);
	        }else{
	        	return resolve(false);
	        }
			}catch(err){
	            console.error(err);
	            return reject("No Results found..")
        	}
		});
	},

	getNextLblRole:async function(flow_for_id,nextStatusId,legalEntiryID)
	{
		return new Promise((resolve,reject)=>{
			var nextLblRoleQry ="select awf.awf_id, awf.awf_name,det.applied_role_id,det.is_final,det.hub_data from appr_workflow_status_new AS awf join appr_workflow_status_details AS det on det.awf_id=awf.awf_id where awf.awf_for_id="+flow_for_id+" and det.awf_status_id="+nextStatusId+" and awf.legal_entity_id="+legalEntiryID;
				db.query(nextLblRoleQry,{},function(err,rows){
	                if(err){
	                    return reject('error');
	                }
	                if((rows != null && Object.keys(rows).length >0)){
	                    return resolve(rows);
	                }
	                else{
	                    return resolve({"status":"failed","message":"No Data Found","data":[]});
	                }
	            });
		})
	},

	getcurrentStatusDataForWorkFlowId:async function(flow_for_id,currentStatusID,nextStatusId,legalEntiryID)
	{
		return new Promise((resolve,reject)=>{
			var currentStatusDataQry ="select awf.awf_id, awf.awf_name,det.applied_role_id,det.is_final,det.awf_condition_id from appr_workflow_status_new AS awf join appr_workflow_status_details AS det on det.awf_id=awf.awf_id where awf.awf_for_id="+flow_for_id+" and det.awf_status_id="+nextStatusId+" and awf.legal_entity_id="+legalEntiryID+" and det.awf_status_id="+currentStatusID;
				db.query(currentStatusDataQry,{},function(err,rows){
	                if(err){
	                    return reject('error');
	                }
	                if((rows != null && Object.keys(rows).length >0)){
	                    return resolve(rows);
	                }
	                else{
	                    return resolve({"status":"failed","message":"No Data Found","data":[]});
	                }
	            });
		})
	},

	getPreviousHistoryID:async function(flowTypeForID,flow_for_id)
	{
		return new Promise((resolve,reject)=>{
			var previoushistoryIDQry ="select * from appr_workflow_history AS hist where hist.awf_for_id="+flowTypeForID+" and hist.awf_for_type_id="+flow_for_id+" order by hist.awf_history_id desc limit 1";
				db.query(previoushistoryIDQry,{},function(err,rows){
	                if(err){
	                    return reject('error');
	                }
	                if((rows != null && Object.keys(rows).length >0)){
	                    return resolve({"status":"success","message":"Data Found","data":rows});
	                }
	                else{
	                    return resolve({"status":"failed","message":"No Data Found","data":[]});
	                }
	            });
		})
	},

	apprWorkflowHistoryUpdateisFinal: async function(previousHistoryID){
		return new Promise((resolve,reject)=>{
			var isfinalupdateQry="update appr_workflow_history AS hist set is_final=1 where hist.awf_history_id="+previousHistoryID;
			db.query(isfinalupdateQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		})
	},

	getRoleId:async function(){
		return new Promise((resolve,reject)=>{
			var getRoleQry="select role_id from roles where name='Expenses Reporting Manager' limit 1";
			db.query(getRoleQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		})
	},
	getApprovalWorkFlowTicketCount:async function(ticketCrMgrID,role_id,flow_for_id){
		return new Promise((resolve,reject)=>{
			var checkTicketQry="select count(awf_history_id) as count from appr_workflow_history AS awf join expenses_main AS em on em.exp_id=awf.awf_for_id where em.exp_appr_status=57078 and awf.created_by_manager="+ticketCrMgrID+" and awf.next_lbl_role="+role_id+" and awf.awf_for_type_id="+flow_for_id;
			db.query(checkTicketQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		});
	},
	unsetManagerRole:async function(ticketCrMgrID,role_id){
		return new Promise((resolve,reject)=>{
			var unsetManagerQry="delete from user_roles where user_id="+ticketCrMgrID+" and role_id="+role_id;
			db.query(unsetManagerQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		})
	},

	dataToSaveInApprWorkFlowHstry:async function(datatosave){
		return new Promise((resolve,reject)=>{
			var insertQry = "insert into appr_workflow_history(awf_for_type,awf_for_type_id,awf_for_id,awf_comment,status_from_id,status_to_id,user_id,next_lbl_role,is_final,condition_id,ticket_created_by,created_by_manager,created_by) values ('"+datatosave.awf_for_type+"','"+datatosave.awf_for_type_id+"','"+datatosave.awf_for_id+"','"+datatosave.awf_comment+"','"+datatosave.status_from_id+"','"+datatosave.status_to_id+"','"+datatosave.user_id+"','"+datatosave.next_lbl_role+"','"+datatosave.is_final+"','"+datatosave.condition_id+"','"+datatosave.ticket_created_by+"','"+datatosave.created_by_manager+"','"+datatosave.created_by+"')";
			db.query(insertQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve({"status":"success","message":"Data Found","data":rows});
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		})
	},

	getApprovalDetails:async function(flow_for_id,legalEntiryID){
		return new Promise((resolve,reject)=>{
			var checkTicketQry="select * from appr_workflow_status_new where awf_for_id="+flow_for_id+" and legal_entity_id="+legalEntiryID+" limit 1";
			db.query(checkTicketQry,{},function(err,rows){
                if(err){
                    return reject('error');
                }
                if((rows != null && Object.keys(rows).length >0)){
                    return resolve(rows);
                }
                else{
                    return resolve({"status":"failed","message":"No Data Found","data":[]});
                }
            });	
		})
	},

	sendMail: function (subject, email, body) {
	     return new Promise((resolve, reject) => {
	          let transporter = nodeMailer.createTransport({
				   host: 'smtp.office365.com',
				   port: 587,
				   secureConnection: true, // upgrade later with STARTTLS
	               auth: {
	                    // user: config.EmailUsername,
	                    // pass: config.EmailPassword
	                    user: "tracker1@ebutor.com",
						pass: "Rasmus@12$Ler123"
	               }
	          });

			//   var Mail_list = 'nishanthreddy312@gmail.com'//email;
			  let Mail_list = email;
	          let HelperOptions = {
	               from: '"no-reply" <tracker1@ebutor.com>',//support@ebutor.com
	               to: Mail_list,
	               subject: JSON.stringify(subject),
	               html: body
	          };
	          // transporter.use('compile', htmlToText(HelperOptions));
	          transporter.sendMail(HelperOptions, (error, info) => {
	               if (error) {
	                    console.log("error =====>", error);
	                    // console.log("successful ===>", info);
	                    reject(error)
	               }
	               else {
	                    // console.log("successful ===>", info);
	                    resolve(info)
	               }
	          });
	     })

	},

	 getFlowForID:async function(flowForName){
    	return new Promise((resolve,reject)=>{
    		var flowForID="select `value` from `master_lookup` where `mas_cat_id`=56 and `master_lookup_name`='"+flowForName+"' limit 1";
    		db.query(flowForID,{},function(err,rows){
		        if(err){
		            reject('error');
		        }
		        if(Object.keys(rows).length >0){
		            return resolve(rows[0].value);
		        }else{
		            return resolve([0]);
		        }
		    });
    	});
    },

    getUserLegalEntity : function (user_id) {
		return new Promise((resolve, reject) => {
			let LegalEntityId = 'select `legal_entity_id` from `users` where user_id=' + user_id + ' limit 1';
			db.query(LegalEntityId, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					let le_id = rows[0].legal_entity_id;
					return resolve(le_id);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},

	saveWorkflowModuleWise:async function(dataToSave,flowTypeForID,flow_for_id){
        return new Promise((resolve,reject)=>{
	       	var query="select getMastLookupValue("+dataToSave.status_from_id+") as status_from_name, getMastLookupValue("+dataToSave.status_to_id+") as status_to_name, getMastLookupValue("+dataToSave.status_to_id+") as master_lookup_name, GetUserName("+dataToSave.user_id+",2) as user_name,getMastLookupValue("+dataToSave.condition_id+") as condition_name, GetUserName("+dataToSave.created_by+",2) as created_by_name,getUserProfilePicture("+dataToSave.user_id+") as profile_picture, getUserFirstName("+dataToSave.user_id+") as firstname,getUserLastName("+dataToSave.user_id+") as lastname,getUserRoleName("+dataToSave.user_id+") as name";
	       	//$query=json_decode(json_encode($query),1);
	       	db.query(query,{},async function(err,rows){
		        if(Object.keys(rows).length >0){
		        	await Promise.all(rows.map(async function(value,index){
		        		dataToSave[index]=value;
		        	}));
			    }
			});
			var poData=[];
		     poData = dataToSave;
            var dataExist ='select `comments` from `appr_comments` where `comments_id`='+flowTypeForID+' and `awf_for_type_id`='+flow_for_id+'';
            db.query(dataExist,{},function(err,rows){
            	var current_datetime = new Date();
            	var pocreateddate = current_datetime.getFullYear() + "-" + (current_datetime.getMonth() + 1) + "-" + current_datetime.getDate() + " " + current_datetime.getHours() + ":" + current_datetime.getMinutes() + ":" + current_datetime.getSeconds()
		        if(Object.keys(rows).length >0){
		        	var finalArray=[];
			        poData['created_at']=pocreateddate;
		            finalArray[finalArray.length]=poData;
		            var updateData = (['comments_id=flowTypeForID','comments=finalArray','awf_for_type_id=flow_for_id']);
		            var query='update `appr_comments` set '+updateData+' where comments_id='+flowTypeForID+' and `awf_for_type_id`='+flow_for_id+'';
		            db.query(dataExist,{},function(err,rows){});
		            return resolve([1]);
                }else{
                	var tempArray=[];
		            tempArray[0]=poData;
		            tempArray[0]['created_at']=pocreateddate;
                    var temp=tempArray[0];
		            var po =(["comments_id="+flowTypeForID+",comments="+temp+",awf_for_type_id="+flow_for_id+""]);
		            var query="insert into appr_comments "+po+"";
		            db.query(dataExist,{},function(err,rows){});
		            return resolve([1]);
                }
            });
        });
    },

       getFirstRecordForNextRole : function (flow_for_id,nextStatusId,flowTypeForID) {
		return new Promise((resolve, reject) => {
			let apprWrkhist = "select * from appr_workflow_history where awf_for_type_id = '"+flow_for_id+"' and awf_for_id = '"+flowTypeForID+"' and status_to_id = '"+nextStatusId+"' order by awf_history_id desc limit 1";
			db.query(apprWrkhist, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					return resolve(rows[0]);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},

	getroleDetails : function (next_lbl_role) {
		return new Promise((resolve, reject) => {
			let roleName = "select name from roles where role_id = '"+next_lbl_role+"' limit 1";
			db.query(roleName, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					return resolve(rows[0]);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},

	getUserInformationByNxtStsAndLeId : function (flow_for_id,nextStatusId,legalEntiryID) {
		return new Promise((resolve, reject) => {
			let roleName = "select distinct awf.awf_id,awf.awf_name,rls.user_roles_id,urs.user_id,urs.firstname,urs.lastname,urs.email_id,det.applied_role_id from appr_workflow_status_new AS awf join appr_workflow_status_details AS det on det.awf_id=awf.awf_id join user_roles as rls on rls.role_id=det.applied_role_id join users as urs on urs.user_id=rls.user_id where urs.is_active =1 and awf.awf_for_id="+flow_for_id+" and det.awf_status_id="+nextStatusId+" and awf.legal_entity_id="+legalEntiryID+" limit 1";
			db.query(roleName, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					return resolve(rows);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},

	getUserName : function (userID) {
		return new Promise((resolve, reject) => {
			let userName = "select * from users where user_id="+userID+" limit 1";
			db.query(userName, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					return resolve(rows[0]);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},

	insertApprworkflowcallDetils : function (finalFlowData,flowType,userID,datatosave) {
		let sample =  Object.assign({},finalFlowData);
		return new Promise((resolve, reject) => {
			let apprflowinsert = "insert into appr_workflow_call_details(appr_call_for,appr_name,appr_call_user_id,appr_call_made_at,appr_call_response,appr_call_input) values ('"+flowType+"','storeWorkFlowHistory','"+userID+"','"+presentday+"','"+ JSON.stringify(sample)+"','"+JSON.stringify(datatosave[0])+"')";
			db.query(apprflowinsert, {}, (err, rows) => {
				if (err) {
					reject('Bad request');
				} 
				if(Object.keys(rows).length >0){
					console.log('success')
					return resolve(rows);
		        }else{
		            return resolve([]);
		        }
			});
		});
	},
}