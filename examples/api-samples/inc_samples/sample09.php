<?php
    //<i>This sample will show how to use <b>GuId</b> of file to generate an embedded Viewer URL for a Document</i>

    //###Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileGuId = f3::get('POST["fileId"]');
    $width = f3::get('POST["width"]');
    $height = f3::get('POST["height"]');
    
    function Iframe($file_Id, $width='400', $height='650')
    {
        //###Check fileGuId
        if (empty($file_Id)) {
            throw new Exception('Please enter FILE ID');
        } else {
            //Generation of iframe URL using fileGuId
            $iframe = 'https://apps.groupdocs.com/document-viewer/embed/' . $file_Id . '?frameborder="0" width="450" height="650"';
            //If request was successfull - set url variable for template
            return f3::set('url', $iframe);
        }
    }
    
    try {
        Iframe($fileGuId, $width, $height);
    } catch(Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }
    //Process template
    F3::set('userId', $clientId);
    F3::set('privateKey', $privateKey);
    F3::set('width', $width);
    F3::set('height', $height);
    f3::set('fileId', $fileGuId);
    echo Template::serve('sample09.htm');