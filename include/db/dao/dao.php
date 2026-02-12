<?php
require_once 'dao_t.php';

#region EQUIP
class WB_EQUIP_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_equip', 'CD_DIST_OBSV');
    }

    public function FAIL_QUERY($cd_dist_osbv)
    {
        $this->sql = "UPDATE {$this->table} SET LastStatus = 'fail' WHERE CD_DIST_OBSV = '{$cd_dist_osbv}'";
    }
}
#endregion

class WB_OTP_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_otp', 'IDX');
    }
}
#region BROADCAST DAO
class WB_BRDALERT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdalert', 'AltCode');
    }
}

class WB_BRDCID_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdcid', 'CidCode');
    }
}

class WB_BRDGROUP_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdgroup', 'GCode');
    }
}

class WB_BRDLIST_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdlist', 'BCode');
    }
}

class WB_BRDLISTDETAIL_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdlistdetail', 'RegDate DESC');
    }
}

class WB_BRDMENT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdment', 'AltCode');
    }
}

class WB_BRDSEND_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_brdsend', 'SendCode');
    }

    public function FAIL_QUERY($idx)
    {
        $this->sql = "UPDATE {$this->table} SET BStatus = 'fail' WHERE SendCode = {$idx}";
        return $this->EXECUTE($this->sql);
    }
}
#endregion
#region DISPLAY DAO
class WB_DISPLAY_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_display', 'DisCode');
    }

    public function base64toImage($imgTag, $path)
    {
        $img = str_replace('data:image/png;base64,', '', $imgTag);
        $img = str_replace(' ', '+', $img);
        $imgData = base64_decode($img);
        file_put_contents($path, $imgData);
    }

    public function imageResize($name, $addWidth = 0.5, $addHeight = 0.5)
    {
        $info = getimagesize($name);
        $img_width = round($info[0] * $addWidth);
        $img_height = round($info[1] * $addHeight);

        $image = imagecreatefrompng($name);

        $new_image = imagecreatetruecolor($img_width, $img_height);
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $img_width, $img_height, $info[0], $info[1]);
        imagepng($new_image, $name);

        imagedestroy($image);
        imagedestroy($new_image);
    }
}

class WB_DISSEND_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_dissend', 'SendCode DESC');
    }

    public function FAIL_QUERY($idx)
    {
        $this->sql = "UPDATE {$this->table} SET BStatus = 'fail' WHERE SendCode = {$idx}";
        return $this->EXECUTE($this->sql);
    }
}

class WB_DISSTATUS_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_disstatus', 'CD_DIST_OBSV');
    }
}

class WB_DISPLAYMENT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_displayment', 'disCode DESC');
    }

    public function SELECT_SINGLE($where = '1')
    {
        return $this->SELECT($where)[0];
    }
}
#endregion

#region GATE DAO
class WB_GATECONTROL_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_gatecontrol', 'GCtrCode DESC');
    }

    public function FAIL_QUERY($idx)
    {
        $this->sql = "UPDATE {$this->table} SET GStatus = 'fail' WHERE GCtrCode = {$idx}";
        return $this->EXECUTE($this->sql);
    }
}

class WB_GATESTATUS_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_gatestatus', 'CD_DIST_OBSV');
    }

    public function SELECT_SINGLE($where = '1', $order = 'CD_DIST_OBSV')
    {
        return $this->SELECT($where, $order)[0];
    }
}

class WB_PARKCARHIST_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkcarhist', 'idx DESC', 'WB_PARKCAR_VO');
    }

    function insertCarInfo(WB_PARKCAR_VO $vo)
    {
        $obj = (object) [
            'GateDate' => $vo->GateDate,
            'GateSerial' => $vo->GateSerial,
            'CarNum' => $vo->CarNum,

            'uGateDate' => $vo->GateDate,
            'uGateSerial' => $vo->GateSerial,
            'uCarNum' => $vo->CarNum,
        ];

        $sql = "INSERT INTO {$this->table} (GateDate, GateSerial, CarNum) VALUES (:GateDate, :GateSerial, :CarNum) ";
        $sql .= 'ON DUPLICATE KEY UPDATE ';
        $sql .= 'GateDate = :uGateDate, ';
        $sql .= 'GateSerial = :uGateSerial, ';
        $sql .= 'CarNum = :uCarNum';

        $this->PREPARE($sql, $obj);
    }
}

