<?php
$messages = array();
$textReturn = "รายละเอียด \n" . $content . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));

$getUserGroup = $line->getProfileGroup($groupId,$userID,$strAccessToken);
$textReturn = "รายละเอียด \n" . $getUserGroup . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));
$getUserGroup = json_decode($getUserGroup);
$textReturn = "displayName \n" . $getUserGroup->displayName . " \n";
$textReturn .= "pictureUrl \n" . $getUserGroup->pictureUrl . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));


reply($replyToken,'',$messages);