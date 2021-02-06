<?php

if (isset($_GET['action'])) {
    switch (trim($_GET['action'])) {
        case 'line':
            require 'Line/config.php';
            require 'function.php';
            require 'line.php';
            break;
    
        default:
            echo json_encode(array('status' => false, 'msg' => 'Not Found: action'));
            exit(0);
            break;
    }
} else {
    echo json_encode(array('status' => false, 'msg' => 'Not Params: action'));
    exit(0);
}
