<?php
$getUserGroup = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();
$textReturn = "รายละเอียด \n" . $getUserGroup . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

reply($replyToken,'',$messages);