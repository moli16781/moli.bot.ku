<?php
// date_default_timezone_set('Asia/Bangkok');
$content = file_get_contents('php://input');
file_put_contents('textLine.txt', json_encode(json_decode($content, true), JSON_PRETTY_PRINT), FILE_APPEND);
// die();
$arrJson = json_decode($content, true);
$LINE_ID = '1655217396';
$LINE_SECRET = '1606d35a13d769e9182190be78eb08be';
$line = new Line($LINE_ID, $LINE_SECRET);
$obj = json_decode($line->getTokenForChanal());
$strAccessToken = $obj->access_token;

$API_PUSH = "https://api.line.me/v2/bot/message/push";
$API_REPLY = "https://api.line.me/v2/bot/message/reply";

$strUrl = $API_REPLY;
$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer $strAccessToken";
if ($arrJson['events'][0]['source']['type'] == 'user') {

    $userID = $arrJson['events'][0]['source']['userId'];
    $messageText = trim($arrJson['events'][0]['message']['text']);
    $replyToken = trim($arrJson['events'][0]['replyToken']);
    $hasUser = checkUser($userID);
    if (!filter_var($messageText, FILTER_VALIDATE_URL) && $hasUser->num_rows == 0) {
        $tmpKeyword = explode(':', $messageText);
    }
    if (count($tmpKeyword) == 1 && $hasUser->num_rows == 0) {
        ## Check LOGIN
        // $obj_profile = json_decode($line->getProfile($arrJson['events'][0]['source']['userId'],$strAccessToken));
        $textReturn = "กรุณาเข้าสู่ระบบ \n";
        $textReturn .= "พิมพ์ user:password \nเช่น user@000001:passw0rd \n";
        $textReturn .= "\nปล. หากพบปัญหาในการใช้งานกรุณาติดต่อ 096-7567844 Peerapat Matheang";
        // replyText($userID,$textReturn); //API_PUSH
        reply($replyToken, $textReturn);
        exit(0);
    } elseif (count($tmpKeyword) == 2) {
        ## LOGIN

        $loginMember = checkUserLogin($tmpKeyword[0], $tmpKeyword[1]);
        if ($loginMember->num_rows != 0) {
            $updateMember = updateUser($userID, $tmpKeyword[0], $tmpKeyword[1]);
            // replyText($userID, 'ลงทะเบียนสำเร็จ');
            $messages = array();
            array_push($messages, array(
                'type' => 'text',
                'text' => 'ลงทะเบียนสำเร็จ'
            ));
            $hasUser = checkUser($userID);
            $rowUser = $hasUser->fetch_assoc();
            $textReturn = "โปรไฟล์ \n";
            $textReturn .= "ชื่อ " . $rowUser['name'] . " \n";
            $textReturn .= "โควต้าสปินคงเหลือ " . number_format($rowUser['quota']) . " \n";
            $textReturn .= "โควต้าลิงก์คงเหลือ " . number_format($rowUser['quota_link']) . " \n";
            if ($rowUser['parent_id'] != 0) {
                $textReturn .= "สังกัด " . $rowUser['parent_id'] . " \n";
            }
            $textReturn .= "\nปล. หากพบปัญหาในการใช้งานกรุณาติดต่อ 096-7567844 Peerapat Matheang ";
            // replyText($userID, $textReturn);
            array_push($messages, array(
                'type' => 'text',
                'text' => $textReturn
            ));
            reply($replyToken, '', $messages);
            exit(0);
        } else {
            // replyText($userID, 'Username หรือ Password ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            reply($replyToken, 'Username หรือ Password ไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
            exit(0);
        }
    }
    $keywordDie = array('', 'ลิงก์', 'สปิน');
    if ($arrJson['events'][0]['type'] == 'message') {
        if ($arrJson['events'][0]['message']['type'] == 'text') {
            ##EXPLODE TEXT
            // $userID = $arrJson['events'][0]['source']['userId'];
            // $messageText = $arrJson['events'][0]['message']['text'];
            if (filter_var($messageText, FILTER_VALIDATE_URL)) {
                $checkUrlCode = checkUrlCode($messageText, $userID);
                if ($checkUrlCode['status'] == true) {
                    // replyTypeTemplate_Type($userID);
                    $messages = array(
                        array(
                            'type' => 'flex',
                            'altText' => 'ตัวเลือก',
                            'contents' => array(
                                'type' => 'bubble',
                                'styles' => array(
                                    'footer' => array(
                                        'separator' => true
                                    ),
                                ),
                                'body' => array(
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'contents' => array(
                                        array(
                                            'type' => 'text',
                                            'text' => 'กรุณาเลือกรูปแบบการทำงาน',
                                        ),
                                    ),
                                ),
                                'footer' => array(
                                    'type' => 'box',
                                    'layout' => 'horizontal',
                                    'contents' => array(
                                        array(
                                            'type' => 'button',
                                            'style' => 'link',
                                            'action' => array(
                                                'type' => 'postback',
                                                'data' => 'action=logs&type=1',
                                                'label' => 'ลิงก์',
                                                'text' => 'ลิงก์',
                                            ),
                                        ),
                                        array(
                                            'type' => 'button',
                                            'style' => 'link',
                                            'action' => array(
                                                'type' => 'postback',
                                                'data' => 'action=logs&type=2',
                                                'label' => 'สปิน',
                                                'text' => 'สปิน',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    );
                    reply($replyToken, '', $messages);
                } else {
                    // replyText($userID, $checkUrlCode['msg']);
                    reply($replyToken, $checkUrlCode['msg']);
                }
                exit(0);
            } elseif ($messageText == 'วิธีใช้งาน') {
                $textReturn = "วิธีใช้งาน nd-coin \n";
                $textReturn .= "1. ส่งลิงก์เข้ามาทางแชทของ nd-coin \n";
                $textReturn .= "2. รอการตอบกลับเพื่อขอจำนวนรอบจาก nd-coin \n";
                $textReturn .= "3. หากต้องการยกเลิกระหว่างการทำรายการ ให้พิมพ์ว่า 'ยกเลิก' หรือวางลิงก์เชิญเพื่อนใหม่ได้เลย \n";
                $textReturn .= "\nปล. หากพบปัญหาในการใช้งานกรุณาติดต่อ 096-7567844 Peerapat Matheang ";
                // replyText($userID, $textReturn);
                reply($replyToken, $textReturn);
                exit(0);
            } elseif ($messageText == 'โปรไฟล์') {
                $hasUser = checkUser($userID);
                $rowUser = $hasUser->fetch_assoc();
                $textReturn = "โปรไฟล์ \n";
                $textReturn .= "ชื่อ " . $rowUser['name'] . " \n";
                $textReturn .= "โควต้าสปินคงเหลือ " . number_format($rowUser['quota']) . " \n";
                $textReturn .= "โควต้าลิงก์คงเหลือ " . number_format($rowUser['quota_link']) . " \n";
                if ($rowUser['parent_id'] != 0) {
                    $textReturn .= "สังกัด " . $rowUser['parent_id'] . " \n";
                }
                $textReturn .= "\nปล. หากพบปัญหาในการใช้งานกรุณาติดต่อ 096-7567844 Peerapat Matheang ";
                // replyText($userID, $textReturn);
                reply($replyToken, $textReturn);
                exit(0);
            } elseif ($messageText == 'คิวงาน') {
                // replyText($userID, 'coming soon');
                $hasUser = checkUser($userID);
                $rowUser = $hasUser->fetch_assoc();
                $dash_task_link = funcTaskLink($rowUser['id']);
                $textReturn = "คิวงานลิงก์\n";
                $textReturn .= "ลิงก์ที่ทำไปทั้งหมด (ลิงก์) : ".number_format($dash_task_link['tasks_all_link'])." \n";
                $textReturn .= "โควต้าคงเหลือ (ลิงก์) : ".number_format($rowUser['quota_link'])." \n";
                $textReturn .= "คิวงานทั้งหมดวันนี้ (คิว) : ".number_format($dash_task['tasks_day'])." \n";
                $textReturn .= "คิวที่กำลังทำงาน (คิว) : ".number_format($dash_task_link['tasks_inprogress'])." \n";
                $textReturn .= "คิวที่รอทำงาน (คิว) : ".number_format($dash_task_link['tasks_pending'])." \n";
                $textReturn .= "\n";
                $dash_task = funcTask($rowUser['id']);
                $textReturn .= "คิวงานสปิน\n";
                $textReturn .= "สปินที่ทำไปทั้งหมด (สปิน) : ".number_format($dash_task['tasks_all_spin'])." \n";
                $textReturn .= "โควต้าคงเหลือ (สปิน) : ".number_format($rowUser['quota'])." \n";
                $textReturn .= "คิวที่กำลังทำงาน (คิว) : ".number_format($dash_task['tasks_inprogress'])." \n";
                $textReturn .= "คิวที่รอทำงาน (คิว) : ".number_format($dash_task['tasks_pending'])." \n";
                $textReturn .= "คิวที่ยกเลิก (คิว) : ".number_format($dash_task['tasks_reject'])." \n";
                $textReturn .= "คิวทั้งหมด (คิว) : ".number_format($dash_task['tasks_all'])." \n";
                $textReturn .= "\nปล. หากพบปัญหาในการใช้งานกรุณาติดต่อ 096-7567844 Peerapat Matheang ";
                // replyText($userID, $textReturn);
                reply($replyToken, $textReturn);
                
                exit(0);
            }elseif ($messageText == 'ยกเลิก') {
                // replyText($userID, 'coming soon');
                delLogsLine($userID);
                reply($replyToken, '-ยกเลิกการทำรายการก่อนหน้านี้แล้ว-');
                exit(0);
            } elseif (is_numeric($messageText)) {
                $checkTypeLogs = checkTypeLogs($userID);
                if ($checkTypeLogs->num_rows != 0) {

                    $row = $checkTypeLogs->fetch_assoc();
                    if ($row["type"] == 1) { // 1 ลิงก์

                        $checkRoundLogsType = checkRoundLogsType($userID, 1);
                        if ($checkSpinLogsType->num_rows == 0) {
                            if ((int)$messageText <= 0 || (int)$messageText > 20) {
                                // replyText($userID, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                reply($replyToken, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                exit(0);
                            }
                            $rowUser = $hasUser->fetch_assoc();
                            if ((int)$messageText <= 0) {
                                // replyText($userID, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                reply($replyToken, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                exit(0);
                            }
                            if ($rowUser['quota_link'] < (int)$messageText) {
                                // replyText($userID, 'โควต้าลิงก์ไม่เพียงพอ คงเหลือ ' . number_format($rowUser['quota_link']));
                                reply($replyToken, 'โควต้าลิงก์ไม่เพียงพอ คงเหลือ ' . number_format($rowUser['quota_link']));
                                exit(0);
                            }

                            updateRoundLogs($userID, (int)$messageText);
                            $checkUser = checkUser($userID);
                            $rowUser = $checkUser->fetch_assoc();
                            $insertRound = insertRound($rowUser['id'], (int)$messageText, $row["code"]);
                            if ($insertRound['status'] == true) {
                                // replyText($userID, 'เสร็จสิ้น Task ID : ' . $insertRound['msg']);
                                reply($replyToken, 'เสร็จสิ้น Task ID : ' . $insertRound['msg']);
                                delLogsLine($userID);
                                exit(0);
                            } else {
                                // replyText($userID, 'ไม่สำเร็จ');
                                reply($replyToken, 'ไม่สำเร็จ');
                                exit(0);
                            }
                        } else {
                            // replyText($userID, 'คุณกรอกจำนวนรอบเรียบร้อยแล้ว');
                            reply($replyToken, 'คุณกรอกจำนวนรอบเรียบร้อยแล้ว');
                            exit(0);
                        }
                    } elseif ($row["type"] == 2) { // 1 สปิน
                        $checkSpinLogsType = checkSpinLogsType($userID, 2);
                        if ($checkSpinLogsType->num_rows == 0) {

                            if (!in_array((int)$messageText, array('30', '40', '50', '60', '75', '90', '100', '110', '120', '125', '150'))) {
                                // replyText($userID, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                                reply($replyToken, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                                exit(0);
                            }
                            $rowUser = $hasUser->fetch_assoc();
                            if ((int)$messageText <= 0) {
                                // replyText($userID, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                                reply($replyToken, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                                exit(0);
                            }
                            if ($rowUser['quota'] < (int)$messageText) {
                                // replyText($userID, 'โควต้าสปินไม่เพียงพอ คงเหลือ ' . number_format($rowUser['quota']));
                                reply($replyToken, 'โควต้าสปินไม่เพียงพอ คงเหลือ ' . number_format($rowUser['quota']));
                                exit(0);
                            }

                            updateSpinLogs($userID, (int)$messageText);
                            // replyText($userID, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                            reply($replyToken, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                            exit(0);
                        }

                        $checkRoundLogsType = checkRoundLogsType($userID, 2);
                        if ($checkRoundLogsType->num_rows == 0) {
                            if ((int)$messageText <= 0 || (int)$messageText > 20) {
                                // replyText($userID, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                reply($replyToken, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                                exit(0);
                            }
                            updateRoundLogs($userID, (int)$messageText);
                            $checkUser = checkUser($userID);
                            $rowUser = $checkUser->fetch_assoc();
                            $checkLogsLine = checkLogsLine($userID);
                            $rowLogs = $checkLogsLine->fetch_assoc();
                            $insertSpin = insertSpin($rowUser['id'], (int)$rowLogs['spin'], (int)$rowLogs['round'], $row["code"]);
                            if ($insertSpin['status'] == true) {
                                // replyText($userID, 'เสร็จสิ้น Task ID : ' . $insertSpin['msg']);
                                reply($replyToken, 'เสร็จสิ้น Task ID : ' . $insertSpin['msg']);
                                delLogsLine($userID);
                            } else {
                                // replyText($userID, 'ไม่สำเร็จ');
                                reply($replyToken, 'ไม่สำเร็จ');
                            }
                        } else {
                            // replyText($userID, 'คุณกรอกจำนวนรอบเรียบร้อยแล้ว');
                            reply($replyToken, 'คุณกรอกจำนวนรอบเรียบร้อยแล้ว');
                            exit(0);
                        }
                    }
                }
                exit(0);
            } elseif (array_search($messageText, $keywordDie) == false) {
                // replyText($userID, 'หากไม่เข้าใจการใช้งานให้กดที่เมนู "วิธีใช้งาน"');
                reply($replyToken, 'หากไม่เข้าใจการใช้งานให้กดที่เมนู "วิธีใช้งาน"');
                exit(0);
            } else exit(0);
        } else exit(0);
    } elseif ($arrJson['events'][0]['type'] == 'postback') {
        $checkLogsLine = checkLogsLine($userID);
        if ($checkLogsLine->num_rows == 0) {
            // replyText($userID, 'กรุณาส่งส่งลิงก์เข้ามาทางแชทของ nd-coin');
            reply($replyToken, 'กรุณาส่งส่งลิงก์เข้ามาทางแชทของ nd-coin');
            exit(0);
        }
        $checkTypeLogs = checkTypeLogs($userID);
        if ($checkTypeLogs->num_rows == 0) {
            parse_str($arrJson['events'][0]['postback']['data'], $data);
            if ($data['action'] == 'logs') {
                if ($data['type'] == 1) {
                    insertTypeLogs($userID, 1);
                    // replyText($userID, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                    reply($replyToken, 'กรุณากรอกจำนวนรอบตั้งแต่ 1-20 เท่านั้น');
                } elseif ($data['type'] == 2) {
                    insertTypeLogs($userID, 2);
                    // replyText($userID, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                    reply($replyToken, 'กรุณากรอกสปิน 30, 40, 50, 60, 75, 90, 100, 110, 120, 125, 150 เท่านั้น');
                }
            }
        } else {
            // replyText($userID, 'คุณเลือกรูปแบบการทำงานเรียบร้อยแล้ว ไม่สามารถเลือกรูปแบบการทำงานได้อีก');
            reply($replyToken, 'คุณเลือกรูปแบบการทำงานเรียบร้อยแล้ว ไม่สามารถเลือกรูปแบบการทำงานได้อีก');
        }
        exit(0);
    } else exit(0);



    exit(0);
}
