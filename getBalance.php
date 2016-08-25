<?php
require_once("package/Database.php");

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


#連結資料庫
$db = new Database();
$ip = $db->getIp();
$username = $db->strSqlReplace($username);

#檢查帳戶是否已註冊
$sql = "SELECT account_money as Balance FROM `account` WHERE `account_name` = '$username' ";

$row = $db->select($sql);

// var_dump($row);
// exit;
if(empty($row)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'acount no register';
    echo json_encode($arr_result);
    exit;
}

$arr_result['result'] = true;
$arr_result['data'] = $row['0'];
echo json_encode($arr_result);
exit;

