<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_LOG_DAO();
$vo = new WB_LOG_VO();

//mType:mType, url:lastUrl[1], ip:ip, action:action, equip:equip, befor:before, after:after, content:content
$vo->RegDate = date('Y-m-d H:i:s');
$vo->ip = $_POST['ip'];
$vo->userID = $_POST['uid'];
$vo->pType = $_POST['mType'];
$vo->Page = $_POST['url'];
$vo->EventType = $_POST['action'];
$vo->equip = $_POST['equip'];
$vo->EventBefore = $_POST['before'];
$vo->EventAfter = $_POST['after'];
$vo->EventContent = $_POST['content'];

$dao->INSERT($vo);
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/files/eventlog/log_' . date('ymd', strtotime('Now')) . '.txt', 'a');
fwrite($fp, date('H:i:s') . "\tIP(ID): {$vo->ip}({$vo->userID})\tPage: {$vo->pType}/{$vo->Page}\taction: { $vo->EventType}\tequip: {$vo->equip}\tbefore: {$vo->EventBefore}\tafter: {$vo->EventAfter}\tcontent: { $vo->EventContent}\r\n");
fclose($fp);
?>
