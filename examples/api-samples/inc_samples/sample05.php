<?php
   //<i>This sample will show how to use <b>MoveFile</b> method from Storage Api to copy/move a file in GroupDocs Storage </i>

    //###Set variables and get POST data
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileName = F3::get('POST["srcPath"]');
    $copy = F3::get('POST["copy"]');
    $move = F3::get('POST["move"]');
    $path = F3::get('POST["destPath"]');
    
    function copy_move($clientId, $privateKey, $fileName, $move=NULL, $copy=NULL, $path)
    {
        //###Check clientId, privateKey and file Id
        if (!isset($clientId) || !isset($privateKey) || !isset($fileGuId)) {
			
			throw new Exception('You do not enter all parameters');
			
        }else{   
           
            //###Create Signer, ApiClient and Storage Api objects

            //Create signer object
			$signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer); 
            //Create Storage Api object
            $api = new StorageApi($apiClient);
            
             //###Make a request to Storage API using clientId
            
            //Obtaining all Entities from current user
            $files = $api->ListEntities($clientId, '', 0);
            //Obtaining file name and id by fileGuID
            $name = '';
            $file_id = '';
			
            foreach ($files->result->files as $item)
            {
               if ($item->guid == $fileGuId) {
                   $name = $item->name;
                   $file_id = $item->id;
               }
            }
            //###Make request for file copying/movement
            
            //If user choose copy
            if (isset($copy)) {
               //Where to copy
               $path = $folder . '/' . $name;
               //Request to Storage for copying
               $file = $api->MoveFile($clientId, $path, NULL, $file_id, NULL); //download file
               //Returning to Viewer what button was pressed
               return  F3::set('button', $copy);
            }
            //If user choose move
            if (isset($move)) {
                //Where to move
               $path = $folder . '/' . $name;
               //Request to Storage for copying
               $file = $api->MoveFile($clientId, $path, NULL, NULL, $file_id); //download file
                //If request was successfull - set button variable for template
               return F3::set('button', $move);
            }
         } 
    }
    
    try {
        copy_move($clientId, $privateKey, $fileName, $move, $copy, $path);
        $message = "File was {{@button}}'ed to the {{@folder}} folder";
    } catch(Exception $e) {

        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        $message = $error;
    }
    //Process template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('file_Name', $fileName);
    F3::set('folder', $path);
    f3::set('message', $message);
    
    echo Template::serve('sample05.htm');