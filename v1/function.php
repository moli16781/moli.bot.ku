<?php
## print pre ##

function print_pre($expression, $return = false, $wrap = false)
{
  $css = 'border:1px dashed #06f;background:#69f;padding:1em;text-align:left;z-index:99999;font-size:12px;position:relative';
  if ($wrap) {
    $str = '<p style="' . $css . '"><tt>' . str_replace(
      array('  ', "\n"),
      array('&nbsp; ', '<br />'),
      htmlspecialchars(print_r($expression, true))
    ) . '</tt></p>';
  } else {
    $str = '<pre style="' . $css . '">' . print_r($expression, true) . '</pre>';
  }
  if ($return) {
    if (is_string($return) && $fh = fopen($return, 'a')) {
      fwrite($fh, $str);
      fclose($fh);
    }
    return $str;
  } else
    echo $str;
}


function checkUser($userId = null)
{
  global $conn;
  $sql = "SELECT *
  FROM `users`
  WHERE 
  users.status = 'enable' AND 
  users.line_uid = '$userId'
  ";
  $result = $conn->query($sql);
  return $result;
}


function checkUserLogin($user = null, $pass = null)
{
  global $conn;
  $password = md5($pass);
  $sql = "SELECT *
  FROM `users`
  WHERE 
  users.status = 'enable' AND 
  users.username = '$user' AND
  users.password = '$password'
  ";
  $result = $conn->query($sql);
  return $result;
}


function funcTask($UserID = null){
global $conn;
$datetime = date('Y-m-d');
$rs = $conn->query("SELECT 
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin' AND DATE(tasks.created_at) = '$datetime') as tasks_day,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin' AND tasks.process = 'pending' AND tasks.`status` = 'enable') as tasks_pending,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin' AND tasks.process = 'inprogress' AND tasks.`status` = 'enable') as tasks_inprogress,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin' AND tasks.process = 'reject') as tasks_reject,
(SELECT SUM(tasks.round_x_count*tasks.count_spin) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin' AND tasks.process = 'success') as tasks_all_spin,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'spin') as tasks_all");
$dash_task = $rs->fetch_assoc();

$dash_task['quota'] = number_format($user['quota']);
$dash_task['tasks_day'] = number_format($dash_task['tasks_day']);
$dash_task['tasks_all_spin'] = number_format($dash_task['tasks_all_spin']);
$dash_task['tasks_inprogress'] = number_format($dash_task['tasks_inprogress']);
$dash_task['tasks_pending'] = number_format($dash_task['tasks_pending']);
$dash_task['tasks_reject'] = number_format($dash_task['tasks_reject']);
$dash_task['tasks_all'] = number_format($dash_task['tasks_all']);

return $dash_task;

}

function funcTaskLink($UserID = null){
global $conn;
$datetime = date('Y-m-d');
$rs = $conn->query("SELECT 
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link' AND DATE(tasks.created_at) = '$datetime') as tasks_day,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link' AND tasks.process = 'pending' AND tasks.`status` = 'enable') as tasks_pending,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link' AND tasks.process = 'inprogress' AND tasks.`status` = 'enable') as tasks_inprogress,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link' AND tasks.process = 'reject') as tasks_reject,
(SELECT SUM(tasks.round_x_count) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link' AND tasks.process = 'success') as tasks_all_link,
(SELECT COUNT(*) FROM tasks WHERE tasks.deleted_at IS NULL AND tasks.uid = '$UserID' AND tasks.type = 'link') as tasks_all");
$dash_task = $rs->fetch_assoc();

$dash_task['quota_link'] = number_format($user['quota_link']);
$dash_task['tasks_day'] = number_format($dash_task['tasks_day']);
$dash_task['tasks_all_link'] = number_format($dash_task['tasks_all_link']);
$dash_task['tasks_inprogress'] = number_format($dash_task['tasks_inprogress']);
$dash_task['tasks_pending'] = number_format($dash_task['tasks_pending']);
$dash_task['tasks_reject'] = number_format($dash_task['tasks_reject']);
$dash_task['tasks_all'] = number_format($dash_task['tasks_all']);

return $dash_task;


}

