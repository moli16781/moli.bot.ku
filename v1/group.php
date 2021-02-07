<?php
// $getUserGroup = json_decode($line->getProfileGroup($groupId,$userID,$strAccessToken));

$messages = array();
$textReturn = "รายละเอียด \n" . $content . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

reply($replyToken,'',$messages);