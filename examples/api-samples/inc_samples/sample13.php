<?php
    //### This sample will show how to add collaborator to doc with annotations
    
    //### Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');
    F3::set('collaborations', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileId = F3::get('POST["fileId"]');
    $collaborations = array(F3::get('POST["email"]'));

    function addCollaborator($clientId, $privateKey, $fileId, $collaborations) {
        // Remove NULL value
        $collaborations = (is_array($collaborations)) ? array_filter($collaborations, 'strlen') : array();

        if (empty($clientId) || empty($privateKey) || empty($fileId) || (is_array($collaborations) && !count($collaborations))) {
            throw new Exception('Please enter all required parameters');
        } else {

            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            F3::set('fileId', $fileId);
            F3::set('collaborations', $collaborations);

            //### Create Signer, ApiClient and Annotation Api objects
            // Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);

            // Create apiClient object
            $apiClient = new ApiClient($signer);

            // Create Annotation object
            $ant = new AntApi($apiClient);
            var_dump($collaborations);
            // Make a request to Annotation API using clientId and fileId
            $response = $ant->SetAnnotationCollaborators($clientId, $fileId, "v2.0", $collaborations);

            // Check the result of the request
            if (isset($response->result)) {
                // If request was successfull - set annotations variable for template
                return F3::set('result', $response->result);
            }
        }
    }

    try {
        addCollaborator($clientId, $privateKey, $fileId, $collaborations);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }

    // Process template
    echo Template::serve('sample13.htm');