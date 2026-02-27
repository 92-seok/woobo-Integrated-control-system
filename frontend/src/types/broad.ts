// 장비 (wb_equip 테이블)
export interface Equipment {
  CD_DIST_OBSV: string; // 장비코드 (PK)
  NM_DIST_OBSV: string; // 장비명
  DSCODE: string; // 지역코드
  ConnPhone: string; // 전화번호
  LastStatus: string; // OK | ING | FAIL
  ConnType: string;
  USE_YN: string;
}

// 그룹 (wb_brdgroup 테이블)
export interface BroadGroup {
  GCode: number;
  GName: string;
  BEquip: string; // "001,002,003" 콤마구분
}

// 멘트 템플릿 (wb_brdment 테이블)
export interface MessageMent {
  AltCode: number;
  Title: string;
  Content: string;
  BUse: string; // ON | OFF
}

// 방송 내역 (wb_brdlist 테이블)
export interface BroadcastItem {
  BCode: number;
  Title: string;
  BType: string; // general | reserve | level1~4
  BrdType: string; // tts | alert
  TTSContent: string;
  BrdDate: string;
  BRepeat: number;
  RevType: string; // now | reserve
  standbyCount?: number;
  successCount?: number;
  failCount?: number;
}

// 방송 상세 (wb_brdlistdetail + wb_equip JOIN)
export interface BroadcastDetailItem {
  BCode: number;
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  ConnPhone: string;
  BrdStatus: string; // start | ing | end | error | fail
  RetDate: string;
}

// CID (wb_brdcid 테이블)
export interface CidItem {
  CidCode: number;
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  Cid: string;
  CStatus: string; // start | ing | end | error
  RegDate: string;
}

// 방송 발송 요청
export interface SendBroadcastPayload {
  equip: string[];
  title: string;
  tType: 'general' | 'reserve';
  sDate?: string;
  sTime?: string;
  sMin?: string;
  repeat: number;
  type: 'tts' | 'alert';
  ment?: string;
  content: string;
}

// 방송유형 라벨
export const BTYPE_LABELS: Record<string, string> = {
  general: '일반방송',
  reserve: '예약방송',
  provin: '도청방송',
  kiot: 'KIOT방송',
  level1: '주의보',
  level2: '경보',
  level3: '긴급경보',
  level4: '해제',
};

// 방송상태 라벨 + 색상
export const STATUS_CONFIG: Record<string, { label: string; color: string }> = {
  start: { label: '대기중', color: 'text-gray-500' },
  ing: { label: '방송중', color: 'text-blue-500' },
  end: { label: '방송완료', color: 'text-emerald-600' },
  error: { label: '방송오류', color: 'text-red-500' },
  fail: { label: '방송실패', color: 'text-red-500' },
};
