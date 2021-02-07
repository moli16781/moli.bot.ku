<?php

$getUser = json_decode($line->getProfile($userID,$strAccessToken));       
$textReturn .= "รายละเอียด " . $getUser->pictureUrl . " \n";
$textReturn .= "รายละเอียด " . $getUser . " \n";

$messages = array();
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

reply($replyToken,'',$messages);