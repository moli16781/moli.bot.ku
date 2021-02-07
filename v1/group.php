<?php
$getUserGroup = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();

array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->displayName
));
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->pictureUrl
));
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup->statusMessage
));


reply($replyToken,'',$messages);