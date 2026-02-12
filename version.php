<?php

$version = '[v1.0(23/06/20)]';

/*******************************************************************************************************************

	version	: v1.0.0
	date	: 2023-06-20
	author	: KJH

	content	: 
		전광판 페이징 오류 수정
		임계값 설정 유효성 검사 수정
		방송 점검시 parm1, parm2, parm3, parm4 기본값 설정
		sms 기타 버그 수정
			
		※ 변경 File
		  - equipsetting.php
		  - equipgroupsetting.php
		  - popup.js
		  - mentList.php
		  - sendEachScen.php
		  - sms.js
		  - alert.js

******************************************************************************************************************

	version	: v1.0
	date	: 2023-03-24
	author	: JHJ

	content	:
		MAP 수정
			- 스카이뷰 버튼 추가

		POP3 문제 수정
			- 폐쇄망의 경우 PHPMailer가 NAVER로 설정되어 있어 메일 발송이 힘들기에 IDC서버로 메일발송하도록 로직수정
			
		※ 변경 File
			- map.js
			- mailer.php
			- sendMail.php

******************************************************************************************************************

	version	: v1.0
	date	: 2023-01-12
	author	: JHJ

	content	:
		GS인증 관련 수정
			- AI에서 지능형으로 문구 교체
			
		※ 변경 File
			- index.php
			- loginOK.php
			- popup.php
			- top.php

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-29
	author	: JHJ

	content	:
		GS인증 관련 수정
			- 임계치 경보 그룹 이름 입력 필드 특수문자 ';'를 제외한 20자내 허용으로 수정
			- 전광판 시작/완료시간 입력 필드 특수문자 '-' 두개를 포함한 날짜만 입력되게 수정
			- SMS 검색 입력 필드 특수문자 ';'는 허용되지 않게 수정
			- AI 점검현황 확인 위해 일시적 20초 간격으로 줄였던것 60초로 수정
			
		※ 변경 File
			- alert.js
			- display.js
			- sms.js
			- popup.js

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-27
	author	: JHJ

	content	:
		GS인증 관련 수정
			- 회원가입, 계정추가 할 때 10자 이하로 입력되게 수정
			- 회원가입, 계정추가 할 때 아이디에 특수문자 미허용

		버그 수정
			- URL Encoding 하여 넘어간 데이터가 Decode되지 않고 저장되던 문제 수정
			
		※ 변경 File
			- login.php
			- loginOK.php
			- executeUser.php

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-26
	author	: JHJ

	content	:
		GS인증 관련 수정
			- 로그인 10회 연속 실패 후 10분간 잠겼을때 잔여 시간 없이 10분 후 로그인이 가능하다는 알람만 뜨게 수정

		버그 수정
			- URL Encoding 하여 넘어간 데이터가 Decode되지 않고 저장되던 문제 수정
			
		※ 변경 File
			- login.php
			- loginOK.php
			- executeUser.php

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-21
	author	: JHJ

	content	:
		GS인증 관련 수정
			- 1분 간격 자동 점검 (%s) 수동 변경 시 (수동) 뜨던거 안뜨게 수정
			- 강우 임계 기준 중 '기준 시간'이 햇갈린다 하여 '누적 기준 시간'으로 변경
			- AI 점검현황 확인을 위해 일시적으로 20초간격 점검으로 수정

		버그 수정
			- POP UP 강우, 수위만 새로고침 버튼 되던거 수정
			- POP UP 창 뜨고 30초 후 새로고침 되던 문제 창 끄면 변경되게 수정
			- POP UP 자동 A/S 안되던거 수정
			- 침수센서 데이터 표출 부분 NULL 값 오류 표시 수정
			- 경보그룹 페이지에 임계 상황 발생 후 수동승인일때 & 경보 발령중일때 경보발령 & 경보종료 버튼을 누르면 안에 접속되던 오류 수정
			- 차단기 페이지 PDO 적용

		※ 변경 File
			- dataPopup.php
			- Period.php
			- alert.js
			- popup.js
			- criForm.php
			- serverGate.php
			

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-20
	author	: JHJ

	content	: 
		POP UP 관련 AI System 문제
			- LastDate가 2시간은 안넘었지만 LastStatus가 오류로 잡히는 항목 AS접수 멘트에서 점검요망으로 수정
			- 점검 항목 중 차단기 구분기호를 21번으로 구분하던 문제, 마지막 값을 읽고 Open & Close 하던 프로토콜에서 check로 변경
			- popup.js 코드 안정화

		침수 데이터 단위 변경
			- 침수 수위도 수위로 M단위로 체크 했으나, 침수 특성상 21Cm를 넘지 못하여 0.2M까지밖에 값이 안들어오기에 Cm로 단위 변경

		경보 수동제어 삭제
			- 시나리오상 문제가 많아서 페이지 삭제 (차후 시간 될때 변경 할 예정이나 현재는 이대로 진행)

		임계치 설정 부분
			- 0도 데이터라 가정하여 나뒀었지만, 0 입력시 임계치 무한 반복으로 인해 0값 제한
			- 1단계부터 4단계까지 더욱 큰 값을 넣어야 하게 수정

		버그 수정
			- 방송멘트 숫자 안써지던 문제 정규식 수정
			- 계정 삭제 안되던 문제 수정
			- 전광판 목록, 경보그룹 임계치 전체 선택 CheckBox 작동 안하던 문제 수정
			- 차단기 Open & Close 작동 시 점등이 재대로 안되던 문제 해결
			- 차단기 제어 내역 검색 누르면 재대로 결과가 안뜨던 문제 해결
			- 경보 수동 발령시 m-start에서 start로 들어가게 변경

		※ 변경 File
			- equipgroupsetting.php
			- equipPopup.php
			- equipsettingcheck.php
			- realtimealert.php
			- sendMail.php
			- dbvo.php
			- top_sub.php
			- alertForm.php
			- saveIssue.php
			- excuteUser.php
			- Day.php
			- Time.php
			- Period.php
			- sendEachScen.php
			- serverGate.php
			- alert.js
			- broad.js
			- gate.js
			- userAuth.js
			- popup.js

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-14
	author	: JHJ

	content	: 
		로그인, 가입시 Client - Server 통신간 암호화
			- 계정에 관련된 통신시 Base64 Encode하여 통신으로 수정

		※ 변경 File
			- login.php
			- loginOK.php
			- join.php
			- join.php
			- userAuth.js
			- executeUser.php

******************************************************************************************************************

	version	: v1.0
	date	: 2022-12-13
	author	: JHJ

	content	: 
		로그인 실패 10회 처리 정책 변경
			- 로그인 실패한 IP에 대한 10분 시도 정지에서 로그인 실패한 ID에 대한 10분 시도 정지로 변경

		비밀번호 인증 규칙 변경
			- 영문+숫자 ( #$^&()_+|<>?:;{} 를 제외한 특수문자 가능 ) 10자리 이상 20자리 이하 에서
			  영문+숫자+특수문자 (특수문자 전체 가능) 8자리 이상으로 규칙 변경

		버그 수정
			- 임계치 관리 : 경보 그룹 삭제, 임계치 추가/삭제 버그 수정

		※ 변경 File
			- loginOK.php
			- join.php
			- userAuth.js
			- alertSave.php
			- criSave.php

******************************************************************************************************************

	version	: v1.0
	date	: 2022-11-22
	author	: JHJ

	content	: 
		버그 수정
			- 강우 경보 추가/변경/삭제 시 RainTime을 고려하지 않아 전체 삭제 되던 버그 수정

		※ 변경 File
			- criSave.php
			- alert.js

******************************************************************************************************************

	version	: v1.0
	date	: 2022-11-14
	author	: JHJ

	content	: 
		세션 만료 디테일한 부분 설정
			- session_start()로 세션 연장 및 Session 만료시 logout 로직 버그 수정
			- 각 Frame 상단에 있던 Session Time Out 로직을 include/sessionUseTime.php 로 따로 빼고 include_once로 포함

		기타 버그 수정
			- 경보멘트관리 1~0 숫자를 특수문자로 인식하던 부분 수정

		※ 변경 File
			- 모든 Frame 페이지
			- alert.js

******************************************************************************************************************
	
	version	: v1.0
	date	: 2022-11-11
	author	: JHJ

	content	: 
		세션 만료 디테일한 부분 설정
			- session_start()로 세션 연장 및 Session 만료시 logout 로직 구현
			- SESSION 변수 설정을 login.php에서 index.php로 변경
			- SESSION의 lastSessionUseTime Index에 시간을 넣고 페이지 이동시 현재 시간과 비교하여 10분 이내면 (index.php에서 설정 가능) 세션 변수 시간 변경

		※ 변경 File
			- index.php
			- 모든 Frame 페이지 상단 session 유지 부분 logic 변경

******************************************************************************************************************

	version	: v1.0
	date	: 2022-11-10
	author	: JHJ

	content	: 
		로그인 - 가입
			- IP 비교하여 로그인 기능 추가로 계정관리 중 IP 변경 기능 추가 ( '*'을 이용한 대역대 설정 가능하게 구현 )
			- IP 비교하여 로그인 기능을 켰지만 IP값이 없으면 최초 로그인 시 IP 추가 할 수 있게 변경
			- DB wb_user에 IPUse 컬럼 추가
			- 로그인 창에서 가입 할 수 있게 회원가입 페이지 추가
			( 회원가입 할 당시 Auth, IP, IPUse는 NULL로 처리하여 관리자가 승인 해야 가입 할 수 있게 변경 )
			- 계정관리 목록에 승인 해야 할 계정 있는지 확인 후 승인 처리 할 수 있게 버튼 구현

		데이터 - 일별
			- Warning Error 수정
			( 시간, 월, 연별 데이터는 Array에 담아서 사용하기에 아직 안들어온 컬럼값을 !== "" 로 구분할 수 있으나, 일별은 컬럼값을 바로 사용하기에 !== null 로 변경 )


		SMS - 발송내역 (자세히)
			- 삭제된 수신인 소정 변경

		임계치관리 - 경보수동제어
			- 상세정보에 값이 없으면 Fatal Error 수정

		※ 변경 File
			- login.php
			- loginOK.php
			- join.php
			- joinOK.php
			- Day.php
			- sendDetail.php
			- manageUser.php
			- AddUser.php
			- executeUser.php
			- userAuth.js

******************************************************************************************************************

	version	: v1.0
	date	: 2022-11-08
	author	: JHJ

	content	: 
		로그인 - 가입
			- IP 비교하여 로그인 기능 추가
			- 가입 당시 IP를 DB에 입력 및 비교
			- 위 기능으로 인해 로그인 화면에 가입 기능 및 화면 추가
			( 추 후 IP Column이 비어있다면 접속하는 IP 입력 기능 필요 )

		세션 관리
			- 로그인 창에서 10분 (세션 만료 시간) 지난 후 로그인 하면 세션 만료되었다고 종료시키던 버그 수정

		Pop Up - Radar Content
			- DBDAO.php 수정으로 Pop Up 부분도 수정

		JS
			- 작은 오류 수정
					
		※ 변경 File
			login.php
			loginOK.php
			dbdao.php
			radarPopup.php
			popup.js
			broad.js

	coment	: IP 비교하여 로그인 기능은 추 후 제거될 기능으로 따로 버전관리 필요

******************************************************************************************************************

	version	: v1.0
	date	: 2022-11-01
	author	: JHJ

	content	: 
		임계치관리 - 경보전광판 관리
			- 처리방식 변경
				기존 : wb_isument 테이블에 이미지 경로 ',' 구분자로 입력
				변경
					- wb_display 테이블에 레코드 추가 후 DisCode를 wb_isument 테이블에 해당 DisMent1~4에 ',' 구분자로 입력
					- PDO사용한 방식으로 코드 변경
					
			- 변경 File
				setAlertEachScen.php
				saveWarnEachScen.php
				dbdao.php
				alert.js

******************************************************************************************************************

	version	: v1.0
	date	: 2022-10-25
	author	: JHJ

	content	: 
		기능 구현
			1.	Main
			2.	Data
			3.	Borad
			4.	Display
			5.	Gate
			6.	SMS
			7.	Alert
			8.	Report
			9.	Admin
			10.	Include
				1) Main Menu
				2) Top Menu
				3) Pop UP (AI 어시스턴트)
				4) DB
				5) Java Script
			11. etc
				1) Image
				2) Font
				3) Files
				4) CSS
				5) Java Script

*******************************************************************************************************************/

?>