// function insertUser($data = null){
//   global $config, $db, $url;
//   $insert = array();
//   $insert[$config['users']['db'] . "_subject"] = changeQuot($data->displayName);
//   $insert[$config['users']['db'] . "_masterkey"] = "cu";
//   $insert[$config['users']['db'] . "_userid"] = changeQuot($data->userId);
//   $insert[$config['users']['db'] . "_credate"] = date("Y-m-d H:i:s");
//   $insertSQL = sqlinsert($insert, $config['users']['db'], $config['users']['db'] . "_id");
//   $contantID = $insertSQL['id'];
//   // print_pre($insertSQL);
//   return $contantID > 0 ? $contantID : 0;

// }

function updateUser($userId = null, $user = null, $pass = null)
{
  global $conn;
  $password = md5($pass);
  // $update = array();
  // $update[$config['users']['db'] . '_subject'] = changeQuot($data->displayName);
  // $update[$config['users']['db'] . '_lastdate'] = date("Y-m-d H:i:s");
  // $updateSQL = sqlupdate($update, $config['users']['db'], $config['users']['db'] . "_userid", "'$data->userId'");
  $sql = "UPDATE `users` SET users.line_uid = '$userId' WHERE users.username = '$user' AND  users.password = '$password' AND  users.status = 'enable'";
  $result = $conn->query($sql);
  return $result;
}

function checkUrlCode($code = null, $userId = null)
{
  global $conn;
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => $code,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",

    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT => "Moziella/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1",
    CURLOPT_FOLLOWLOCATION => true,

    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
    ),
  ));

  $isExpired = $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    // echo json_encode(array('status' => false, 'msg' => 'Error: code'));
    // exit(0);
    $result['status'] = false;
    $result['msg'] = 'Link ไม่ถูกต้อง';
  } else {
    $response = explode('<meta property="og:url" content="https://GetCoinMaster.com/', $response);
    $response = explode('"/>', $response[1]);
    $code = $response[0];

    $isExpired = explode('c=', $isExpired);
    $isExpired = explode('&', $isExpired[1]);
    $isExpired = $isExpired[0];
    if ($isExpired == 'expired') {
      // echo json_encode(array('status' => false, 'msg' => 'Error: code, ลิงก์ชวนเพื่อนหมดอายุแล้ว กรุณารับจากในตัวเกมส์ใหม่'));
      // exit(0);
      $result['status'] = false;
      $result['msg'] = 'ลิงก์ชวนเพื่อนหมดอายุแล้ว กรุณารับจากในตัวเกมส์ใหม่';
    } else {
      $conn->query("DELETE FROM logs_line WHERE line_uid = '$userId'");
      $datetime = date('Y-m-d H:i:s');
      $conn->query("INSERT INTO logs_line (code, line_uid, created_at, updated_at) VALUES ('$code','$userId', '$datetime', '$datetime')");
      $result['status'] = true;
      $result['msg'] = 'บันทึกลง Logs';
    }
  }
  return $result;
}

function checkLogsLine($userId = null)
{
  global $conn;
  $sql = "SELECT *
    FROM `logs_line`
    WHERE 
    logs_line.line_uid = '$userId'
    ";
  $result = $conn->query($sql);
  return $result;
}

function checkSpinLogsType($userId = null, $type = null)
{
  global $conn;
  $sql = "SELECT *
    FROM `logs_line`
    WHERE 
    logs_line.line_uid = '$userId' AND
    logs_line.type = '$type' AND
    logs_line.spin != ''
    ";
  $result = $conn->query($sql);
  return $result;
}

function checkRoundLogsType($userId = null, $type = null)
{
  global $conn;
  $sql = "SELECT *
    FROM `logs_line`
    WHERE 
    logs_line.line_uid = '$userId' AND
    logs_line.type = '$type' AND
    logs_line.round != ''
    ";
  $result = $conn->query($sql);
  return $result;
}

function delLogsLine($userId = null)
{
  global $conn;
  $conn->query("DELETE FROM logs_line WHERE line_uid = '$userId'");
}

function insertTypeLogs($userId = null, $type = null)
{
  global $conn;
  $datetime = date('Y-m-d H:i:s');
  $sql = "UPDATE `logs_line` SET logs_line.type = '$type', logs_line.updated_at = '$datetime'  WHERE logs_line.line_uid = '$userId'";
  $result = $conn->query($sql);
}
function updateRoundLogs($userId = null, $round = null)
{
  global $conn;
  $datetime = date('Y-m-d H:i:s');
  $sql = "UPDATE `logs_line` SET logs_line.round = '$round', logs_line.updated_at = '$datetime'  WHERE logs_line.line_uid = '$userId'";
  $result = $conn->query($sql);
}
function updateSpinLogs($userId = null, $spin = null)
{
  global $conn;
  $datetime = date('Y-m-d H:i:s');
  $sql = "UPDATE `logs_line` SET logs_line.spin = '$spin', logs_line.updated_at = '$datetime'  WHERE logs_line.line_uid = '$userId'";
  $result = $conn->query($sql);
}

