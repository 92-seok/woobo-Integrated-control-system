<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$type = $_POST['type'];
$num = $_POST['num'];
$title = $_POST['title'];
$content = $_POST['content'];

if ($type == 'insert') {
    $sql =
        "insert into wb_brdment ( Title, Content, BUse )
			values ('" .
        $title .
        "','" .
        $content .
        "', 'ON' )";

    $res = mysqli_query($conn, $sql);
} elseif ($type == 'update') {
    $sql =
        "update wb_brdment
			set Title = '" .
        $title .
        "',
				Content = '" .
        $content .
        "'
			where AltCode = '" .
        $num .
        "'";
    $res = mysqli_query($conn, $sql);
} elseif ($type == 'delete') {
    $sql =
        "update wb_brdment 
			set BUse = 'OFF'
			where AltCode = '" .
        $num .
        "'";
    $res = mysqli_query($conn, $sql);
} elseif ($type == 'mdelete') {
    $where = '';
    $explode = explode(',', $num);
    for ($i = 0; $i < count($explode); $i++) {
        if ($i == 0) {
            $where = " AltCode = '" . $explode[$i] . "' ";
        } else {
            $where = $where . " or AltCode = '" . $explode[$i] . "' ";
        }
    }
    $sql =
        "update wb_brdment 
			set BUse = 'OFF'
			where " . $where;
    $res = mysqli_query($conn, $sql);
}
?>