class WB_PARKCARNOW_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkcarnow', 'idx DESC', 'WB_PARKCAR_VO');
    }

    function insertCarInfo(WB_PARKCAR_VO $vo)
    {
        $obj = (object) [
            'idx' => $vo->idx,
            'GateDate' => $vo->GateDate,
            'GateSerial' => $vo->GateSerial,
            'CarNum' => $vo->CarNum,

            'uidx' => $vo->idx,
            'uGateDate' => $vo->GateDate,
            'uGateSerial' => $vo->GateSerial,
        ];

        $this->sql = "INSERT INTO {$this->table} (idx, GateDate, GateSerial, CarNum) ";
        $this->sql .= 'VALUES (:idx, :GateDate, :GateSerial, :CarNum) ';
        $this->sql .= 'ON DUPLICATE KEY UPDATE ';
        $this->sql .= 'idx = :uidx, ';
        $this->sql .= 'GateDate = :uGateDate, ';
        $this->sql .= 'GateSerial = :uGateSerial';

        $this->PREPARE($this->sql, $obj);
    }

    public function delete3DaysInfo()
    {
        $date = date('Y-m-d', strtotime('-3 day'));
        return $this->DELETE("GateDate <= '{$date}'");
    }

    public function outCarDelete($CarNum)
    {
        $obj = (object) ['CarNum' => $CarNum];

        $this->sql = "DELETE FROM {$this->table} WHERE CarNum = :CarNum";

        $this->PREPARE($this->sql, $obj);
    }
}

class WB_PARKCARIMG_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkcarimg', 'idx DESC');
    }

    function insertCarImg(WB_PARKCARIMG_VO $vo)
    {
        try {
            $this->sql = "INSERT INTO {$this->table} (idx, CarNum_Img, CarNum_Imgname) ";
            $this->sql .= 'VALUES (:idx, :CarNum_Img, :CarNum_Imgname) ';
            $this->sql .= 'ON DUPLICATE KEY UPDATE ';
            $this->sql .= 'idx = :idx, ';
            $this->sql .= 'CarNum_Img = :CarNum_Img, ';
            $this->sql .= 'CarNum_Imgname = :CarNum_Imgname';

            $this->PREPARE($this->sql, $vo);
        } catch (PDOException | Exception $e) {
            warningLog("[WARN] {$e->getMessage()}({$this->sql})");
        }
    }
}

class WB_PARKGATEGROUP_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkgategroup', 'ParkGroupCode');
    }
}

class WB_PARKSMSLIST_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parksmslist', 'idx DESC');
    }
}

class WB_PARKSMSMENT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parksmsment', 'idx DESC');
        $this->addInsert();
    }

    function addInsert()
    {
        $this->sql = "SELECT * FROM {$this->table}";
        $cnt = $this->QUERY();

        if (count($cnt) < 1) {
            $ment = '[재난안전문자] 둔치주차장 침수위험! 신속 이동주차 요망! -재난안전대책본부-';
            $this->EXECUTE("INSERT INTO {$this->table} (Title, Content) VALUES ('침수위험알림', '{$ment}')");
        }
    }
}

class WB_PARKCARINCNT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkcarincnt', 'RegDate DESC', 'WB_PARKCARCNT_VO');
    }

    function carCountUp(WB_PARKCARCNT_VO $vo, $MR)
    {
        $this->sql = "INSERT INTO {$this->table} (ParkGroupCode, RegDate, {$MR}, DaySum) ";
        $this->sql .= "VALUES ('{$vo->ParkGroupCode}', '{$vo->RegDate}', 1, 1) ";
        $this->sql .= 'ON DUPLICATE KEY UPDATE ';
        $this->sql .= "{$MR} = {$MR} + 1, ";
        $this->sql .= 'DaySum = DaySum + 1';

        $this->EXECUTE();
    }

    function carCountUpColNull(WB_PARKCARCNT_VO $vo, $MR)
    {
        $this->sql = "UPDATE {$this->table} SET {$MR} = 1, DaySum = 1 WHERE ParkGroupCode = '{$vo->ParkGroupCode}' AND RegDate = '{$vo->RegDate}'";

        $this->EXECUTE();
    }
}