function insertSpin($UserID = null, $count_spin = null, $round_x_count = null, $code = null)
{
  global $conn;
  $datetime = date('Y-m-d H:i:s');
  $sv_count = 105;
  $type = 'spin';
  $spin = $count_spin * $round_x_count;
  $sql = "SELECT ";
  for ($i = 1; $i <= $sv_count; $i++) {
    if ($i == $sv_count) {
      $sql .= "(SELECT COUNT(*) FROM tasks WHERE tasks.node = 'sv$i' AND tasks.process IN('pending','inprogress')) AS sv$i ";
    } else {
      $sql .= "(SELECT COUNT(*) FROM tasks WHERE tasks.node = 'sv$i' AND tasks.process IN('pending','inprogress')) AS sv$i, ";
    }
  }


  $rsSV = $conn->query($sql);
  $countSV = $rsSV->fetch_assoc();
  asort($countSV);
  $sv = array_keys($countSV);
  $sv_available = $sv[0];

  $conn->query("INSERT INTO tasks (code, round_x_count, count_spin, completed, uid, node, parent_id, type, created_at, updated_at) VALUES ('$code','$round_x_count','$count_spin', 0, '$UserID', '$sv_available', 0, '$type', '$datetime', '$datetime')");
  $task_id = $conn->insert_id;
  $conn->query("UPDATE users SET users.quota=users.quota-$spin, users.updated_at = '$datetime' WHERE users.id = '$UserID'");

  $result['status'] = true;
  $result['msg'] = $task_id;
  return $result;
}
function insertRound($UserID = null, $round_x_count = null, $code = null)
{
  global $conn;
  $sv_count = 105;
  $count_spin = 0;
  $type = 'link';
  $datetime = date('Y-m-d H:i:s');
  $sql = "SELECT ";
  for ($i = 1; $i <= $sv_count; $i++) {
    if ($i == $sv_count) {
      $sql .= "(SELECT COUNT(*) FROM tasks WHERE tasks.node = 'sv$i' AND tasks.process IN('pending','inprogress')) AS sv$i ";
    } else {
      $sql .= "(SELECT COUNT(*) FROM tasks WHERE tasks.node = 'sv$i' AND tasks.process IN('pending','inprogress')) AS sv$i, ";
    }
  }


  $rsSV = $conn->query($sql);
  $countSV = $rsSV->fetch_assoc();
  asort($countSV);
  $sv = array_keys($countSV);
  $sv_available = $sv[0];

  $listTask = array();
  for ($i = 0; $i < $round_x_count; $i++) {
    array_push($listTask, array('round_x_count' => 1, 'node' => $sv_available));
    $countSV[$sv_available]++;
    asort($countSV);
    $sv = array_keys($countSV);
    $sv_available = $sv[0];
  }

  $task_id = 0;
  $task_id_end = 0;
  $sql_insert = "INSERT INTO tasks (code, round_x_count, count_spin, completed, uid, node, parent_id, type, created_at, updated_at) VALUES ";
  foreach ($listTask as $key => $task) {
    $task_round_x_count = $task['round_x_count'];
    $task_node = $task['node'];
    if ($task_id == 0) {
      $conn->query("INSERT INTO tasks (code, round_x_count, count_spin, completed, uid, node, parent_id, type, created_at, updated_at) VALUES ('$code','$task_round_x_count','$count_spin', 0, '$UserID', '$task_node', 0, '$type', '$datetime', '$datetime')");
      $task_id = $conn->insert_id;
    } else {
      if ($key == 1) {
        $sql_insert .= " ('$code','$task_round_x_count','$count_spin', 0, '$UserID', '$task_node', $task_id, '$type', '$datetime', '$datetime')";
      } else {
        $sql_insert .= ", ('$code','$task_round_x_count','$count_spin', 0, '$UserID', '$task_node', $task_id, '$type', '$datetime', '$datetime')";
        // $task_id_end = $conn_primary->insert_id;
      }
    }
  }

  $conn->query($sql_insert);
  $conn->query("UPDATE users SET users.quota_link=users.quota_link-$round_x_count, users.updated_at = '$datetime' WHERE users.id = '$UserID'");
  $result['status'] = true;
  $result['msg'] = $task_id;
  return $result;
  // echo json_encode(array('status' => true, 'msg' => $task_id));
  // exit(0);
}

