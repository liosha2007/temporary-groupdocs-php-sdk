<?php
    //<i>This sample will show how to use <b>Upload</b> method from Storage Api to upload file to GroupDocs Storage </i>

    //###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');
    F3::set('message', '');
    F3::set('iframe', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $url = F3::get('POST["url"]');
    
    function Upload($clientId, $privateKey, $url)
    {
        //###Check clientId and privateKey
        if (empty($clientId) || empty($privateKey) || empty($url)) {
            throw new Exception('Please enter all required parameters');
        } else {
    
            $clientID = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
            $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
            
            //###Create Signer, ApiClient and Storage Api objects
            
            //Create signer object
            $signer = new GroupDocsRequestSigner($apiKey);
            //Create apiClient object
            $apiClient = new APIClient($signer);
            //Create Storage Api object
            $apiStorage = new StorageApi($apiClient);
            
            //###Make a request to Storage API using clientId
            
            //Upload file to current user storage using entere URl to the file
            $uploadResult = $apiStorage->UploadWeb($clientID, $url);
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Generation of Embeded Viewer URL with uploaded file GuId
                $result = '<iframe src="https://apps.groupdocs.com/document-viewer/Embed/' . $uploadResult->result->guid . '" frameborder="0" width="720" height="600"></iframe>';                               
                //If request was successfull - set result variable for template
                return $result;
            } 
        }  
     }
     
     try {
         $upload = Upload($clientId, $privateKey, $url);
         $message = '<p>File was uploaded to GroupDocs. Here you can see your <strong> file in the GroupDocs Embedded Viewer.</p>';
         F3::set('message', $message);
         F3::set('iframe', $upload);
     } catch(Exception $e) {
         $error = 'ERROR: ' .  $e->getMessage() . "\n";
         f3::set('error', $error);
     }
     //Process template
     F3::set('userId', $clientId);
     F3::set('privateKey', $privateKey);
     echo Template::serve('sample24.htm');