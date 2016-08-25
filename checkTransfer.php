<?php

require_once("package/Database.php");
$arr_result = array();

$arr_result = array();
if(!isset($_GET['username']) || empty($_GET['username'])){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data get error';
    echo json_encode($arr_result);
    exit;
}
$username = $_GET['username'];

if(!filter_var($username,  FILTER_SANITIZE_STRING)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data type error';
    echo json_encode($arr_result);
    exit;
}

#檢查transid
if(!isset($_GET['transid']) || empty($_GET['transid'])){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data get error';
    echo json_encode($arr_result);
    exit;
}
$transid = $_GET['transid'];
if(!filter_var($transid,  FILTER_VALIDATE_INT)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data type error';
    echo json_encode($arr_result);
    exit;
}

#連結資料庫
$db = new Database();
$ip = $db->getIp();
$username = $db->strSqlReplace($username);



#檢查帳戶是否已註冊
$sql = "SELECT *  FROM `transfer_log` WHERE `log_transid` = '$transid' AND `log_account` = '$username'";
$row = $db->select($sql);

if(empty($row)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'no seach find';
    echo json_encode($arr_result);
    exit;
}

if($row['0']['log_result'] == '1'){
    $arr_result['result'] = true;
    $arr_result['data'] = 'Transfer Successful';
    echo json_encode($arr_result);
    exit;
}else{
    $arr_result['result'] = false;
    $arr_result['data'] = 'Transfer fail';
    echo json_encode($arr_result);
    exit;
}


