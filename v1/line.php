<?php
// date_default_timezone_set('Asia/Bangkok');
$content = file_get_contents('php://input');
// file_put_contents('textLine.txt', json_encode(json_decode($content, true), JSON_PRETTY_PRINT), FILE_APPEND);
// die();
$arrJson = json_decode($content, true);
$LINE_ID = '1655624949';
$LINE_SECRET = '2faf5eceb595d6265db5fdf0bfa48510';
$line = new Line($LINE_ID, $LINE_SECRET);
$obj = json_decode($line->getTokenForChanal());
$strAccessToken = $obj->access_token;

$API_PUSH = "https://api.line.me/v2/bot/message/push";
$API_REPLY = "https://api.line.me/v2/bot/message/reply";

$strUrl = $API_REPLY;
$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer $strAccessToken";

$userID = $arrJson['events'][0]['source']['userId'];
$groupId = $arrJson['events'][0]['source']['groupId'];
$messageText = trim($arrJson['events'][0]['message']['text']);
$replyToken = trim($arrJson['events'][0]['replyToken']);

if ($arrJson['events'][0]['source']['type'] == 'user') {

    $getUser = $line->getProfile($userID,$replyToken); 
    $textReturn .= "รายละเอียด \n" . $obj . "";
  
    $messages = array();
    array_push($messages, array(
        'type' => 'text',
        'text' => $textReturn
    ));

    reply($replyToken,'',$messages);
    

    exit(0);
}else if ($arrJson['events'][0]['source']['type'] == 'group') {

    $getUser = $line->getProfile($userID,$replyToken);       
    $textReturn .= "รายละเอียด " . $getUser . " \n";
    
    $messages = array();
    array_push($messages, array(
        'type' => 'text',
        'text' => $textReturn
    ));
    
    reply($replyToken,'',$messages);

    exit(0);
}