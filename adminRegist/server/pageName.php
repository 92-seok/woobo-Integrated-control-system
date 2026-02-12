<?php
function pageName($page)
{
    switch ($page) {
        //Main
        case 'main.php':
            $r = '상황화면';
            break;
        //admin
        case 'AddUser.php':
            $r = '계정목록(추가)';
            break;
        case 'logDetail.php':
            $r = '로그목록(자세히)';
            break;
        case 'logList.php':
            $r = '로그목록';
            break;
        case 'manageUser.php':
            $r = '계정목록';
            break;
        //alert
        case 'alertForm.php':
            $r = '경보그룹목록(자세히)';
            break;
        case 'alertList.php':
            $r = '경보그룹목록';
            break;
        case 'controlDetail.php':
            $r = '경보발령내역(자세히)';
            break;
        case 'controlIssue.php':
            $r = '경보수동제어';
            break;
        case 'controllList.php':
            $r = '경보발령내역';
            break;
        case 'criForm.php':
            $r = '임계값설정(자세히)';
            break;
        case 'criList.php':
            $r = '임계값설정';
            break;
        case 'issueMent.php':
            $r = '경보멘트관리';
            break;
        case 'setAlertEachScen.php':
            $r = '경보전광판관리';
            break;
        //broad
        case 'broadForm.php':
            $r = '방송하기';
            break;
        case 'broadReport.php':
            $r = '결과통계';
            break;
        case 'broadResult.php':
            $r = '방송내역';
            break;
        case 'broadResultDetail.php':
            $r = '방송내역(자세히)';
            break;
        case 'criList.php':
            $r = 'CID관리';
            break;
        case 'group.php':
            $r = '그룹관리';
            break;
        case 'mentForm.php':
            $r = '멘트관리(자세히)';
            break;
        case 'mentList.php':
            $r = '멘트관리';
            break;
        //data
        case 'Daygraph.php':
            $r = '일별그래프';
            break;
        case 'Monthgraph.php':
            $r = '월별그래프';
            break;
        case 'Timegraph.php':
            $r = '시간별그래프';
            break;
        case 'Yeargraph.php':
            $r = '연별그래프';
            break;
        case 'Day.php':
            $r = '일별데이터';
            break;
        case 'Month.php':
            $r = '월별데이터';
            break;
        case 'Period.php':
            $r = '기간별데이터';
            break;
        case 'Time.php':
            $r = '시간별데이터';
            break;
        case 'Year.php':
            $r = '연별데이터';
            break;
        //display
        case 'eachEquList.php':
            $r = '전광판목록';
            break;
        case 'eachScenForm.php':
            $r = '시나리오관리';
            break;
        case 'sendEachScen.php':
            $r = '시나리오관리(자세히)';
            break;
        //equip
        case 'brdequip.php':
            $r = '방송장비';
            break;
        case 'disequip.php':
            $r = '전광판';
            break;
        case 'disequipDetail.php':
            $r = '전광판(자세히)';
            break;
        case 'equip.php':
            $r = '총 장비';
            break;
        case 'equipChange.php':
            $r = '총 장비(자세히)';
            break;
        //gate
        case 'gateList.php':
            $r = '차단기 제어 내역';
            break;
        case 'InOutCareDay.php':
            $r = '일별데이터';
            break;
        case 'InOutCareMonth.php':
            $r = '월별데이터';
            break;
        case 'InOutCareYear.php':
            $r = '연별데이터';
            break;
        case 'parkingCar.php':
            $r = '주차장 그룹 관리';
            break;
        case 'parkingCare.php':
            $r = '차량 입/출내역';
            break;
        case 'parkingCareAdd.php':
            $r = '주차장 그룹 관리(자세히)';
            break;
        case 'passiveGate.php':
            $r = '차단기 수동제어';
            break;
        //login
        case 'login.php':
            $r = '로그인';
            break;
        //report
        case 'reportFrame.php':
            $r = '보고서';
            break;
        //sms
        case 'addrControl.php':
            $r = '연락처관리';
            break;
        case 'addrDetail.php':
            $r = '연락처관리(자세히)';
            break;
        case 'sendDetail.php':
            $r = '발송내역(자세히)';
            break;
        case 'sendList.php':
            $r = '발송내역';
            break;
        case 'sendMsg.php':
            $r = '문자발송';
            break;
        default:
            $r = '알 수 없음';
    }
    return $r;
}
?>
