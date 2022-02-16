<?php

require_once '../_loader.php';

$receivers = [
    'stui@isleoflan.ch' => 'Steve'
];

$newsletter = new \IOL\Newsletter\v1\Content\Newsletter(1002);

foreach($receivers as $email => $name){
    try {
        $newsletter->send($name, new \IOL\Newsletter\v1\DataType\Email($email));
    } catch (\IOL\Newsletter\v1\Exceptions\IOLException $e) {
        error_log($e->getMessage());
    }
}