class WB_PARKCAROUTCNT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_parkcaroutcnt', 'RegDate DESC', 'WB_PARKCARCNT_VO');
    }

    function carCountDown(WB_PARKCARCNT_VO $vo, $MR)
    {
        $this->sql = "INSERT INTO {$this->table} (ParkGroupCode, RegDate, {$MR}) ";
        $this->sql .= "VALUES ('{$vo->ParkGroupCode}', '{$vo->RegDate}', 1) ";
        $this->sql .= 'ON DUPLICATE KEY UPDATE ';
        $this->sql .= "{$MR} = {$MR} + 1, ";
        $this->sql .= 'DaySum = DaySum + 1';

        $this->EXECUTE();
    }

    function carCountDownColNull(WB_PARKCARCNT_VO $vo, $MR)
    {
        $this->sql = "UPDATE {$this->table} SET {$MR} = 1, DaySum = 1 WHERE ParkGroupCode = '{$vo->ParkGroupCode}' AND RegDate = '{$vo->RegDate}'";

        $this->EXECUTE();
    }
}
#endregion
#region SMS DAO
class WB_SENDMESSAGE_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_sendmessage', 'MsgCode DESC');
    }
}

class WB_SMSLIST_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_smslist', 'SCode DESC');
    }
}

class WB_SMSUSER_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_smsuser', 'GCode DESC');
    }
}
#endregion
#region ALERT & CRITICAL DAO
class WB_ISSUESTATUS_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_issuestatus', 'idxCode DESC');
    }
}

class WB_ISUALERT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_isualert', 'AltCode DESC');
    }
}

class WB_ISUALERTGROUP_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_isualertgroup', 'GCode DESC');
    }
}

class WB_ISULIST_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_isulist', 'IsuCode DESC');
    }
}

class WB_ISUMENT_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_isument', 'MentCode');
        $this->addInsert();
    }

    function addInsert()
    {
        $this->sql = "SELECT * FROM {$this->table}";
        $cnt = $this->QUERY();

        if (count($cnt) < 1) {
            $this->sql = 'INSERT INTO wb_isument (MentCode) VALUES (1)';
            $this->EXECUTE();
        }
    }
}
#endregion
#region LOG & USER & SATALLITE & AS DAO
class WB_LOG_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_log', 'idx DESC');
    }
}

class WB_USER_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_user', 'idx DESC');
    }
}

class WB_ASRECEIVED_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('wb_asreceived', 'RCode DESC');
    }
}

class KMA_SATELLITE_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('kma_satellite', 'RCode DESC');
    }

    public function getraderImage($type = 'RDR')
    {
        $rtv = false;

        if ($type == 'RDR') {
            $rtv = $this->SINGLE("type = 'RDR", 'filename DESC', 1);
        } else {
            $rtv = $this->SINGLE("type = 'SAT", 'SUBSTR(filename, INSTR(filename, concat(date1, date2)), 12) DESC', 1);
        }

        return $rtv;
    }
}
#endregion
#region DATA
class WB_DATA_DAO extends DAO_T
{
    function __construct()
    {
        parent::__construct('data', 'RegDate DESC', 'WB_DATA_VO');
    }

    public function SELECT1HOUR(WB_DATA_VO $vo)
    {
        $data = array_fill(0, 60, null);
        $y = $vo->regDate->format('Y');
        $table = "wb_{$vo->type}1min_{$y}";

        if ($vo->subObsv > 0) {
            $dplaceWhere = " AND SUB_OBSV = '{$vo->subObsv}'";
        } else {
            $dplaceWhere = '';
        }

        try {
            $sql = "SELECT * FROM {$table} WHERE CD_DIST_OBSV = '{$vo->cdDistObsv}' AND RegDate = '{$vo->regDate->format('YmdH')}' {$dplaceWhere}";
            $res = $this->ARRAYTOSINGLE($sql);
            if ($res !== false) {
                $data = explode('/', $res['MRMin']);

                if (count($data) != 60) {
                    $data = array_fill(0, 60, null);
                } else {
                    for ($i = 0; $i < 60; $i++) {
                        $data[$i] = floatval($data[$i]);
                    }
                }

                $vo->data = $data;
                $vo->sum = array_sum($data);
                $vo->max = max($data);
                $vo->min = min($data);
                return $vo;
            } else {
                new Exception('Query오류');
            }
        } catch (PDOException | Exception $ex) {
            warningLog("SELECT1HOUR()::{$ex->getMessage()}({$this->sql})");
            return false;
        }
    }

