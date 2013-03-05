<?php
    //<i>This sample will show how to use <b>ShareDocument</b> method from Doc Api to share a document to other users</i>

    //###Set variables and get POST data

    F3::set('userId', '');
    F3::set('privateKey', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileGuId = f3::get('POST["fileId"]');
    $email = f3::get('POST["email"]');
    $sharer = array($email);
    
    function Share($userId, $privateKey, $file_Id, $body)
    {
        //### Check file id, user, private key and body
        if ($file_Id == "" || $userId == "" || $privateKey == "" || $body == "") {
            throw new Exception('Please enter FILE ID');
        } else {
            //###Create Signer, ApiClient and Storage Api objects

            //Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer);
            //Create Storage Api object
            $api = new StorageApi($apiClient);
            //###Make request to Storage

            //Geting all Entities from current user
            $files = $api->ListEntities($userId, '', 0);
            //Selecting file names
            $name = '';
            foreach ($files->result->files as $item)
            {
               if ($item->guid == $file_Id) {
                $name = $item->name;
                $file_id = $item->id;
               }
            }
            //###Create DocApi object
            $docApi = new DocApi($apiClient);
            //Make request to user storage for sharing document
            $URL = $docApi->ShareDocument($userId, $file_id, $body);
            //If request was successfull - set shared variable for template
            return f3::set('shared', $body['0']);
        }
    }
    
    try {
        Share($clientId, $privateKey, $fileGuId, $sharer);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    f3::set('fileId', $fileGuId);
    f3::set('email', $email);
    echo Template::serve('sample10.htm');