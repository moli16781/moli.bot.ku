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
$replyToken = trim($arrJson['events'][0]['replyToken']);
$messageText = trim($arrJson['events'][0]['message']['text']);
$groupId = $arrJson['events'][0]['source']['groupId'];

if ($arrJson['events'][0]['source']['type'] == 'user') {
    require 'user.php';
    exit(0);
}else if ($arrJson['events'][0]['type'] == 'memberJoined') {
    require 'memberJoined.php';
    exit(0);
}else if ($arrJson['events'][0]['source']['type'] == 'group') {
    require 'group.php';
    exit(0);
}