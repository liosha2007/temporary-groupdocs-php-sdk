<?php
/**
 * Created by JetBrains PhpStorm.
 * User: svarog
 * Date: 01.02.13
 * Time: 9:15
 * To change this template use File | Settings | File Templates.
 */
    $post = f3::get('POST');

    if(!empty($post)) {
        $fp = fopen(__DIR__ . '/../temp/sign.txt', 'w');

        foreach($post as $name => $content) {
            fwrite($fp, $name . ' => ' . $content . " ; ");
        }

        fclose($fp);
    }

header('Location: /sample21');