<?php
    //### This sample will show how create or update user and add him to collaborators using PHP SDK
    
    //### Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('email', '');
    F3::set('first_name', '');
    F3::set('fileId', '');
    F3::set('last_name', '');
    $clientId = F3::get('POST["client_id]');
    $privateKey = F3::get('POST["private_key"]');
    $email = F3::get('POST["email"]');
    $firstName = F3::get('POST["first_name"]');
    $fileId = F3::get('POST["fileId"]');
    $lastName = F3::get('POST["last_name"]');
    $basePath = f3::get('POST["server_type"]');

    function updateUser($clientId, $privateKey, $email, $firstName, $fileId, $lastName, $basePath) {
        //Check if all requared parameters were transferred
        if (empty($clientId) || empty($privateKey) || empty($email) || empty($firstName) || empty($fileId) || empty($lastName)) {
            //if not send error message
            throw new Exception('Please enter all required parameters');
        } else {
            //Set variables for template "You are entered" block
            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            F3::set('email', $email);
            F3::set('first_name', $firstName);
            F3::set('fileId', $fileId);
            F3::set('last_name', $lastName);

            //### Create Signer, ApiClient and Mgmt Api objects
            
            // Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            // Create apiClient object
            $apiClient = new ApiClient($signer);
            // Create MgmtApi object
            $mgmtApi = new MgmtApi($apiClient);
            //Declare which server to use
            $mgmtApi->setBasePath($basePath);
             //###Create User info object
            
            //Create User info object
            $user = new UserInfo();
            //Create Role info object
            $role = new RoleInfo();
            //Set user role Id. Can be: 1 -  SysAdmin, 2 - Admin, 3 - User, 4 - Guest
            $role->id = "3";
            //Set user role name. Can be: SysAdmin, Admin, User, Guest
            $role->name = "User";
            //Create array of roles.
            $roles = array($role);
            //Set nick name as entered first name
            $user->nickname = $firstName;
            //Set first name as entered first name
            $user->firstname = $firstName;
            //Set last name as entered last name
            $user->lastname = $lastName;
            $user->roles = $roles;
            //Set email as entered email
            $user->primary_email = $email;
            //Creating of new user. $clientId - user id, $firstName - entered first name, $user - object with new user info
            $newUser = $mgmtApi->UpdateAccountUser($clientId, $email, $user);
            //Check the result of the request
            if ($newUser->status == "Ok") {
                //### If request was successfull
                
                //Create Annotation api object
                $ant = new AntApi($apiClient);
                //Create array with entered email for SetAnnotationCollaborators method 
                $arrayEmail = array($email);
                //Make request to Ant api for set new user as annotation collaborator
                $addCollaborator = $ant->SetAnnotationCollaborators($clientId, $fileId, "2.0", $arrayEmail);
                //Make request to Annotation api to receive all collaborators for entered file id
                $getCollaborators = $ant->GetAnnotationCollaborators($clientId, $fileId);
                //Set reviewers rights for new user. $newUser->result->guid - GuId of created user, $fileId - entered file id, 
                //$getCollaborators->result->collaborators - array of collabotors in which new user will be added
                $setReviewer = $ant->SetReviewerRights($newUser->result->guid, $fileId, $getCollaborators->result->collaborators);
                //Create calback from entered URL
                $callbackUrl = f3::get('POST["callbackUrl"]');
                F3::set("callbackUrl", $callbackUrl);
                //Createing an array with data for callBack session
                $arrayForJson = array($newUser->result->guid, $fileId, $callbackUrl);
                //Encoding to json array with data for callBack session
                $json = json_encode($arrayForJson);
                //Make request to Annotation api to set CallBack session
                $setCallBack = $ant->SetSessionCallbackUrl($json, "", "");
                //Generating iframe for template
                if($basePath == "https://api.groupdocs.com/v2.0") {
                    $iframe = 'https://apps.groupdocs.com//document-annotation2/embed/' . $fileId . '?&uid=' . $newUser->result->guid . '&download=true frameborder="0" width="720" height="600"';
                //iframe to dev server
                } elseif($basePath == "https://dev-api.groupdocs.com/v2.0") {
                    $iframe = 'https://dev-apps.groupdocs.com//document-annotation2/embed/' . $fileId . '?&uid=' . $newUser->result->guid . '&download=true frameborder="0" width="720" height="600"';
                //iframe to test server
                } elseif($basePath == "https://stage-api.groupdocs.com/v2.0") {
                    $iframe = 'https://stage-apps.groupdocs.com//document-annotation2/embed/' . $fileId . '?&uid=' . $newUser->result->guid . '&download=true frameborder="0" width="720" height="600"';
                }
                //Set variable with work results for template
                return F3::set('url', $iframe);
            } else {
                return F3::set("message", $newUser->error_message);
            }
        }
    }

    try {
        updateUser($clientId, $privateKey, $email, $firstName, $fileId, $lastName, $basePath);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }

    // Process template
    echo Template::serve('sample22.htm');