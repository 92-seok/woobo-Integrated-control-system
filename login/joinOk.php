<?php
$data = json_decode(file_get_contents('php://input'), true);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_USER_DAO();
$vo = new WB_USER_VO();

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
$_SESSION['ip'] = $ip;

$vo = $dao->SELECT_SINGLE("uId = '{$data['id']}'");
if ($vo->{key($vo)}) {
    $res['code'] = '400';
} else {
    $vo->uId = urldecode(base64_decode($data['id']));
    $vo->uPwd = strtoupper(hash('sha512', base64_decode($data['pwd'])));
    $vo->uPhone = str_replace('-', '', base64_decode($data['uphone']));
    $vo->uName = urldecode(base64_decode($data['uname']));
    $vo->RegDate = date('Y-m-d H:i:s');
    $vo->ipUse = 'N';

    $dao->INSERT($vo);
    $res['code'] = '200';
}

echo json_encode($res);
?>