function checkTypeLogs($userId = null)
{
  global $conn;
  $sql = "SELECT *
    FROM `logs_line`
    WHERE 
    logs_line.line_uid = '$userId' AND
    logs_line.type != ''
    ";
  $result = $conn->query($sql);
  return $result;
}

function reply($replyToken = null, $msg = '', $messages = array())
{
  $arrPostData = array();
  if(empty($msg)){
    $arrPostData = array(
      'replyToken' => $replyToken,
      'messages' => $messages,
    );
  }else{
    $arrPostData = array(
      'replyToken' => $replyToken,
      'messages' => array(
        array(
          'type' => 'text',
          'text' => $msg
        ),
      ),
    );
  }
  curl($arrPostData);
}

function replyText($userId = null, $msg = '')
{
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'text',
        'text' => $msg
      ),
    ),
  );
  curl($arrPostData);
}

function replyImage($userId = null, $img = '')
{
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'image',
        'originalContentUrl' => $img,
        'previewImageUrl' => $img
      ),
    ),
  );
  curl($arrPostData);
}

function replyTypeTemplate($userId = null)
{
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'template',
        'altText' => 'ตัวเลือก',
        'template' => array(
          'type' => 'buttons',
          'title' => 'ตัวเลือก',
          'text' => 'กรุณาเลือกรูปแบบการใช้งาน',
          'actions' => array(
            array(
              'type' => 'postback',
              'label' => 'ลิงก์',
              'text' => 'ลิงก์',
              'data' => 'action=logs&type=1',
            ),
            array(
              'type' => 'postback',
              'label' => 'สปิน',
              'text' => 'สปิน',
              'data' => 'action=logs&type=2',
            ),
          ),
        ),
      ),
    ),
  );
  // echo json_encode($arrPostData,JSON_UNESCAPED_UNICODE);

  curl($arrPostData);
}


function replyTypeTemplate_confirm($userId = null, $data = null)
{
  global $url;
  $img_product = fileinclude($data['pic'], 'real', $data['masterkey'], 'link');
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'template',
        'altText' => 'ออเดอร์ซื้อสินค้า',
        'template' => array(
          'type' => 'buttons',
          'thumbnailImageUrl' => $img_product,
          'title' => funcSubStr($data['code'] . ' - ' . $data['subject']),
          'text' => number_format($data['price'], 2) . '฿',
          'actions' => array(
            array(
              'type' => 'uri',
              'label' => 'ดูรายละเอียดสินค้า',
              'uri' => _URL . 'product/detail/' . encodeStr($data['id']),
            ),
            array(
              'type' => 'postback',
              'label' => 'ยกเลิกการสั่งซื้อ',
              'text' => 'ยกเลิกการสั่งซื้อสินค้า',
              'data' => 'action=cancel&sku=' . $data['code'],
            ),
          ),
        ),
      ),
    ),
  );
  // echo json_encode($arrPostData,JSON_UNESCAPED_UNICODE);

  curl($arrPostData);
}

function replyTypeTemplate_success($userId = null, $data = null)
{
  global $url;
  $img_product = fileinclude($data['pic'], 'real', $data['masterkey'], 'link');
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'template',
        'altText' => 'ออเดอร์ซื้อสินค้า',
        'template' => array(
          'type' => 'buttons',
          'thumbnailImageUrl' => $img_product,
          'title' => funcSubStr($data['code'] . ' - ' . $data['subject']),
          'text' => number_format($data['price'], 2) . '฿ - คำสั่งซื้อได้รับการยืนยันแล้ว',
          'actions' => array(
            array(
              'type' => 'uri',
              'label' => 'ดูรายละเอียดสินค้า',
              'uri' => _URL . 'product/detail/' . encodeStr($data['id']),
            ),
          ),
        ),
      ),
    ),
  );
  // echo json_encode($arrPostData,JSON_UNESCAPED_UNICODE);

  curl($arrPostData);
}


