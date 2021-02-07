<?php
$getUserGroup = $line->getProfileGroup($groupId,$userID,$strAccessToken);
$textReturn = "รายละเอียด \n" . $getUserGroup . " \n";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));
$getUserGroup = json_decode($getUserGroup);
$textReturn = "สวัสดี คุณ " . $getUserGroup->displayName . " ขอบคุณที่เข้าร่วมกลุ่มเรา";
array_push($messages, array(
    'type' => 'text',
    'text' => $textReturn
));


reply($replyToken,'',$messages);