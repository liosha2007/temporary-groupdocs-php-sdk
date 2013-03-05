<?php
//<i>This sample will show how to use <b>ListEntities</b> method from Storage  API  to list files within GroupDocs Storage</i>

//###Set variables and get POST data

    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    
    function FileList($clientId, $privateKey)
    {
         //###Check clientId and privateKey
        if (empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter all required parameters');
        } else {
             //###Create Signer, ApiClient and Storage Api objects
            
            //Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer); // PHP SDK V1.1
            //Create Storage Api object
            $api = new StorageApi($apiClient);
            
            //###Make a request to Storage API using clientId
            
            //Obtaining all Entities from current user
            $files = $api->ListEntities($clientId, '', 0); 
                        
            //Obtaining file names
            $name = '';
            foreach ($files->result->files as $item) {
                $name .= $item->name . '<br>';
            }
            
           //If request was successfull - set filelist variable for template
           return F3::set('filelist', $name);
        }
    }
    
    try {
        FileList($clientId, $privateKey);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    
    //Process template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    echo Template::serve('sample02.htm');