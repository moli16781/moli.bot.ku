<?php
$messages = array();
$getUserGroup = $line->getProfileGroup($groupId,$userID,$strAccessToken);
$textReturn = "รายละเอียด \n" . $getUserGroup . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));
$getUserGroup = json_decode($getUserGroup);
$textReturn = "รายละเอียด \n" . $getUserGroup->userID . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));


reply($replyToken,'',$messages);