function replyTypeTemplate_confirmOrder($userId = null, $hasUser = null, $data = null, $order_number = null, $type = 1)
{
  global $url, $config;

  $countOrder = 0; //จำนวนรายการทั้งหมด
  $sumPriceOrder = 0;
  $user = array();
  $contents = array();

  #เก็บรายการอาหารก่อน
  foreach ($data as $key => $row) {
    $tmp = array(
      'type' => 'box',
      'layout' => 'horizontal',
      'contents' => array(
        array(
          'type' => 'text',
          'text' => funcSubStr($row['code']),
          'color' => '#555555',
          'size' => 'sm',
          'flex' => 0,
        ),
        array(
          'type' => 'text',
          'text' => number_format($row['price'], 2) . '฿',
          'color' => '#111111',
          'size' => 'sm',
          'align' => 'end',
        ),
      ),
    );
    array_push($contents, $tmp);
    $countOrder++;
    $sumPriceOrder += $row['price'];
  }

  array_push($contents, array('type' => 'separator', 'margin' => 'xxl')); //เส้นบรรทัดแบ่งกลางระหว่างรายการกับราคารวม

  $tmp_count = array(
    'type' => 'box',
    'layout' => 'horizontal',
    'margin' => 'xxl',
    'contents' => array(
      array(
        'type' => 'text',
        'text' => 'จำนวน',
        'color' => '#555555',
        'size' => 'sm',
      ),
      array(
        'type' => 'text',
        'text' => $countOrder . ' ชิ้น',
        'color' => '#111111',
        'size' => 'sm',
        'align' => 'end',
      ),
    ),
  );
  array_push($contents, $tmp_count);

  $tmp_total = array(
    'type' => 'box',
    'layout' => 'horizontal',
    'contents' => array(
      array(
        'type' => 'text',
        'text' => 'ราคาทั้งหมด',
        'color' => '#555555',
        'size' => 'sm',
      ),
      array(
        'type' => 'text',
        'text' => number_format($sumPriceOrder, 2) . '฿',
        'color' => '#111111',
        'size' => 'sm',
        'align' => 'end',
      ),
    ),
  );
  array_push($contents, $tmp_total);

  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'flex',
        'altText' => 'คุณได้ยืนยันรายการที่สั่งซื้อแล้ว',
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
                'text' => 'ยืนยันการสั่งซื้อ',
                'weight' => 'bold',
                'color' => '#1DB446',
                'size' => 'sm',
              ),
              array(
                'type' => 'text',
                'text' => $hasUser->fields[$config['users']['db'] . "_subject"],
                'weight' => 'bold',
                'size' => 'xxl',
              ),
              array(
                'type' => 'text',
                'text' => 'ORDER NO. ' . $order_number,
                'color' => '#aaaaaa',
                'size' => 'sm',
                'wrap' => true,
              ),
              array(
                'type' => 'separator',
                'margin' => 'xxl',
              ),
              array(
                'type' => 'box',
                'layout' => 'vertical',
                'margin' => 'xxl',
                'spacing' => 'sm',
                'contents' => $contents
              ),
              array(
                'type' => 'separator',
                'margin' => 'xxl',
              ),
              array(
                'type' => 'box',
                'layout' => 'horizontal',
                'margin' => 'md',
                'contents' => array(
                  array(
                    'type' => 'text',
                    'text' => 'PAYMENT ID ' . $order_number,
                    'size' => 'xs',
                    'color' => '#aaaaaa',
                    'flex' => 0,
                  ),
                ),

              ),
            ),
          ),
        ),
      ),
    ),
  );

  curl($arrPostData);
  if ($type == 1) {
    replyText($userId, 'ยืนยันการสั่งซื้อสินค้าสำเร็จ กรุณาชำระเงินที่ ธนาคารกรุงไทย 857-043-7986 Intelligent system for selling products online พร้อมแนบสลิปหลังชำระเงิน กรุณาชำระภายใน 3 วัน  หากไม่ชำระภายใน 3 วัน ทางร้านขออนุญาตยกเลิกออเดอร์นะคะ ');
  }
  die();
}

function replyTypeTemplate_Type($userId = null)
{
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
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
    ),
  );

  curl($arrPostData);

  die();
}



