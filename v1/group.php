<?php
$getUserGroup = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();

$textReturn = "pictureUrl \n" . $groupId . " ";
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->userId
));
$textReturn = "displayName \n" . $userID . " ";
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->pictureUrl
));
$textReturn = "All \n" . $strAccessToken . " ";
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->userId
));


reply($replyToken,'',$messages);