<?php
    function openBar(){
        $servername = "192.168.80.80"; 	// 서버 이름
        $username = "WBEarly"; 			// 사용자 이름
        $password = "#woobosys@early!"; // 비밀번호
        $dbname = "warning"; 			// 데이터베이스 이름
        $port = 3306;

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // 연결 확인
    if ($conn->connect_error) {
        die("연결 실패: " . $conn->connect_error);
    }

    // 버튼 클릭 시 쿼리 실행
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // MySQLi 연결
        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        
        // 버튼 클릭 시 데이터 삽입
        //if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $sql = "INSERT INTO wb_gatecontrol (GCtrCode, CD_DIST_OBSV, RegDate, Gate, GStatus) VALUES (NULL, '16', now(), 'open', 'start' )";

            // 준비된 문
            $stmt = $conn->prepare($sql);
            //$stmt->bind_param("sisss",$gCtrcode,$cdDistObsv, $regDate, $gate, $gStatus);

            //echo "<script>console.log('콘솔 남기기 : $stmt')</script>";

            // 실행 및 결과 체크
            if ($stmt->execute()) {
                echo "데이터 삽입 성공!";
            } else {
                echo "실행 결과 : 오류: " . $stmt->error;
            }

            // 문 및 연결 종료
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<?php
    function closeBar(){
        $servername = "192.168.80.80"; 	// 서버 이름
        $username = "WBEarly"; 			// 사용자 이름
        $password = "#woobosys@early!"; // 비밀번호
        $dbname = "warning"; 			// 데이터베이스 이름
        $port = 3306;

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // 연결 확인
    if ($conn->connect_error) {
        die("연결 실패: " . $conn->connect_error);
    }

    // 버튼 클릭 시 쿼리 실행
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // MySQLi 연결
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        $sql = "INSERT INTO wb_gatecontrol (GCtrCode, CD_DIST_OBSV, RegDate, Gate, GStatus) VALUES (NULL, '16', now(), 'close', 'start' )";

        // 준비된 문
        $stmt = $conn->prepare($sql);
        //$stmt->bind_param("sisss",$gCtrcode,$cdDistObsv, $regDate, $gate, $gStatus);

        //echo "<script>console.log('콘솔 남기기 : $stmt')</script>";

        // 실행 및 결과 체크
        if ($stmt->execute()) {
            echo "데이터 삽입 성공!";
        } else {
            echo "실행 결과 : 오류: " . $stmt->error;
        }

        // 문 및 연결 종료
        $stmt->close();
    }

    $conn->close();
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>열림 버튼</title>
</head>
<body>
    <!-- Henry Lee 2024.09.25-->
    <div>
        <iframe width = "545px" height=" 360px" src="http://192.168.83.213" title="CCTV 어플리케이션"></iframe>
    </div>

    <input type="button" name="openBarBtn" id="openBarBtn" value="열림"  onclick="<?php echo openBar(); ?>" /><br/>
    <input type="button" name="closeBarBtn" id="closeBarBtn" value="닫힘"  onclick="<?php echo closeBar(); ?>" /><br/>

</body>
</html>