    public function SELECT1DAY(WB_DATA_VO $vo)
    {
        $data = array_fill(0, 24, null);
        $table = "wb_{$vo->type}1hour";

        if ($vo->subObsv > 0) {
            $dplaceWhere = " AND SUB_OBSV = '{$vo->subObsv}'";
        } else {
            $dplaceWhere = '';
        }

        try {
            $sql = "SELECT * FROM {$table} WHERE CD_DIST_OBSV = '{$vo->cdDistObsv}' AND RegDate = '{$vo->regDate->format('Ymd')}' {$dplaceWhere}";
            $res = $this->ARRAYTOSINGLE($sql);
            if ($res !== false) {
                for ($i = 1; $i <= 24; $i++) {
                    if ($res["MR{$i}"] !== '') {
                        $floatVal = floatval($res["MR{$i}"]);
                        $data[$i - 1] = $floatVal;
                    }
                }

                $vo->data = $data;
                $vo->sum = array_sum($data);
                $vo->max = max($data);
                $vo->min = min($data);

                return $vo;
            } else {
                new Exception('Query오류');
            }
        } catch (PDOException | Exception $ex) {
            warningLog("SELECT1DAY()::{$ex->getMessage()}({$this->sql})");
            return false;
        }
    }

    public function SELECT1MONTH(WB_DATA_VO $vo)
    {
        $dplaceWhere = '';
        $column = null;
        $table = "wb_{$vo->type}1hour";

        $data = array_fill(0, intval($vo->regDate->format('t')), null);
        $minData = array_fill(0, intval($vo->regDate->format('t')), null);

        try {
            switch ($vo->type) {
                case 'rain':
                    $column = 'DaySum';
                    break;

                case 'water':
                    $column = 'DayMax, DayMin';
                    break;

                case 'dplace':
                    $dplaceWhere = " AND SUB_OBSV = '{$vo->subObsv}'";
                case 'soil':
                case 'snow':
                case 'tilt':
                    $column = 'DayMax';
                    break;

                default:
                    break;
            }

            $sql = "SELECT RegDate, {$column} FROM {$table} WHERE CD_DIST_OBSV = '{$vo->cdDistObsv}' AND MONTH(RegDate) = {$vo->regDate->format('m')} {$dplaceWhere}";
            $res = $this->SQLTOARRAY($sql);
            if ($res !== false) {
                foreach ($res as $row) {
                    $regDate = new DateTime($row['RegDate']);
                    $idx = intval($regDate->format('d')) - 1;

                    switch ($vo->type) {
                        case 'rain':
                            $data[$idx] = floatval($row['DaySum']);
                            break;

                        case 'water':
                            $data[$idx] = floatval($row['DayMax']);
                            $minData[$idx] = floatval($row['DayMin']);
                            break;

                        case 'dplace':
                        case 'soil':
                        case 'snow':
                        case 'tilt':
                            $data[$idx] = floatval($row['DayMax']);
                            break;

                        default:
                            break;
                    }
                }

                $vo->data = $data;
                $vo->minData = $vo->type == 'water' ? $minData : [];
                $vo->sum = array_sum($data);
                $vo->max = max($data);
                $vo->min = min($data);
                $vo->min = $vo->type == 'water' ? min($minData) : min($data);

                return $vo;
            }
        } catch (PDOException | Exception $ex) {
            warningLog("SELECT1MONTH()::{$ex->getMessage()}({$this->sql})");
            return false;
        }
    }

