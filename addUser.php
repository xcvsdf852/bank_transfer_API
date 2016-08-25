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

#連結資料庫
$db = new Database();
$ip = $db->getIp();
$username = $db->strSqlReplace($username);

#檢查帳戶是否已註冊
$sql = "SELECT  COUNT( * ) AS c FROM `account` WHERE `account_name` = '$username' ";

$row = $db->select($sql);

if($row['0']['c'] > 0){
    $arr_result['result'] = false;
    $arr_result['data'] = 'repeat acount';
    echo json_encode($arr_result);
    exit;
}

// var_dump($row);
// exit;
#當查詢為空時，開始新增帳戶

$sql_INSERT = "INSERT INTO `account`(`account_name`, `account_money`) VALUES ('$username','100000')";

$row_INSERT = $db->insert($sql_INSERT);

#新增失敗
if(!$row_INSERT){
    $arr_result['result'] = false;
    $arr_result['data'] = 'Add acount failure';
    echo json_encode($arr_result);
    exit;
}


$arr_result['result'] = true;
$arr_result['data'] = 'Add acount Successful';
echo json_encode($arr_result);
exit;