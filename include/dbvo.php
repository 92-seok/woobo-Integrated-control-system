<?php
/// DataBase VO 영역 ///

#region Equip
#[\AllowDynamicProperties]
class WB_EQUIP_VO {
    public $CD_DIST_OBSV;
    public $NM_DIST_OBSV;
    public $ConnType;
    public $ConnModel;
    public $ConnPhone;
    public $ConnIP;
    public $ConnPort;
    public $LastDate;
    public $LastStatus = 'ok';
    public $GB_OBSV;
    public $USE_YN;
    public $LAT;
    public $LON;
    public $DTL_ADRES;
    public $sizeX;
    public $sizeY;
    public $SubObCount = 0;
    public $DetCode = 0;
    public $Data;
}
#endregion
#region Broad & Display
//Status
class WB_BRDSTATUS_VO {
    public $CD_DIST_OBSV;
    public $Relay;
    public $Output;
    public $Volume;
    public $Bell;
    public $LastSync;
    public $BStatus;
}

class WB_BRDSEND_VO {
    public $SendCode;
    public $CD_DIST_OBSV;
    public $RegDate;
    public $RCMD;
    public $Parm1;
    public $Parm2;
    public $Parm3;
    public $Parm4 = '';
    public $BStatus;
    public $RetDate;
}

//게시물
class WB_BRDLIST_VO {
    public $BCode;
    public $CD_DIST_OBSV;
    public $Title;
    public $BType = 'general';
    public $BrdType;
    public $AltMent;
    public $TTSContent;
    public $RevType;
    public $BrdDate;
    public $BRepeat = '1';
    public $IsuCode;
    public $RegDate;
}

class WB_DISPLAYMENT_VO {
    public $disCode;
    public $Title;
    public $HtmlData;
    public $NM_DIST_OBSV;
    public $DTL_ADRES;
}

class WB_BRDLISTDETAIL_VO {
    public $BCode;
    public $CD_DIST_OBSV;
    public $BrdStatus;
    public $ErrLog;
    public $RegDate;
    public $RetDate;
}

//미리 지정된 Ment (없으면 추가 필요)
class WB_BRDMENT_VO {
    public $AltCode;
    public $Title;
    public $Content;

    public $Buse;
}

//Cid 지정 위해 필요
class WB_BRDCID_VO {
    public $CidCode;
    public $CD_DIST_OBSV;
    public $Cid;
    public $CStatus;
    public $RegDate;
    public $RetDate;
}

//Group 지정 위해 필요
class WB_BRDGROUP_VO {
    public $GCode;
    public $GName;
    public $BEquip;
}

//Ment 지정 위해 필요 (Alert와 같이 사용)
// class WB_BRDMENT_VO
// {
//     public $AltCode;
//     public $Title;
//     public $Content;
//     public $BUse;
// }
#endregion
#region Display
class WB_DISSTATUS_VO {
    public $CD_DIST_OBSV;
    public $Relay;
    public $LastDate;
    public $Power;
    public $Bright;
    public $ExpStatus;
}

//Send
class WB_DISSEND_VO {
    public $CD_DIST_OBSV;
    public $RegDate;
    public $RCMD;
    public $Parm1;
    public $Parm2;
    public $Parm3;
    public $BStatus;
    public $RetData;
}

class WB_DISPLAY_VO {
    public $DisCode;
    public $CD_DIST_OBSV;
    public $RegDate;
    public $SaveType;
    public $DisEffect;
    public $DisSpeed;
    public $DisTime;
    public $EndEffect;
    public $EndSpeed;
    public $StrTime;
    public $EndTime;
    public $Relay;
    public $ViewImg;
    public $SendImg;
    public $HtmlData;
    public $ViewOrder;
    public $DisType;
    public $Exp_YN;
}
#endregion
#region SMS
//sms정보
class WB_SMSUSER_VO {
    public $GCode;
    public $UName;
    public $Division;
    public $Phone;
    public $UPosition;
}

//sms List 정보
class WB_SMSLIST_VO {
    public $SCode;
    public $GCode;
    public $SMSTitle;
    public $SMSContent;
    public $SMSDate;
}

class WB_SMSLISTDETAIL_VO {
    public $SCode;
    public $GCode;
    public $SMSStatus;
    public $ErrLog;
    public $SMSDate;
}

//send
class WB_SENDMESSAGE_VO {
    public $MsgCode;
    public $SCode;
    public $PhoneNum;
    public $SendMessage;
    public $SendStatus;
    public $RegDate;
    public $RetDate;
}
#endregion
#region ParkCar(In/Out Count in Data) & Gate
//History, Now 입/출차 정보
class WB_PARKCAR_VO {
    public $idx;
    public $GateDate;
    public $GateSerial;
    public $CarNum;
}

