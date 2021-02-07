<?php
$getUserGroup = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();
array_push($messages, array(
    'type' => 'text',
    'text' => $getUserGroup
));



reply($replyToken,'',$messages);