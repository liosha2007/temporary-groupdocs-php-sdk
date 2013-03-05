<?php
    //### This sample will show how to check the number of document's views using PHP SDK
     
    //### Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');

    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');

    function DocumentsViews($clientId, $privateKey) {

        if (empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter all required parameters');
        } else {
            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);

            // initialization some variables
            $views = 0;

            //### Create Signer, ApiClient and Document Api objects
            // Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);

            // Create apiClient object
            $apiClient = new ApiClient($signer);

            // Create Document object
            $doc = new DocApi($apiClient);

            // Make a request to Doc API using clientId
            $result = $doc->GetDocumentViews($clientId);

            // Check the result of the request
            if (isset($result->result)) {
                // If request was successfull - set annotations variable for template
                return F3::set('views', count($result->result->views));
            }
        }
    }


    try {
        DocumentsViews($clientId, $privateKey);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }

    // Process template
    echo Template::serve('sample15.htm');