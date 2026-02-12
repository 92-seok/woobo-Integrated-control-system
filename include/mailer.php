<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/mail/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/mail/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/mail/src/SMTP.php';
/*
 * AUTHOR : YOUNGMINJUN
 *
 * $EMAIL : 보내는 사람 메일 주소
 * $NAME : 보내는 사람 이름
 * $SUBJECT : 메일 제목
 * $CONTENT : 메일 내용
 * $MAILTO : 받는 사람 메일 주소
 * $MAILTONAME : 받는 사람 이름
 */

use PHPMailer\PHPMailer\PHPMailer;
function sendMail($MAILTO, $SUBJECT, $CONTENT, $NAME)
{
    $mail = new PHPMailer();
    $body = $CONTENT;

    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
    // 1 = errors and messages
    // 2 = messages only
    $mail->CharSet = 'utf-8';
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->SMTPSecure = 'ssl'; // sets the prefix to the servier
    $mail->Host = 'smtp.naver.com'; // sets NAVER as the SMTP server
    $mail->Port = 465; // set the SMTP port for the NAVER server
    $mail->Username = 'djd0953@naver.com'; // NAVER username
    $mail->Password = 'woobosys!'; // NAVER password

    $mail->SetFrom($mail->Username, $NAME);

    $mail->AddReplyTo($mail->Username, $NAME);

    $mail->Subject = $SUBJECT;

    $mail->MsgHTML($body);

    $address = $MAILTO;
    $mail->AddAddress($address, 'woobosys');

    if (!$mail->Send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
    }
}

function sendMailWithIDCServer($MAILTO, $SUBJECT, $CONTENT, $NAME, $connectType = false)
{
    $host = '211.34.105.29';

    if ($connectType) {
        $port = 80;
        $waitTimeoutInSeconds = 3;

        $fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds);
        if (!$fp) {
            $connectType = false;
        }
        fclose($fp);
    }

    if ($connectType) {
        $url = "{$host}:{$port}/sendMail/sendMail.php";
        $body = [
            'to' => $MAILTO,
            'subject' => $SUBJECT,
            'content' => $CONTENT,
            'name' => $NAME,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        try {
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $resArr = explode("{\"code\"", $response);
            $res = json_decode("{\"code\"{$resArr[1]}");
            if ($res->code == 400) {
                throw new Exception($res->message);
            }

            $rtv = ['code' => 200, 'msg' => $res['sendCount']];
        } catch (Exception $ex) {
            $rtv = ['code' => 400, 'msg' => $ex->getMessage()];
        }

        curl_close($ch);
    } else {
        $port = 4097;
        $user = 'userWooboWeb';
        $pass = 'wooboWeb!@';
        $database = 'weathersi';
        $charset = 'utf-8';

        $insertData = [
            'mailto' => $MAILTO,
            'subject' => $SUBJECT,
            'content' => $CONTENT,
            'name' => $NAME,
        ];

        $conn = new PDO("mysql:host={$host}:{$port};dbname={$database};charset={$charset}", "{$user}", "{$pass}");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO sendMail (mailto, subject, content, name, mStatus, RegDate) Values (:mailto, :subject, :content, :name, 'start', NOW())";
        $stmt = $conn->prepare($sql);
        $rtv = $stmt->execute($insertData);
    }

    return $rtv;
}
?>