//입/출 이미지 정보
class WB_PARKCARIMG_VO {
    public $idx;
    public $CarNum_Img;
    public $CarNum_Imgname;
}

//차단기 제어 send 및 내역
class WB_GATECONTROL_VO {
    public $GCtrCode;
    public $CD_DIST_OBSV;
    public $RegDate;
    public $Gate;
    public $GStatus;
}

//차단기 현재 상태 정보
class WB_GATESTATUS_VO {
    public $CD_DIST_OBSV;
    public $RegDate;
    public $Gate;
}

//차단기(LPR) 그룹 정보 -> 그룹 지정이 되어있어야 현재 주차중인 차량 계산이 가능
class WB_PARKGATEGROUP_VO {
    public $ParkGroupCode;
    public $ParkGroupName;
    public $ParkGroupAddr;
    public $ParkJoinGate;
    public $RegDate;
    public $GCode;
    public $GateArr;

    function Gate_array() {
        $this->GateArr = explode(',', $this->ParkJoinGate);
        return $this->GateArr;
    }
}

//주차중인 차량에게 메세지 발송위해 필요
class WB_PARKSMSLIST_VO {
    public $idx;
    public $CarNum;
    public $CarPhone;
    public $SMSContent;
    public $RegDate;
    public $EndDate;
    public $SendStatus;
    public $SendType;
}

class WB_PARKCARMENT_VO {
    public $SMSMentCode;
    public $Title;
    public $Content;
}
#endregion
#region Data
class WB_DATA1MIN_VO {
    public $CD_DIST_OBSV;
    public $SUB_OBSV;
    public $RegDate;
    public $MRMin = '///////////////////////////////////////////////////////////';
    public $idx;
    public $MRMinArr;
    public $Max;
    public $Min;
    public $Sum;

    // MRMin '/' 구분자로 배열로 받는 함수 + Max, Min, Sum 계산하여 this->Max, Min, Sum에 대입
    function MRmin_array() {
        $this->MRMinArr = explode('/', $this->MRMin);
        $this->Max = 0;
        $this->Min = 0;
        $this->Sum = 0;

        foreach ($this->MRMinArr as $val) {
            if ($val != '') {
                if (abs($this->Max) < abs((int) $val)) {
                    $this->Max = $val;
                }
                if (abs($this->Min) > abs((int) $val)) {
                    $this->Min = $val;
                }
                $this->Sum += (int) $val;
            }
        }

        if ($this->Max == 0) {
            $this->Max = '-';
        }
        if ($this->Min == 0) {
            $this->Min = '-';
        }
        if ($this->Sum == 0) {
            $this->Sum = '-';
        }

        return $this->MRMinArr;
    }
}

class WB_DATA1HOUR_VO {
    public $CD_DIST_OBSV;
    public $ParkGroupCode;
    public $SUB_OBSV;
    public $RegDate;
    public $MR1;
    public $MR2;
    public $MR3;
    public $MR4;
    public $MR5;
    public $MR6;
    public $MR7;
    public $MR8;
    public $MR9;
    public $MR10;
    public $MR11;
    public $MR12;
    public $MR13;
    public $MR14;
    public $MR15;
    public $MR16;
    public $MR17;
    public $MR18;
    public $MR19;
    public $MR20;
    public $MR21;
    public $MR22;
    public $MR23;
    public $MR24;
    public $DaySum;
    public $DayMin;
    public $DayMax;
    public $MRArr;
    public $Data;
    public $idx;

    // MR1 ~ MR24까지 배열로 처리 할 수 있게 배열에 담는 함수
    function MR_array() {
        $i = 0;
        if ($this->SUB_OBSV != '') {
            $this->MRArr[$i++] = $this->SUB_OBSV;
        } else {
            $this->MRArr[$i++] = '';
        }
        $this->MRArr[$i++] = $this->MR1;
        $this->MRArr[$i++] = $this->MR2;
        $this->MRArr[$i++] = $this->MR3;
        $this->MRArr[$i++] = $this->MR4;
        $this->MRArr[$i++] = $this->MR5;
        $this->MRArr[$i++] = $this->MR6;
        $this->MRArr[$i++] = $this->MR7;
        $this->MRArr[$i++] = $this->MR8;
        $this->MRArr[$i++] = $this->MR9;
        $this->MRArr[$i++] = $this->MR10;
        $this->MRArr[$i++] = $this->MR11;
        $this->MRArr[$i++] = $this->MR12;
        $this->MRArr[$i++] = $this->MR13;
        $this->MRArr[$i++] = $this->MR14;
        $this->MRArr[$i++] = $this->MR15;
        $this->MRArr[$i++] = $this->MR16;
        $this->MRArr[$i++] = $this->MR17;
        $this->MRArr[$i++] = $this->MR18;
        $this->MRArr[$i++] = $this->MR19;
        $this->MRArr[$i++] = $this->MR20;
        $this->MRArr[$i++] = $this->MR21;
        $this->MRArr[$i++] = $this->MR22;
        $this->MRArr[$i++] = $this->MR23;
        $this->MRArr[$i] = $this->MR24;

        return $this->MRArr;
    }
}

