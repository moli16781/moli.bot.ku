<?php

$getUser = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));       
$textReturn .= "pictureUrl " . $getUser->pictureUrl . " \n";
$textReturn .= "displayName " . $getUser->displayName . " \n";
$textReturn .= "All " . $getUser . " \n";

$messages = array();
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

reply($replyToken,'',$messages);