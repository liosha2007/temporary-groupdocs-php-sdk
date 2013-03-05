<?php
    //<i>This sample will show how to use <b>ListEntities</b> method from Storage Api to create a list of thumbnails for a document</i>

    //###Set variables and get POST data
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    
    function ThumbnailList($clientId, $privateKey)
    {
        //### Check clientId and privateKey
        if (empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter all required parameters');
        } else {
            //Set variables for Viewer
            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            //###Create Signer, ApiClient and Storage Api objects

            //Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer);
            //Create Storage Api object
            $api = new StorageApi($apiClient);
            //###Make request to Storage

            //Geting all Entities with thumbnails from current user
            $files = $api->ListEntities($clientId, "", null, null, null, null, null, null, true);
            //Obtaining all thumbnails
            $thumbnail = '';
            $name = '';
            for ($i=0; $i < count($files->result->files); $i++) {
                //Check is file have thumbnail
                if ($files->result->files[$i]->thumbnail !== "") {
                    //Placing thumbnails to local folder
                    $fp = fopen(__DIR__ . '/../temp/thumbnail' . $i . '.jpg', 'w');
                    fwrite($fp, base64_decode($files->result->files[$i]->thumbnail));
                    fclose($fp);
                    //Geting file names for thumbnails
                    $name = $files->result->files[$i]->name;
                    //Create HTML representation for thumbnails
                    $thumbnail .= '<img src= "/temp/thumbnail' . $i . '.jpg", width="40px", height="40px">'
                                  . $name = $files->result->files[$i]->name . '</img> <br>';
                }            
            }
            //If request was successfull - set thumbnailList variable for template
            return F3::set('thumbnailList', $thumbnail);
        }
    }
    
    try {
        ThumbnailList($clientId, $privateKey);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    echo Template::serve('sample07.htm');