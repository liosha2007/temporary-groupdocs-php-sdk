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
    $email = f3::get('POST["email"]');
    $signName = f3::get('POST["name"]');
    $lastName = f3::get('POST["lastName"]');
    f3::set('email', $email);
    f3::set('name', $signName);
    f3::set('lastName', $lastName);
    
    function sendEnvelop($clientId, $privateKey, $email, $signName, $lastName)
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
                return new RedirectResponse("/sample21");
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
            $basePath = f3::get('POST["server_type"]');
            //Declare which Server to use
            $apiStorage->setBasePath($basePath);
            //###Make a request to Storage API using clientId
            
            //Upload file to current user storage
            $uploadResult = $apiStorage->Upload($clientID, $name, 'uploaded', $fs);
            
            //###Check if file uploaded successfully
            if ($uploadResult->status == "Ok") {
                //Create SignatureApi object
                $signature = new SignatureApi($apiClient);
                $signature->setBasePath($basePath);
                
                //Create envilope using user id and entered by user name
                $envelop = $signature->CreateSignatureEnvelope($clientID, $name);
//                sleep(5);
                //Add uploaded document to envelope

                $addDocument = $signature->AddSignatureEnvelopeDocument($clientID, $envelop->result->envelope->id, $uploadResult->result->guid);
                //Get role list for curent user
                $recipient = $signature->GetRolesList($clientID);
                //Get id of role which can sign
                for($i = 0; $i < count($recipient->result->roles); $i++) {
                    if($recipient->result->roles[$i]->name == "Signer") {
                        $roleId = $recipient->result->roles[$i]->id;
                    }
                }
                //Add recipient to envelope
                $addRecipient = $signature->AddSignatureEnvelopeRecipient($clientID, $envelop->result->envelope->id, $email, $signName, $lastName, null, $roleId);
                //Get recipient id
                $getRecipient = $signature->GetSignatureEnvelopeRecipients($clientId, $envelop->result->envelope->id);
                $recipientId = $getRecipient->result->recipients[0]->id;
                //Url for callback
                $callbackUrl = f3::get('POST["callbackUrl"]');
                F3::set("callbackUrl", $callbackUrl);
                //Send envelop with callback url
                $send = $signature->SignatureEnvelopeSend($clientID, $envelop->result->envelope->id, $callbackUrl);
                
                if($basePath == "https://api.groupdocs.com/v2.0") {
                //iframe to prodaction server
                    $iframe = '<iframe src="https://apps.groupdocs.com/signature/signembed/'. $envelop->result->envelope->id .'/'. $recipientId . '?frameborder="0" width="720" height="600"></iframe>';
                //iframe to dev server
                } elseif($basePath == "https://dev-api.groupdocs.com/v2.0") {
                    $iframe = '<iframe src="https://dev-apps.groupdocs.com/signature/signembed/'. $envelop->result->envelope->id .'/'. $recipientId . '?frameborder="0" width="720" height="600"></iframe>';
                //iframe to test server
                } elseif($basePath == "https://stage-api.groupdocs.com/v2.0") {
                    $iframe = '<iframe src="https://stage-apps.groupdocs.com/signature/signembed/'. $envelop->result->envelope->id .'/'. $recipientId . '?frameborder="0" width="720" height="600"></iframe>';
                }
                
                $result = array();
                //Make iframe
                $result = array('iframe' => $iframe,
                                'name' => $name);
                //If request was successfull - set result variable for template
                return $result;
            } 
        }  
     }
     
     try {
         $upload = sendEnvelop($clientId, $privateKey, $email, $signName, $lastName);
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
     echo Template::serve('sample21.htm');