    public function SELECT1YEAR(WB_DATA_VO $vo)
    {
        $dplaceWhere = '';
        $column = null;
        $table = "wb_{$vo->type}1hour";

        $data = array_fill(0, 12, null);
        $minData = array_fill(0, 12, null);

        try {
            switch ($vo->type) {
                case 'rain':
                    $column = 'SUM(DaySum) as sum';
                    break;

                case 'water':
                    $column = 'MAX(DayMax) as max, MIN(DayMin) as min';
                    break;

                case 'dplace':
                    $dplaceWhere = " AND SUB_OBSV = '{$vo->subObsv}'";
                case 'soil':
                case 'snow':
                case 'tilt':
                    $column = 'MAX(DayMax) as max';
                    break;

                default:
                    break;
            }

            $sql = "SELECT RegDate, {$column} FROM {$table} WHERE CD_DIST_OBSV = '{$vo->cdDistObsv}' {$dplaceWhere}";
            $sql .= "GROUP BY MONTH(RegDate) HAVING YEAR(RegDate) = {$vo->regDate->format('Y')}";
            $res = $this->SQLTOARRAY($sql);
            if ($res !== false) {
                foreach ($res as $row) {
                    $regDate = new DateTime($row['RegDate']);
                    $idx = intval($regDate->format('m')) - 1;

                    switch ($vo->type) {
                        case 'rain':
                            $data[$idx] = floatval($row['sum']);
                            break;

                        case 'water':
                            $data[$idx] = floatval($row['max']);
                            $minData[$idx] = floatval($row['min']);
                            break;

                        case 'dplace':
                        case 'soil':
                        case 'snow':
                        case 'tilt':
                            $data[$idx] = floatval($row['max']);
                            break;

                        default:
                            break;
                    }
                }

                $vo->data = $data;
                $vo->minData = $vo->type == 'water' ? $minData : [];
                $vo->sum = array_sum($data);
                $vo->max = max($data);
                $vo->min = min($data);
                $vo->min = $vo->type == 'water' ? min($minData) : min($data);

                return $vo;
            }
        } catch (PDOException | Exception $ex) {
            warningLog("SELECT1YEAR()::{$ex->getMessage()}({$this->sql})");
            return false;
        }
    }

