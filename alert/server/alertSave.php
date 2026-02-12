<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
$data = json_decode(file_get_contents('php://input'), true);

$dao = new WB_ISUALERTGROUP_DAO();
$vo = new WB_ISUALERTGROUP_VO();

$rDao = new DAO_T();

$type = $data['type'];

if ($type == 'del') {
    $vo->GCode = $data['num'];
    $vo = $dao->SELECT_SINGLE("GCode = '{$vo->GCode}'");

    $result['action'] = 'Alert Group Delete';
    $result['name'] = $vo->GName;
    $result['after'] = '';
    $result['before'] = "경보명 : {$vo->GName}<br/>";

    $sql = "SELECT NM_DIST_OBSV FROM wb_isualert AS a JOIN wb_equip AS b ON a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE AltCode IN ({$vo->AltCode})";
    $row = $rDao->SELECT_QUERY($sql);
    $i = 1;
    foreach ($row as $r) {
        $result['before'] .= "임계장비{$i} : {$r['NM_DIST_OBSV']}<br/>";
        $i++;
    }

    for ($i = 1; $i <= 4; $i++) {
        $auto = $vo->{"Auto{$i}"};
        $sms = $vo->{"SMS{$i}"};
        $equip = $vo->{"Equip{$i}"};

        $result['before'] .= '<hr/>';
        $result['before'] .= "{$i}단계({$auto})<br/>";

        $result['before'] .= '장비 : ';
        if ($equip != '') {
            $sql = "SELECT NM_DIST_OBSV FROM wb_equip WHERE CD_DIST_OBSV IN ({$equip})";
            $row = $rDao->SELECT_QUERY($sql);
            foreach ($row as $r) {
                $result['before'] .= "{$r['NM_DIST_OBSV']} ";
            }
        }
        $result['before'] .= '<br/>';

        $result['before'] .= 'SMS : ';
        if ($sms != '') {
            $sql = "SELECT UName FROM wb_smsuser WHERE GCode IN ({$sms})";
            $row = $rDao->SELECT_QUERY($sql);
            foreach ($row as $r) {
                $result['before'] .= "{$r['UName']} ";
            }
        }
        $result['before'] .= '<hr/>';
        $result['before'] .= '<br/>';
    }

    $dao->DELETE($vo);
} else {
    $vo->GCode = $data['num'];
    $vo->GName = $data['GName'];
    $vo->AltCode = $data['AltCode'];
    $vo->AdmSMS = $data['AdmSMS'];
    $vo->AltUse = $data['AltUse'];

    $systemType = $data['systemType'];
    $result = [];

    for ($i = 1; $i <= 4; $i++) {
        if (isset($data["Equip{$i}"])) {
            $vo->{"Equip{$i}"} = $data["Equip{$i}"];
        }
        if (isset($data["SMS{$i}"])) {
            $vo->{"SMS{$i}"} = $data["SMS{$i}"];
        }
        if (isset($data["Auto{$i}"])) {
            $vo->{"Auto{$i}"} = $data["Auto{$i}"];
        }
        if (isset($data["FloodSMS{$i}"])) {
            $vo->{"FloodSMSAuto{$i}"} = $data["FloodSMS{$i}"];
        }
        // if( $systemType == "flood" ) $vo->{"FloodSMSAuto{$i}"} = "on";
        // else $vo->{"FloodSMSAuto{$i}"} = "off";
    }

    if ($type == 'ins') {
        $vo->GCode = null;
        $dao->INSERT($vo);

        $result['action'] = 'Alert Group Insert';
        $result['name'] = $vo->GName;
        $result['before'] = '';
        $result['after'] = "경보명 : {$vo->GName}<br/>";

        $sql = "SELECT NM_DIST_OBSV FROM wb_isualert AS a JOIN wb_equip AS b ON a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE AltCode IN ({$vo->AltCode})";
        $row = $rDao->SELECT_QUERY($sql);
        $i = 1;
        foreach ($row as $r) {
            $result['after'] .= "임계장비{$i} : {$r['NM_DIST_OBSV']}<br/>";
            $i++;
        }

        for ($i = 1; $i <= 4; $i++) {
            $auto = $vo->{"Auto{$i}"};
            $sms = $vo->{"SMS{$i}"};
            $equip = $vo->{"Equip{$i}"};

            $result['after'] .= '<hr/>';
            $result['after'] .= "{$i}단계({$auto})<br/>";

            $result['after'] .= '장비 : ';
            if ($equip != '') {
                $sql = "SELECT NM_DIST_OBSV FROM wb_equip WHERE CD_DIST_OBSV IN ({$equip})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['after'] .= "{$r['NM_DIST_OBSV']} ";
                }
            }
            $result['after'] .= '<br/>';

            $result['after'] .= 'SMS : ';
            if ($sms != '') {
                $sql = "SELECT UName FROM wb_smsuser WHERE GCode IN ({$sms})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['after'] .= "{$r['UName']} ";
                }
            }
            $result['after'] .= '<hr/>';
            $result['after'] .= '<br/>';
        }
    } else {
        $bVo = new WB_ISUALERTGROUP_VO();
        $bVo = $dao->SELECT_SINGLE("GCode = {$vo->GCode}");

        $dao->UPDATE($vo);

        $result['action'] = 'Alert Group Update';
        $result['name'] = $vo->GName;

        $result['before'] = "경보명 : {$bVo->GName}<br/>";

        $bSql = "SELECT NM_DIST_OBSV FROM wb_isualert AS a JOIN wb_equip AS b ON a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE AltCode IN ({$bVo->AltCode})";
        $bRow = $rDao->SELECT_QUERY($bSql);
        $i = 1;
        foreach ($bRow as $r) {
            $result['before'] .= "임계장비{$i} : {$r['NM_DIST_OBSV']}<br/>";
            $i++;
        }

        for ($i = 1; $i <= 4; $i++) {
            $auto = $bVo->{"Auto{$i}"};
            $sms = $bVo->{"SMS{$i}"};
            $equip = $bVo->{"Equip{$i}"};

            $result['before'] .= '<hr/>';
            $result['before'] .= "{$i}단계({$auto})<br/>";

            $result['before'] .= '장비 : ';
            if ($equip != '') {
                $sql = "SELECT NM_DIST_OBSV FROM wb_equip WHERE CD_DIST_OBSV IN ({$equip})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['before'] .= "{$r['NM_DIST_OBSV']} ";
                }
            }
            $result['before'] .= '<br/>';

            $result['before'] .= 'SMS : ';
            if ($sms != '') {
                $sql = "SELECT UName FROM wb_smsuser WHERE GCode IN ({$sms})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['before'] .= "{$r['UName']} ";
                }
            }
            $result['before'] .= '<hr/>';
            $result['before'] .= '<br/>';
        }

        $result['after'] = "경보명 : {$vo->GName}<br/>";

        $sql = "SELECT NM_DIST_OBSV FROM wb_isualert AS a JOIN wb_equip AS b ON a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE AltCode IN ({$vo->AltCode})";
        $row = $rDao->SELECT_QUERY($sql);
        $i = 1;
        foreach ($row as $r) {
            $result['after'] .= "임계장비{$i} : {$r['NM_DIST_OBSV']}<br/>";
            $i++;
        }

        for ($i = 1; $i <= 4; $i++) {
            $auto = $vo->{"Auto{$i}"};
            $sms = $vo->{"SMS{$i}"};
            $equip = $vo->{"Equip{$i}"};

            $result['after'] .= '<hr/>';
            $result['after'] .= "{$i}단계({$auto})<br/>";

            $result['after'] .= '장비 : ';
            if ($equip != '') {
                $sql = "SELECT NM_DIST_OBSV FROM wb_equip WHERE CD_DIST_OBSV IN ({$equip})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['after'] .= "{$r['NM_DIST_OBSV']} ";
                }
            }
            $result['after'] .= '<br/>';

            $result['after'] .= 'SMS : ';
            if ($sms != '') {
                $sql = "SELECT UName FROM wb_smsuser WHERE GCode IN ({$sms})";
                $row = $rDao->SELECT_QUERY($sql);
                foreach ($row as $r) {
                    $result['after'] .= "{$r['UName']} ";
                }
            }
            $result['after'] .= '<hr/>';
            $result['after'] .= '<br/>';
        }
    }
}
echo json_encode($result);
?>	
