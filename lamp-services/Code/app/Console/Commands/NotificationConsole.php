<?php


/**
 *
 * Created By : Prasenjit CHowdhury
 * date : 5th August
 * Description : Single base controll to move to queue can be used for every other movement to queue
 *                For any carification contact prasenji.chowdhury@ebutor.com / jisionpc@gmail.com 
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Davibennun\LaravelPushNotification\PushNotification;
use App\Central\Repositories\MongoRepo;
use Log;


class NotificationConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification {message} {tokenDetails} {type} {sentBy} {link} {pushMessageId} {pushMessageCreatedBy} {paramsdata?}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will be the console front of the dmapi controller';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){

        $this->info('Received Notification');
        
        $arguments = $this->argument();
//        Log::info(json_encode($arguments));
        $message = $arguments['message'];
        $tokenDetails = $arguments['tokenDetails'];
        $type = $arguments['type'];
        $sentBy = $arguments['sentBy'];
        $link = $arguments['link'];
		$pushMessageId = $arguments['pushMessageId'];
        $pushMessageCreatedBy = $arguments['pushMessageCreatedBy'];
        $paramsdata = isset($arguments['paramsdata'])?(json_decode($arguments['paramsdata'],true)):[];
        
        /*=============conversion of all data from the encrypted form*/
        //Log::info($tokenDetails);
        $tokenDetails = base64_decode($tokenDetails);
        $tokenDetails = json_decode($tokenDetails,true);
         $this->info(print_r($tokenDetails));
        /*============================================================*/

        $title = 'Ebutor';
        $current_time = Carbon::now()->toDayDateTimeString();
        $additionalData = [ 
                            "type"=> $type, 
                            "title"=> $title,
                            "message" => $message,
                            "link" => $link, 
                            "sent_by" => $sentBy,
                            "time" => $current_time,
							"category"=>$type,
                            "data"=>$paramsdata
                            ];

        foreach($tokenDetails as $key=>$data){           
            
            try{
                $platformId = isset($data['platform_id'])?$data['platform_id']:NULL;
                $token = isset($data['registration_id'])?$data['registration_id']:NULL;   
                if($platformId!=NULL && $token!=NULL){
                    if($platformId == '5004'){
                        $optionBuiler = new OptionsBuilder();
                        $optionBuiler->setTimeToLive(60*20); 
                        $notificationBuilder = new PayloadNotificationBuilder('Ebutor');
                        $notificationBuilder->setBody($message)
                                            ->setSound('default');
                        $dataBuilder = new PayloadDataBuilder();
                        $dataBuilder->addData($additionalData);
                        $option = $optionBuiler->build();
                        $notification = $notificationBuilder->build();
                        $data = $dataBuilder->build();
                        $downstreamResponse = FCM::sendTo($token, $option, $notification,$data);
		$tableName = 'message_history';
        $insertData['reference_id'] = $pushMessageId;
        $insertData['requested_by'] = $pushMessageCreatedBy;
        $insertData['request_type'] = 'push';
        $insertData['number'] = $token;
        $insertData['message'] = json_encode($additionalData);
        $insertData['response'] = json_encode($downstreamResponse);
        date_default_timezone_set('Asia/Kolkata');
        $insertData['created_on'] = date('Y-m-d H:i:s');
        //$mongoRepo = new MongoRepo();
        //$mongoRepo->insert($tableName, $insertData);
                        $downstreamResponse->numberSuccess();
                        $downstreamResponse->numberFailure();
                        $downstreamResponse->numberModification();

                        //return Array - you must remove all this tokens in your database
                        $downstreamResponse->tokensToDelete(); 

                        //return Array (key : oldToken, value : new token - you must change the token in your database )
                        $downstreamResponse->tokensToModify(); 

                        //return Array - you should try to resend the message to the tokens in the array
                        $downstreamResponse->tokensToRetry();
                    }
                    else{
                        $iosNotifications = new PushNotification;
                        $res = $iosNotifications->app('appNameIOS')
                                ->to($token)
                                ->send($message,$additionalData);
								
								
		$tableName = 'message_history';
        $insertData['reference_id'] = $pushMessageId;
        $insertData['requested_by'] = $pushMessageCreatedBy;
        $insertData['request_type'] = 'push';
        $insertData['number'] = $token;
        $insertData['message'] = json_encode($additionalData);
        $insertData['response'] = json_encode($res);
        date_default_timezone_set('Asia/Kolkata');
        $insertData['created_on'] = date('Y-m-d H:i:s');
        //$mongoRepo = new MongoRepo();
       // $mongoRepo->insert($tableName, $insertData);		
								
                    }
                }
            }catch(\Exception $e){

                    $this->error($e->getMessage());
                    // Log::info("Notification Log  " . $e->getMessage());
            }
        }       
    }

}
