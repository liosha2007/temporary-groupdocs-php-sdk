<?php
    //<i>This sample will show how to use <b>SignDocument</b> method from Signature Api to Sign Document and upload it to user storage</i>

    //###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');
    // get raw post data
    $postdata = file_get_contents("php://input"); 

    //Check postdata
    if (isset($postdata) AND !empty($postdata)) {
        //Get json object from raw data with request parameters
        $jsonPostData = json_decode($postdata, true);
        //Get parameters from json object
        $clientId = $jsonPostData['userId']; 
        $privateKey = $jsonPostData['privateKey'];
        $documents = $jsonPostData['documents'];
        $signers = $jsonPostData['signers'];
        //Determination of placeSingatureOn parameter
        for ($i = 0; $i < count($signers); $i++) {
            $signers[$i]['placeSignatureOn'] = '';
        }
        //###Create Signer, ApiClient and Storage Api objects

        //Create signer object
        $signer = new GroupDocsRequestSigner($privateKey);
        //Create apiClient object
        $apiClient = new APIClient($signer);
        //Create Storage Api object
        $signatureApi = new SignatureApi($apiClient);
        //Create setting variable for signature SignDocument method
        $settings = new SignatureSignDocumentSettings();
        $settings->documents = $documents;
        $settings->signers = $signers;
        
        //###Make a request to Signature Api for sign document
        
        //Sign document using current user id and sign settings
        $response = $signatureApi->SignDocument($clientId, $settings);
        //Check is file signed and uploaded successfully
        
        if ($response->status == "Ok") {
            //Post json object to template
            $return = json_encode(array("responseCode" => 200, "documentId" => $response->result->documents[0]->documentId));
        }
    }
    //Process template
    if (isset($return) AND !empty($return)) {
        header('Content-type: application/json');
        echo $return;
    } else {
        echo Template::serve('sample06.htm');
    }

