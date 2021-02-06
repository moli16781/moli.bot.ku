<?php
// http_response_code(200);
// header("Content-Type: application/json");
switch ($_GET['v']) {
    case '1':
        require 'v1/controller.php';
        break;
    default:
        echo json_encode(array('status' => false, 'msg' => 'VERSION: Not correct.'));
        exit(0);
        break;
}
?>