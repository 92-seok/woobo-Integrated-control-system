<?php
session_start();
if (isset($_SESSION['lastSessionUseTime'])) {
    if ($_SESSION['sessionUseTime'] > 0) {
        if (time() - $_SESSION['lastSessionUseTime'] >= $_SESSION['sessionUseTime']) {
            echo '<script>';
            echo "alert('세션이 만료되었습니다.');";
            echo "window.location.replace('/index.php');";
            echo '</script>';
        } else {
            $_SESSION['lastSessionUseTime'] = time();
        }
    }
} else {
    echo '<script>';
    echo "alert('세션이 만료되었습니다.');";
    echo "window.location.replace('/index.php');";
    echo '</script>';
}