function quickReply($userId = null)
{
  global $url;
  $arrPostData = array(
    'to' => $userId,
    'messages' => array(
      array(
        'type' => 'text',
        'text' => 'Quick reply',
        'quickReply' => array(
          'items' => array(
            array(
              'type' => 'action',
              'imageUrl' => '',
              'action' => array(
                'type' => 'postback',
                'label' => 'ตะกร้าสินค้า',
                'data' => 'action=checkOrder',
                'displayText' => "เช็ครายการสินค้า",
              ),
            ),
            array(
              'type' => 'action',
              'imageUrl' => '',
              'action' => array(
                'type' => 'postback',
                'label' => 'บัญชีธนาคาร',
                'data' => 'action=bank',
                'displayText' => "เลขบัญชีธนาคารร้านค้า",
              ),
            ),
            array(
              'type' => 'action',
              "action" => array(
                'type' => 'cameraRoll',
                'label' => 'แจ้งการโอนเงิน',
              ),
            ),
          ),
        ),
      ),
    ),
  );
  // echo json_encode($arrPostData,JSON_UNESCAPED_UNICODE);
  //     print_pre($arrPostData);
  curl($arrPostData);
}


function sendTextToBackend($data = null)
{

  global $db, $db_group;

  $sql = "SELECT * 

      FROM $db_group 

      WHERE 

      $db_group.$db_group" . "_type = 'admin' AND

      $db_group.$db_group" . "_status = 'Enable'

  ";

  // file_put_contents('logs.txt', "SQL=".$sql."\n".date('Y-m-d H:i:s',time())."\n",FILE_APPEND);

  $result = $db->query($sql);

  $listGroupId = array();

  if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

      array_push($listGroupId, $row[$db_group . '_groupid']);
    }

    foreach ($listGroupId as $key => $gid) {

      $arrPostData = array(

        'to' => $gid,

        'messages' => array(

          array(

            'type' => 'text',

            'text' => $data['events'][0]['message']['text']

          ),

        ),

      );

      curl($arrPostData);
    }
  }
}





function dd($val = '', $die = false)
{

  echo   '<style type="text/css">

        div.ddbug {

          /*background-color: black;*/

        }

        div.ddbug > textarea {

          background-color: black;

          color: #ff3e3e;

          font-weight: bold;

          width: 100%;

          height: 100px;

        }

      </style>';



  if (is_array($val)) {

    echo ('<div class="ddbug">');

    echo ('<textarea>');

    print_r($val);

    echo ('</textarea>');

    echo ('</div>');
  } elseif (is_object($val) || is_bool($val)) {

    echo ('<div class="ddbug">');

    echo ('<textarea>');

    var_dump($val);

    echo ('</textarea>');

    echo ('</div>');
  } else {

    echo ('<div class="ddbug">');

    echo ('<textarea>');

    echo ($val);

    echo ('</textarea>');

    echo ('</div>');
  }

  if ($die) die();
}



function curl($arrPostData)
{
  global $strUrl, $arrHeader;
  // print_pre($arrPostData);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $strUrl);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);
  // print_pre($result);
  return $result;
}


function getContentImg($datas)
{
  global $arrHeader;
  $datasReturn = [];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api.line.me/v2/bot/message/" . $datas['messageId'] . "/content");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  // curl_setopt_array($curl, array(
  //   CURLOPT_URL =>   "https://api.line.me/v2/bot/message/".$datas['messageId']."/content",
  //   CURLOPT_RETURNTRANSFER => true,
  //   CURLOPT_ENCODING => "",
  //   CURLOPT_MAXREDIRS => 10,
  //   CURLOPT_TIMEOUT => 30,
  //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //   CURLOPT_CUSTOMREQUEST => "GET",
  //   CURLOPT_POSTFIELDS => "",
  //   CURLOPT_HTTPHEADER => array(
  //     "Authorization: Bearer ".$datas['token'],
  //     "cache-control: no-cache"
  //   ),
  // ));

  $response = curl_exec($ch);
  $err = curl_error($ch);
  curl_close($ch);
  if ($err) {
    $datasReturn['result'] = false;
    $datasReturn['message'] = $err;
  } else {
    $datasReturn['result'] = true;
    $datasReturn['message'] = 'Success';
    $datasReturn['response'] = $response;
  }
  return $datasReturn;
}

function funcSubStr($title = null)
{
  $strRe = '';
  if (strlen($title) > 34) {
    $strRe = mb_substr($title, 0, 35, 'UTF-8') . '...';
  } else {
    $strRe = mb_substr($title, 0, 35, 'UTF-8');
  }

  return $strRe;
}
