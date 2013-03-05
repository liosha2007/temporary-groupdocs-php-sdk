<?php
    //<i>This sample will show how to use <b>GuId</b> of file to generate an embedded Viewer URL for a Document</i>

    //###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileGuId = f3::get('POST["fileId"]');
    $basePath = f3::get('POST["server_type"]');
   
    function Iframe($fileGuId, $clientId, $privateKey, $basePath)
    {
        //###Check if user entered all parameters
        if (empty($fileGuId) || empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter FILE ID');
        } else {
           
            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            //###Create Signer, ApiClient and Storage Api objects

            //Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer);
            //Create Doc Api object
            $api = new DocApi($apiClient);
            //Set url to choose whot server to use
            $api->setBasePath($basePath);
            //Make request yo the Api to get images for all document pages
            $pageImage = $api->ViewDocument($clientId, $fileGuId, 0, -1, 100, null);
            //Check the result of the request
            if($pageImage->status == "Ok") {
                 //### If request was successfull
               
                //Generation of iframe URL using $pageImage->result->guid
                //iframe to prodaction server
                if($basePath == "https://api.groupdocs.com/v2.0") {
                    $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $pageImage->result->guid . '?frameborder="0" width="500" height="650"';
                //iframe to dev server
                } elseif($basePath == "https://dev-api.groupdocs.com/v2.0") {
                    $iframe = 'https://dev-apps.groupdocs.com/document-viewer/embed/' . $pageImage->result->guid . '?frameborder="0" width="500" height="650"';
                //iframe to test server
                } elseif($basePath == "https://stage-api.groupdocs.com/v2.0") {
                    $iframe = 'https://stage-apps.groupdocs.com/document-viewer/embed/' . $pageImage->result->guid . '?frameborder="0" width="500" height="650"';
                }
                
            }
            //Set variable with results for template
            return f3::set('url', $iframe);
        }
    }
    
    try {
        Iframe($fileGuId, $clientId, $privateKey, $basePath);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
     
    f3::set('fileId', $fileGuId);
    echo Template::serve('sample23.htm');