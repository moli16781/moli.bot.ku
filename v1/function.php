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

  curl($arrPostData);
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