class WB_DATADIS_VO {
    //공용
    public $CD_DIST_OBSV;
    public $RegDate;

    //rain
    public $rain_yester;
    public $rain_hour;
    public $rain_now;
    public $rain_today;
    public $rain_month;
    public $rain_year;
    public $mov_1h;
    public $mov_2h;
    public $mov_3h;
    public $mov_6h;
    public $mov_12h;
    public $mov_24h;

    //water
    public $water_yester;
    public $water_today;
    public $water_speed;
    public $water_now;

    //dplace
    public $SUB_OBSV;
    public $dplace_yester;
    public $dplace_now;
    public $dplace_today;
    public $dplace_speed;

    //snow
    public $snow_yester;
    public $snow_today;
    public $snow_hour;
    public $snow_now;

    //soil
    public $soil_now;

    //tilt
    public $tilt_now;

    //flood
    public $flood_water;
    public $flood_flood;
}
#endregion
#region User & Log & AS
#[\AllowDynamicProperties]
class WB_USER_VO {
    public $idx;
    public $uId;
    public $uPwd;
    public $uName;
    public $uPhone;
    public $Auth;
    public $ip;
    public $ipUse;
    public $RegDate;
}

class WB_LOG_VO {
    public $idx;
    public $RegDate;
    public $ip;
    public $userID;
    public $pType;
    public $Page;
    public $EventType;
    public $equip;
    public $EventBefore;
    public $EventAfter;
    public $EventContent;
}

class WB_ASRECEIVED_VO {
    public $RCode;
    public $CD_DIST_OBSV;
    public $RegDate;
    public $ReceivedType;
    public $MailCheck;
    public $EMail;
    public $PhoneCheck;
    public $Phone;
    public $Content;
}
#endregion
#region Alert
//Critical & Alert Group
class WB_ISSUESTATUS_VO {
    public $idxCode;
    public $GCode;
    public $isuCode;
    public $issueGrade;
    public $issuState;
    public $Occur;
}

class WB_ISUALERT_VO {
    public $AltCode;
    public $CD_DIST_OBSV;
    public $EquType;
    public $RainTime;
    public $L1Use;
    public $L1Std;
    public $L2Use;
    public $L2Std;
    public $L3Use;
    public $L3Std;
    public $L4Use;
    public $L4Std;
    public $NowType;
    public $ChkCount;
}
class WB_ISUALERTGROUP_VO {
    public $GCode;
    public $GName;
    public $AltCode;
    public $AdmSMS;
    public $FloodSMSAuto1;
    public $Auto1;
    public $Equip1;
    public $SMS1;
    public $FloodSMSAuto2;
    public $Auto2;
    public $Equip2;
    public $SMS2;
    public $FloodSMSAuto3;
    public $Auto3;
    public $Equip3;
    public $SMS3;
    public $FloodSMSAuto4;
    public $Auto4;
    public $Equip4;
    public $SMS4;
    public $AltDate;
    public $AltUse;
}

//List
class WB_ISULIST_VO {
    public $IsuCode;
    public $GCode;
    public $IsuKind;
    public $IsuSrtAuto;
    public $IsuSrtDate;
    public $IsuEndAuto;
    public $IsuEndDate;
    public $Occur;
    public $Equip;
    public $SMS;
    public $IStatus;
    public $Send;
    public $HAOK;
}

//Ment
class WB_ISUMENT_VO {
    public $MentCode;
    public $BrdMent1;
    public $BrdMent2;
    public $BrdMent3;
    public $BrdMent4;
    public $DisMent1;
    public $DisMent2;
    public $DisMent3;
    public $DisMent4;
    public $SMSMent1;
    public $SMSMent2;
    public $SMSMent3;
    public $SMSMent4;
}
#endregion
#region Radar
class KMA_SATELLITE_VO {
    public $date1; // 안 씀
    public $date2; // 안 씀
    public $idx;
    public $datatime;
    public $type;
    public $filename;
}
#endregion
#region Radar
class KMA_RADAR_VO {
    public $idx;
    public $datatime;
    public $type;
    public $filename;
}
#endregion
/// DataBase VO 영역 ///
