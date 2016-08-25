<?php
require_once("package/Database.php");
$arr_result = array();
#檢查username
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

#檢查type
if(!isset($_GET['type']) || empty($_GET['type'])){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data get error';
    echo json_encode($arr_result);
    exit;
}
$type = $_GET['type'];
if($type != 'IN' && $type != 'OUT'){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data type error';
    echo json_encode($arr_result);
    exit;
}

#檢查amount
if(!isset($_GET['amount']) || empty($_GET['amount'])){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data get error';
    echo json_encode($arr_result);
    exit;
}
$amount = $_GET['amount'];
if(!filter_var($amount,  FILTER_VALIDATE_INT)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'data type error';
    echo json_encode($arr_result);
    exit;
}

#連結資料庫
$db = new Database();
$ip = $db->getIp();
$username = $db->strSqlReplace($username);
$transid = $db->strSqlReplace($transid);
$type = $db->strSqlReplace($type);
$amount = $db->strSqlReplace($amount);

#檢查帳戶是否已註冊
$sql = "SELECT account_money as Balance FROM `account` WHERE `account_name` = '$username' LOCK IN SHARE MODE";

$row = $db->select($sql);

// var_dump($row);
// exit;
if(empty($row)){
    $arr_result['result'] = false;
    $arr_result['data'] = 'acount no register';
    echo json_encode($arr_result);
    exit;
}
// $row['0'] 這是帳戶金額
#存款
if($type == 'IN'){
    #檢查序號是否重複
    // $sql = "SELECT COUNT(*) as c  FROM `transfer_log` WHERE `log_transid` = '$transid' AND `log_account` = '$username' AND `log_result` = '1' AND `log_type` = 'IN'";
    $sql = "SELECT COUNT(*) as c  FROM `transfer_log` WHERE `log_transid` = '$transid' AND `log_account` = '$username' AND `log_result` = '1'";
    $row = $db->select($sql);

    if($row['0']['c'] > 0){
        $arr_result['result'] = false;
        $arr_result['data'] = 'transid repeated';
        echo json_encode($arr_result);
        exit;
    }

    #更新實際金額
    $sql = "UPDATE `account` SET `account_money` = `account_money`+ $amount WHERE `account_name` = '$username' ";

    // echo $sql;
    // exit;
    $row = $db->update($sql);

    if(!$row){
        $sql_failure_log ="INSERT INTO `transfer_log`(`log_account`, `log_transid`, `log_type`, `log_amount`, `log_result`, `log_time`, `log_ip`)
                            VALUES ('$username', '$transid', '$type', '$amount', '0',NOW(),'$ip')";

        $row_failure_log = $db->insert($sql_failure_log);

        $arr_result['result'] = false;
        $arr_result['data'] = 'UPDATE failure';
        echo json_encode($arr_result);
        exit;
    }

    #新增紀錄
    $sql_INSERT_log ="INSERT INTO `transfer_log`(`log_account`, `log_transid`, `log_type`, `log_amount`, `log_result`, `log_time`, `log_ip`)
                    VALUES ('$username', '$transid', '$type', '$amount', '1',NOW(),'$ip')";
    // echo $sql;
    // exit;
    $row_INSERT_log = $db->insert($sql_INSERT_log);

    if(!$row){
        $arr_result['result'] = false;
        $arr_result['data'] = 'INSERT fail';
        echo json_encode($arr_result);
        exit;
    }
    $arr_result['result'] = true;
    $arr_result['data'] = 'Transfer Successful';
    echo json_encode($arr_result);
    exit;
}

if($type == 'OUT'){
    #檢查序號是否重複
    // $sql_check = "SELECT COUNT(*) as c  FROM `transfer_log` WHERE `log_transid` = '$transid' AND `log_account` = '$username' AND `log_result` = '1' AND `log_type` = 'OUT' ";
    $sql_check = "SELECT COUNT(*) as c  FROM `transfer_log` WHERE `log_transid` = '$transid' AND `log_account` = '$username' AND `log_result` = '1' ";
    $row_check = $db->select($sql_check);

    if($row_check['0']['c'] > 0){
        $arr_result['result'] = false;
        $arr_result['data'] = 'transid repeated';
        echo json_encode($arr_result);
        exit;
    }

    try {
         $db->getConnection()->beginTransaction();
        // echo $row['0']['Balance'];
        // exit;

        if($row['0']['Balance'] < $amount){
            throw new Exception("Insufficient balance");
        }

        #更新實際金額
        $sql = "UPDATE `account` SET `account_money` = `account_money` - $amount WHERE `account_name` = '$username' ";

        $row = $db->update($sql);

        if(!$row){
            $sql_failure_log ="INSERT INTO `transfer_log`(`log_account`, `log_transid`, `log_type`, `log_amount`, `log_result`, `log_time`, `log_ip`)
                                VALUES ('$username', '$transid', '$type', '$amount', '0',NOW(),'$ip')";

            $row_failure_log = $db->insert($sql_failure_log);

            throw new Exception("Sever Error!");
        }

        #新增紀錄
        $sql_INSERT_log ="INSERT INTO `transfer_log`(`log_account`, `log_transid`, `log_type`, `log_amount`, `log_result`, `log_time`, `log_ip`)
                        VALUES ('$username', '$transid', '$type', '$amount', '1',NOW(),'$ip')";

        $row_INSERT_log = $db->insert($sql_INSERT_log);

        if(!$row){
            throw new Exception("Sever Error!");
        }

        $db->getConnection()->commit();
    }
    catch  (Exception $err) {
        $db->getConnection()->rollback();

        $arr_result["result"] = false;
        $arr_result["data"] = $err->getMessage();
        echo json_encode($arr_result);
        exit;
    }
    $arr_result['result'] = true;
    $arr_result['data'] = 'Transfer Successful';
    echo json_encode($arr_result);
    exit;
}
