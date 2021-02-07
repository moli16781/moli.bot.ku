<?php
$getUser = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();

$textReturn = "pictureUrl " . $groupId . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));
$textReturn = "displayName " . $userID . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));
$textReturn = "All " . $strAccessToken . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));


reply($replyToken,'',$messages);