    public function SELECTDIS(WB_DATA_VO $vo)
    {
        try {
            if ($this->existTable("wb_{$vo->type}dis")) {
                $sql = "SELECT * FROM wb_{$vo->type}dis WHERE CD_DIST_OBSV = '{$vo->cdDistObsv}'" . ($vo->type == 'dplace' ? " AND SUB_OBSV = '{$vo->subObsv}'" : '');
                $res = $this->ARRAYTOSINGLE($sql);

                if ($res !== false) {
                    switch ($vo->type) {
                        case 'rain':
                            $vo->regDate = new DateTime($res['RegDate']);
                            $vo->data['yester'] = $res['rain_yester'];
                            $vo->data['today'] = $res['rain_today'];
                            $vo->data['now'] = $res['rain_hour'];

                            $vo->data['mov_1h'] = $res['mov_1h'];
                            $vo->data['mov_2h'] = $res['mov_2h'];
                            $vo->data['mov_3h'] = $res['mov_3h'];
                            $vo->data['mov_6h'] = $res['mov_6h'];
                            $vo->data['mov_12h'] = $res['mov_12h'];
                            $vo->data['mov_24h'] = $res['mov_24h'];
                            break;

                        case 'water':
                            $vo->regDate = new DateTime($res['RegDate']);
                            $vo->data['yester'] = $res['water_yester'];
                            $vo->data['today'] = $res['water_today'];
                            $vo->data['now'] = $res['water_now'];
                            break;

                        case 'dplace':
                            $vo->regDate = new DateTime($res['RegDate']);
                            $vo->data['yester'] = $res['dplace_yester'];
                            $vo->data['today'] = $res['dplace_today'];
                            $vo->data['now'] = $res['dplace_now'];

                            $vo->data['change'] = $res['dplace_change'];
                            $vo->data['speed'] = $res['dplace_speed'];
                            break;

                        case 'snow':
                            $vo->regDate = new DateTime($res['RegDate']);
                            $vo->data['yester'] = $res['snow_yester'];
                            $vo->data['today'] = $res['snow_today'];
                            $vo->data['now'] = $res['snow_hour'];
                            break;

                        case 'soil':
                        case 'tilt':
                        case 'flood':
                            $vo->regDate = new DateTime($res['RegDate']);
                            $vo->data['yester'] = $res['yester'];
                            $vo->data['today'] = $res['today'];
                            $vo->data['now'] = $res['now'];
                            break;

                        default:
                            break;
                    }
                } else {
                    $currentTime = $vo->regDate;
                    $today = $this->SELECT1DAY($vo);
                    $todayIdx = intval($currentTime->format('H'));

                    $vo->regDate = $currentTime->sub(new DateInterval('P1D'));
                    $yester = $this->SELECT1DAY($vo);

                    $minIdx = intval($currentTime->format('m'));

                    switch ($vo->type) {
                        case 'rain':
                            $vo->data['yester'] = $yester->sum;
                            $vo->data['today'] = $today->sum;
                            $vo->data['now'] = $today->data[$todayIdx];

                            $movIndexArr = [1, 2, 3, 6, 12, 24];
                            $moveData = [];

                            for ($i = 24; $i > 1; $i--) {
                                $moveTime = $currentTime->sub(new DateInterval("P{$i}H"));
                                $vo->regDate = $moveTime;

                                $_data = $this->SELECT1HOUR($vo);
                                $moveData = array_merge($moveData, $_data->data);
                            }

                            foreach ($movIndexArr as $h) {
                                // $range = 24 * 60 - $h * 60 + $minidx;
                                $range = 24 * 60 - $h * 60 + $minIdx;

                                $_data = $moveData;
                                $_data = range($range, $minIdx);

                                // $vo->data["mov_{$h}"] = sum($_data);
                                $vo->data["mov_{$h}"] = array_sum($_data);
                            }
                            break;

                        case 'dplace':
                            $this->table = 'wb_dplace1hour';
                            $standVo = $this->SQLTOARRAY("CD_DIST_OBSV = '{$vo->cdDistObsv}' AND SUB_OBSV = '{$vo->subObsv}'", 'RegDate DESC');

                            $standVal = null;
                            foreach ($standVo as $svo) {
                                for ($i = 1; $i <= 24; $i++) {
                                    try {
                                        if (floatval($svo["MR{$i}"]) > 0) {
                                            $standVal = floatval($svo["MR{$i}"]);
                                            break;
                                        }
                                    } catch (PDOException | Exception $ex) {
                                        continue;
                                    }
                                }

                                if ($standVal !== null) {
                                    break;
                                }
                            }
                            $vo->data['change'] = abs($standVal - $today->data[$todayIdx]);

                            $h = 1;
                            $m = $minIdx;
                            $before1HourVo = new WB_DATA_VO();
                            while (true) {
                                try {
                                    if ($h == 1 || $m >= 60) {
                                        $vo->regDate = $currentTime->sub(new DateInterval("P{$h}H"));
                                        $before1HourVo = $this->SELECT1HOUR($vo);

                                        $h++;
                                        $m = 0;
                                    } elseif (floatval($before1HourVo->data[$i]) > 0) {
                                        $beforeVal = floatval($before1HourVo->data[$i]);
                                        break;
                                    } elseif ($h > 5);
                                    $beforeVal = $today->data[$todayIdx];
                                    break;
                                } catch (PDOException | Exception $ex) {
                                    continue;
                                }

                                $m++;
                            }

                            $vo->data['speed'] = abs($beforeVal - $today->data[$todayIdx]);
                        case 'water':
                        case 'snow':
                        case 'soil':
                        case 'tilt':
                        case 'flood':
                            $vo->data['yester'] = $yester->max;
                            $vo->data['today'] = $today->max;
                            $vo->data['now'] = $today->data[$todayIdx];
                            break;
                    }
                }
            }

            return $vo;
        } catch (PDOException | Exception $ex) {
            warningLog("SELECTDIS()::{$ex->getMessage()}");
            return false;
        }
    }
}
#endregion
