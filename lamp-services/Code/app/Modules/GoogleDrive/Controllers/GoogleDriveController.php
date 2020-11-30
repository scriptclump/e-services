<?php

namespace App\Modules\GoogleDrive\Controllers;
use App\Http\Controllers\BaseController;
use Google_Client; 
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Session;
use URL;
use Illuminate\Http\Request;
class GoogleDriveController extends BaseController {

        public function uploadImage(Request $request)
        {
           
            
$client = new Google_Client();
// Get your credentials from the console
//$client->setClientId('251837873391-82rufp9i89bd7p468doqruor0g3eu6rr.apps.googleusercontent.com');
//$client->setClientSecret('zzcbr87ebeh8GrdSsOZU94kG');
        $client->setClientId('846934052840-ips7gb5qteqemoq1jkmii48g4i0ibcs7.apps.googleusercontent.com');
        $client->setClientSecret('Ol2-e2ds8qpkYcQrvrgx_4XD');
$client->setRedirectUri(URL('/').'/driveupload/image');
$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
//Session::forget('access_token');

if (isset($_GET['code']) || Session::get('access_token')) {
    if (isset($_GET['code'])) {
        $client->authenticate($_GET['code']);		
        Session::set('access_token',$client->getAccessToken());
    } else
	{
		try{
                    
			$client->setAccessToken(Session::get('access_token'));
		}
		catch(Exception $e)
		{
                     
			$authUrl = $client->createAuthUrl();
			print "<a class='login' href='$authUrl'>Connect Me!</a>";
			
		}
		//$client->setAccessType("offline");
		//$client->setApprovalPrompt("force");
	}
        

    $service = new Google_Service_Drive($client);
    //Insert a file
    $file = new Google_Service_Drive_DriveFile();
    $file->setName($request->get('filename').'.pdf');
    $file->setDescription('A test document');
    $file->setMimeType('application/vnd.google.drive.ext-type.pdf');
    if($request->file('upload_file')) 
    {
    $data = file_get_contents($request->file('upload_file')->getPathName());
    }
    else{
    $data = '';}	
	
	try{
    $createdFile = $service->files->create($file, array(
          'data' => $data,
          'mimeType' => 'application/vnd.google.drive.ext-type.pdf',
          'uploadType' => 'multipart'
        ));
    
	
  /*$newPermission = new Google_Service_Drive_Permission();
	
  //$newPermission->setValue('default');
  //$newPermission->setId($createdFile->id);
  $newPermission->setType('anyone');
  $newPermission->setRole('reader');
  try {
    $val = $service->permissions->create($createdFile->id, $newPermission);
  } catch (Exception $e) {
    print "An error occurred: " . $e->getMessage();
  } 
  */
	return 'https://drive.google.com/uc?export=view&id='.$createdFile->id;
        //header('Location: http://localhost:8000/suppliers/edit/77');
	//die;
	}
	catch(Exception $e){
		$authUrl = $client->createAuthUrl();
		print "<a class='login' href='$authUrl'>Connect Me!</a>";
	}
    

} else {
    //$authUrl = $client->createAuthUrl();
    header('Location: ' . authUrl());
    exit();
}	
           //echo "here";
        }
		
    public function authUrl()
    {
        $client = new Google_Client();
        // Get your credentials from the console
        //$client->setClientId('251837873391-82rufp9i89bd7p468doqruor0g3eu6rr.apps.googleusercontent.com');
        //$client->setClientSecret('zzcbr87ebeh8GrdSsOZU94kG');
        $client->setClientId('846934052840-ips7gb5qteqemoq1jkmii48g4i0ibcs7.apps.googleusercontent.com');
        $client->setClientSecret('Ol2-e2ds8qpkYcQrvrgx_4XD');
        $client->setRedirectUri(URL('/').'/driveupload/image');
        $client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
        return $authUrl = $client->createAuthUrl();
    }

    public function saveToken()
    {
            if (isset($_GET['code']) )
            {
                    Session::forget('access_token');
                    $client->authenticate($_GET['code']);		
                    Session::set('access_token',$client->getAccessToken());
                    header('Location: ' . URL::to('/'));
            }
    }
		
}
