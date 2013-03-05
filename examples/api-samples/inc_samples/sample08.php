<?php
    //<i>This sample will show how to use <b>GetDocumentPagesImageUrls</b> method from Doc Api to return a URL representing a single page of a Document</i>

    //###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileGuId = f3::get('POST["fileId"]');
    $pageNumber = f3::get('POST["pageNumber"]');
    
    function GetDocumentPages($clientId, $privateKey, $fileGuId, $pageNumber=0)
    {
         //### Check clientId, privateKey and fileGuId
        if (empty($clientId) || empty($privateKey) || empty($fileGuId)) {
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
            //Create DocApi object
            $docApi = new DocApi($apiClient);
            //###Make request to DocApi using user id
            
            //Obtaining URl of entered page 
            $URL = $docApi->GetDocumentPagesImageUrls($clientId, $fileGuId, (int)$pageNumber, 1, '600x750');
            //If request was successfull - set url variable for template
            return f3::set('url', $URL->result->url[0]);
        }
    }
    
    try {
        GetDocumentPages($clientId, $privateKey, $fileGuId, $pageNumber);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    f3::set('fileId', $fileGuId);
    f3::set('pageNumber', $pageNumber);
    echo Template::serve('sample08.htm');