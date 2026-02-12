<?php
$request_word = $_GET['REQUEST_WORD'];
$organ_code = $_GET['ORGAN_CODE'];
$error = true;

if ($request_word == 'EXP ORGAN INFO') {
    if ($organ_code != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CODE PART') {
    $master_code = $_GET['MASTER_CODE'];

    if ($organ_code != '' && $master_code != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'MASTER CODE' => $master_code,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP ORGAN ENV') {
    if ($organ_code != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP USER INFO') {
    $user_id = $_GET['USER_ID'];

    if ($organ_code != '' && $user_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP TERM LIST') {
    $user_id = $_GET['USER_ID'];
    $term_type = $_GET['TERM_TYPE'];

    if ($organ_code != '' && $user_id != '' && $term_type != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'TERM TYPE' => $term_type,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP SGA TERM LIST') {
    $user_id = $_GET['USER_ID'];
    $term_type = $_GET['TERM_TYPE'];

    if ($organ_code != '' && $user_id != '' && $term_type != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'TERM TYPE' => $term_type,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP USER TERM LIST') {
    $user_id = $_GET['USER_ID'];

    if ($organ_code != '' && $user_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP USER GROUP LIST') {
    $user_id = $_GET['USER_ID'];

    if ($organ_code != '' && $user_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP GROUP TERM LIST') {
    $user_id = $_GET['USER_ID'];
    $group_id = $_GET['GROUP_ID']; //?

    if ($organ_code != '' && $user_id != '' && $group_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'GROUP ID' => $group_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP GROUP LIST TERM') {
    $user_id = $_GET['USER_ID'];
    //$group_id_list = $_GET['GROUP_ID_LIST'];
    $group_id = $_GET['GROUP_ID'];

    if ($organ_code != '' && $user_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'GROUP ID LIST' => [
                'GROUP ID' => $group_id,
            ],
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP REQST TERM TTS CAST') {
    $user_id = $_GET['USER_ID'];
    $cast_title = $_GET['CAST_TITLE'];
    $cast_body = $_GET['CAST_BODY'];
    $pre_silence = $_GET['PRE_SILENCE'];
    $pst_silence = $_GET['PST_SILENCE'];
    $read_speed = $_GET['READ_SPEED'];
    $repetitions = $_GET['REPETITIONS'];
    $cast_volume = $_GET['CAST_VOLUME'];
    $chime_yn = $_GET['CHIME_YN'];
    // $dest_list = $_GET['DEST_LIST'];
    $term_code = $_GET['TERM_CODE'];
    $term_name = $_GET['TERM_NAME'];
    $term_cdma = $_GET['TERM_CDMA'];

    if ($organ_code != '' && $user_id != '' && $cast_title != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST TITLE' => $cast_title,
            'CAST BODY' => $cast_body,
            'PRE SILENCE' => $pre_silence,
            'PST SILENCE' => $pst_silence,
            'READ SPEED' => $read_speed,
            'REPETITIONS' => $repetitions,
            'CAST VOLUME' => $cast_volume,
            'CHIME YN' => $chime_yn,
            'DEST LIST' => [
                'TERM CODE' => $term_code,
                'TERM NAME' => $term_name,
                'TERM CDMA' => $term_cdma,
            ],
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP REQST TERM MEDIA CAST') {
    $user_id = $_GET['USER_ID'];
    $cast_title = $_GET['CAST_TITLE'];
    $cast_body = $_GET['CAST_BODY'];
    $cast_volume = $_GET['CAST_VOLUME'];
    $server_file_name = $_GET['SERVER_FILE_NAME'];
    //$dest_list = $_GET['DEST_LIST'];
    $term_code = $_GET['TERM CODE'];
    $term_name = $_GET['TERM NAME'];
    $term_cdma = $_GET['TERM CDMA'];

    if ($organ_code != '' && $user_id != '' && $cast_title != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST TITLE' => $cast_title,
            'CAST BODY' => $cast_body,
            'CAST VOLUME' => $cast_volume,
            'SERVER FILE NAME' => $server_file_name,
            'DEST LIST' => [
                'TERM CODE' => $term_code,
                'TERM NAME' => $term_name,
                'TERM CDMA' => $term_cdma,
            ],
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CHECK TERM REQST') {
    $user_id = $_GET['USER_ID'];
    $cast_title = $_GET['CAST_TITLE'];
    //$dest_list = $_GET['DEST_LIST'];
    $term_code = $_GET['TERM_CODE'];
    $term_name = $_GET['TERM_NAME'];
    $term_cdma = $_GET['TERM_CDMA'];

    if ($organ_code != '' && $user_id != '' && $cast_title != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST TITLE' => $cast_title,
            'DEST LIST' => [
                'TERM CODE' => $term_code,
                'TERM NAME' => $term_name,
                'TERM CDMA' => $term_cdma,
            ],
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CAST ID RESULT') {
    $user_id = $_GET['USER_ID'];
    $cast_id = $_GET['CAST_ID'];

    if ($organ_code != '' && $user_id != '' && $cast_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST ID' => $cast_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CAST ID DETAIL') {
    $user_id = $_GET['USER_ID'];
    $cast_id = $_GET['CAST_ID'];

    if ($organ_code != '' && $user_id != '' && $cast_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST ID' => $cast_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CAST ID DETAIL') {
    $user_id = $_GET['USER_ID'];
    $cast_id = $_GET['CAST_ID'];

    if ($organ_code != '' && $user_id != '' && $cast_id != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'CAST ID' => $cast_id,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
        $error = false;
    }
} elseif ($request_word == 'EXP CAST RESULT') {
    $user_id = $_GET['USER_ID'];
    $from_date = $_GET['FROM_DATE'];
    $to_date = $_GET['TO_DATE'];

    if ($organ_code != '' && $user_id != '' && $from_date != '' && $to_date != '') {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'ORGAN CODE' => $organ_code,
            'USER ID' => $user_id,
            'FROM DATE' => $from_date,
            'TO DATE' => $to_date,
        ];
        $error = true;
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '02',
        ];
    }
} else {
    $code_array = [
        'REQUEST WORD' => $request_word,
        'code' => '01',
    ];
    $error = false;
}

/*if($error == true) {
		$url = '121.180.130.35:36380';
		$data = $code_array;
		
		$ch = curl_init(); //로딩
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$result = curl_exec($ch);
		curl_close($ch);
		//print_r($result);
		echo "d";
	} else {
		echo json_encode($code_array);	
	}*/

if ($error == true) {
    $data = $code_array;
    $tuCurl = curl_init(); // 세션 초기화
    curl_setopt($tuCurl, CURLOPT_URL, 'http://121.180.130.35:36302'); // 옵션 세팅
    //curl_setopt($tuCurl, CURLOPT_PORT , 443);
    curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
    curl_setopt($tuCurl, CURLOPT_HEADER, 0);
    curl_setopt($tuCurl, CURLOPT_POST, 1);
    curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT, 10); // 접속대기시간
    curl_setopt($tuCurl, CURLOPT_TIMEOUT, 60); // 프로세스 MAX 시간
    curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($tuCurl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $tuData = curl_exec($tuCurl); // curl 실행
    if (!curl_errno($tuCurl)) {
        // 에러번호 가져옴
        $info = curl_getinfo($tuCurl); // 상태정보 리턴
        echo json_encode($info);
    } else {
        $code_array = [
            'REQUEST WORD' => $request_word,
            'code' => '03',
        ];
        echo json_encode($code_array);
    }
} else {
    echo json_encode($code_array);
}
?>
