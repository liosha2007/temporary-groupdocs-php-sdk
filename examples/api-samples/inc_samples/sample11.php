<?php
    //### This sample will show how programmatically create and post an annotation into document. How to delete the annotation
     

    //### Set variables and get POST data
    F3::set('userId', '');
    F3::set('privateKey', '');
    F3::set('fileId', '');
    $clientId = F3::get('POST["client_id"]');
    $privateKey = F3::get('POST["private_key"]');
    $fileId = F3::get('POST["fileId"]');

    function CreateAnnotation($clientId, $privateKey, $fileId)
    {
        if (empty($clientId) || empty($privateKey) || empty($fileId)) {
            throw new Exception('Please enter all required parameters');
        } else {

            //### Create Signer, ApiClient and Annotation Api objects
            // Create signer object
            $signer = new GroupDocsRequestSigner($privateKey);
            // Create apiClient object
            $apiClient = new ApiClient($signer);
            // Create Annotation object
            $ant = new AntApi($apiClient);

            $annotationType = F3::get('POST["annotation_type"]');
            $replyText = F3::get('POST["text"]');

            // Delete annotation if Delete Button clicked
            if (F3::get('POST["delete_annotation"]') == "1") {
                $ant->DeleteAnnotation($clientId, F3::get('POST["annotationId"]'));
                return;
            }

            // Required parameters
            $allParams = array('annotation_type', 'box_x', 'box_y', 'text');

            // Added required parameters depends on  annotation type ['type' or 'area']
            if ($annotationType == "text")
                $allParams = array_merge($allParams, array('box_width', 'box_height', 'annotationPosition_x', 'annotationPosition_y', 'range_position', 'range_length'));
            elseif ($annotationType == "area")
                $allParams = array_merge($allParams, array('box_width', 'box_height'));

           // Checking required parameters
            foreach ($allParams as $param) {
                $needParam = F3::get('POST["' . $param .'"]');
                if ( !isset($needParam) or empty($needParam)) {
                    throw new Exception('Please enter all required parameters');
                }
            }

            $types = array('text' => "0", "area" => "1", "point" => "2");

            // reply text
            $reply = new AnnotationReplyInfo();
            $reply->text = $replyText;

            // Annotation Info
            $ann = new AnnotationInfo();
            $ann->replies = array($reply);
            $ann->type = $types[$annotationType];

            // construct annotation info depends on annotation type
            // text annotation
            if ($annotationType == "text") {

                $range = new Range();
                $range->position = F3::get('POST["range_position"]');
                $range->length = F3::get('POST["range_length"]');

                $box = new Rectangle();
                $box->x = F3::get('POST["box_x"]');
                $box->y = F3::get('POST["box_y"]');
                $box->width = F3::get('POST["box_width"]');
                $box->height = F3::get('POST["box_height"]');

                $point = new Point();
                $point->x = F3::get('POST["annotationPosition_x"]');
                $point->y = F3::get('POST["annotationPosition_y"]');

                $ann->box = $box;
                $ann->annotationPosition = $point;
                $ann->range = $range;

            // area annotation
            } elseif ($annotationType == "area") {

                $box = new Rectangle();
                $box->x = F3::get('POST["box_x"]');
                $box->y = F3::get('POST["box_y"]');
                $box->width = F3::get('POST["box_width"]');
                $box->height = F3::get('POST["box_height"]');

                $point = new Point();
                $point->x = 0;
                $point->y = 0;

                $ann->box = $box;
                $ann->annotationPosition = $point;

            // point annotation
            } elseif ($annotationType == "point") {

                $box = new Rectangle();
                $box->x = F3::get('POST["box_x"]');
                $box->y = F3::get('POST["box_y"]');
                $box->width = 0;
                $box->height = 0;

                $point = new Point();
                $point->x = 0;
                $point->y = 0;

                $ann->box = $box;
                $ann->annotationPosition = $point;
            }

            F3::set('userId', $clientId);
            F3::set('privateKey', $privateKey);
            F3::set('fileId', $fileId);

            $createResult = $ant->CreateAnnotation($clientId, $fileId, $ann);
            if ($createResult->status == "Ok") {
                if ($createResult->result) {
                    
                    $iframe = 'https://apps.groupdocs.com//document-annotation2/embed/' . $createResult->result->documentGuid . '?frameborder="0" width="720" height="600"';
                    F3::set('annotationId', $createResult->result->annotationGuid);
                    F3::set('annotationType', $annotationType);
                    F3::set('annotationText', $replyText);
                    F3::set('url', $iframe);
                }
            }
        }
    }

    try {
        CreateAnnotation($clientId, $privateKey, $fileId);
    } catch (Exception $e) {
        $error = 'ERROR: ' .  $e->getMessage() . "\n";
        f3::set('error', $error);
    }

    // Process template
    echo Template::serve('sample11.htm');