<?php
//<i>This sample will show how to use <b>Signer object</b> to be authorized at GroupDocs and how to get GroupDocs user infromation using PHP SDK</i>

//###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    
    function UserInfo($clientId, $privateKey)
    {
        if (empty($clientId) || empty($privateKey)) {
            throw new Exception('Please enter all required parameters');
        } else {
            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            //###Create Signer, ApiClient and Management Api objects
            
            //Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            //Create apiClient object
            $apiClient = new APIClient($signer); // PHP SDK Version > 1.0
            
            //Create Management Api object
            $mgmtApi = new MgmtApi($apiClient);
            
            //###Make a request to Management API using clientId
            $userAccountInfo = $mgmtApi->GetUserProfile($clientId);
            
            //Check the result of the request
            if (isset($userAccountInfo->result) AND isset($userAccountInfo->result->user)) {
                //If request was successfull - set userInfo variable for template
                return F3::set('userInfo', $userAccountInfo->result->user);
            }
        }
    }
     
    try {
        UserInfo($clientId, $privateKey);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    echo Template::serve('sample01.htm');