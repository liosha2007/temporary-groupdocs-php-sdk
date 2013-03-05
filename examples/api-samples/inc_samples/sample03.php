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
    
    function Upload($clientId, $privateKey)
    {
        //###Check clientId and privateKey
        if (empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter all required parameters');
        } else {
            //Get uploaded file
            $uploadedFile = $_FILES['file'];
            //Deleting of tags, slashes and  space from clientId and privateKey
            $clientID = strip_tags(stripslashes(trim($clientId))); //ClientId==UserId
            $apiKey = strip_tags(stripslashes(trim($privateKey))); //ApiKey==PrivateKey
            
            //###Check uploaded file
            if (null === $uploadedFile) {
                return new RedirectResponse("/sample3");
            }
            //Temp name of the file
            $tmp_name = $uploadedFile['tmp_name']; 
            //Original name of the file
            $name = $uploadedFile['name'];
            //Creat file stream
            $fs = FileStream::fromFile($tmp_name);
            
            //###Create Signer, ApiClient and Storage Api objects
            
            //Create signer object
            $signer = new GroupDocsRequestSigner($apiKey);
            //Create apiClient object
            $apiClient = new APIClient($signer);
            //Create Storage Api object
            $apiStorage = new StorageApi($apiClient);
            
            //###Make a request to Storage API using clientId
            
            //Upload file to current user storage
            $uploadResult = $apiStorage->Upload($clientID, $name, 'uploaded', $fs);
            
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Generation of Embeded Viewer URL with uploaded file GuId
                $result = array();
                $result = array('iframe' => '<iframe src="https://apps.groupdocs.com/document-viewer/Embed/' . $uploadResult->result->guid . '" frameborder="0" width="720" height="600"></iframe>',
                                'name' => $name);
                //If request was successfull - set result variable for template
                return $result;
            } 
        }  
     }
     
     try {
         $upload = Upload($clientId, $privateKey);
         $message = '<p>File was uploaded to GroupDocs. Here you can see your <strong>' . $upload['name'] . '</strong> file in the GroupDocs Embedded Viewer.</p>';
         F3::set('message', $message);
         F3::set('iframe', $upload['iframe']);
     } catch(Exception $e) {
         $error = 'ERROR: ' .  $e->getMessage() . "\n";
         f3::set('error', $error);
     }
     //Process template
     F3::set('userId', $clientId);
     F3::set('privateKey', $privateKey);
     echo Template::serve('sample03.htm');