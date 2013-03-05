<?php
    //<i>This sample will show how to use <b>GetFile</b> method from Storage Api to download a file from GroupDocs Storage</i>

    //###Set variables and get POST data
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $file_id = F3::get('POST["fileId"]');
    
    function Download($clientId, $privateKey, $file_id)
    {
        //###Check clientId, privateKey and file Id
        if (!isset($clientId) || !isset($privateKey) || !isset($file_id)) {
            throw new Exception('Please enter all required parameterss');
        } else {
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
            //Selecting file names
            foreach ($files->result->files as $item) 
            {
               //Obtaining file name for entered file Id
               if ($item->guid == $file_id) {
                   $name = $item->name;
               }
            }
            
            //###Make a request to Storage Api for dowloading file
            
            //Obtaining file stream of downloading file and definition of folder where to download file
            $outFileStream =  FileStream::fromHttp(dirname(__FILE__). '/../temp', $name);
            //Downlaoding of file
            $file = $api->GetFile($clientId, $file_id, $outFileStream);
            //If request was successfull - set message variable for template
            $message = '<font color="green">File was downloaded to the <font color="blue">' . $outFileStream->downloadDirectory . '</font> folder</font> <br />';
            return f3::set('message', $message);
        }
    }   
    try {
        Download($clientId, $privateKey, $file_id);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('file_Id', $file_id);
    
    echo Template::serve('sample04.htm');