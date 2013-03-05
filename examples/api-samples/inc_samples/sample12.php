<?php
    //###This sample will show how to list all annotations from document
     

    //### Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');

    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileId = F3::get('POST["fileId"]');

    function ListAnnotations($clientId, $privateKey, $fileId) {
        if (empty($clientId) || empty($privateKey) || empty($fileId)) {
            throw new Exception('Please enter all required parameters');
        } else {

            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            F3::set('fileId', $fileId);

            #### Create Signer, ApiClient and Annotation Api objects
            # Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);

            # Create apiClient object
            $apiClient = new ApiClient($signer);

            # Create Annotation object
            $ant = new AntApi($apiClient);

            # Make a request to Annotation API using clientId and fileId
            $list = $ant->ListAnnotations($clientId, $fileId);

            // Check the result of the request
            if (isset($list->result)) {
                // If request was successfull - set annotations variable for template
                return F3::set('annotations', $list->result->annotations);
            }
        }
    }

    try {
        ListAnnotations($clientId, $privateKey, $fileId);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }

    // Process template
    echo Template::serve('sample12.htm');