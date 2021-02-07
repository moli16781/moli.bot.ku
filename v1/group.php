<?php

// $getUser = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));       
$textReturn .= "pictureUrl " . $groupId . " \n";
$textReturn .= "displayName " . $userID . " \n";
$textReturn .= "All " . $strAccessToken . " \n";

$messages = array();
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

reply($replyToken,'',$messages);