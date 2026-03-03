import type { Equipment, BroadGroup, MessageMent, BroadcastItem, BroadcastDetailItem, CidItem } from '../types/broad';
import type { GateEquipment, GateStatus, GateControlHistory } from '../types/gate';

// -------------- 장비 샘플 (예경보) --------------
export const MOCK_EQUIPMENTS: Equipment[] = [
  {
    CD_DIST_OBSV: '001',
    NM_DIST_OBSV: '강남관측소',
    DSCODE: 'DS001',
    ConnPhone: '010-1234-5678',
    LastStatus: 'OK',
    ConnType: 'LTE',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '002',
    NM_DIST_OBSV: '서초관측소',
    DSCODE: 'DS002',
    ConnPhone: '010-2345-6789',
    LastStatus: 'OK',
    ConnType: 'LTE',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '003',
    NM_DIST_OBSV: '송파관측소',
    DSCODE: 'DS003',
    ConnPhone: '010-3456-7890',
    LastStatus: 'ING',
    ConnType: 'LTE',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '004',
    NM_DIST_OBSV: '관악관측소',
    DSCODE: 'DS004',
    ConnPhone: '010-4567-8901',
    LastStatus: 'FAIL',
    ConnType: 'WiFi',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '005',
    NM_DIST_OBSV: '영등포관측소',
    DSCODE: 'DS005',
    ConnPhone: '010-5678-9012',
    LastStatus: 'OK',
    ConnType: 'LTE',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '006',
    NM_DIST_OBSV: '마포관측소',
    DSCODE: 'DS006',
    ConnPhone: '010-6789-0123',
    LastStatus: 'OK',
    ConnType: 'WiFi',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '007',
    NM_DIST_OBSV: '용산관측소',
    DSCODE: 'DS007',
    ConnPhone: '010-7890-1234',
    LastStatus: 'ING',
    ConnType: 'LTE',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '008',
    NM_DIST_OBSV: '종로관측소',
    DSCODE: 'DS008',
    ConnPhone: '010-8901-2345',
    LastStatus: 'OK',
    ConnType: 'LTE',
    USE_YN: '1',
  },
];

// 그룹 샘플
export const MOCK_GROUPS: BroadGroup[] = [
  { GCode: 1, GName: '강남권', BEquip: '001,002,003' },
  { GCode: 2, GName: '서남권', BEquip: '004,005,006' },
  { GCode: 3, GName: '도심권', BEquip: '007,008' },
];

// 멘트 샘플
// TTS 멘트 (멘트관리에서 사용자가 추가한 것) ─
export const MOCK_TTS_MENTS: MessageMent[] = [
  {
    AltCode: 1,
    Title: '정기 점검 안내',
    Content: '금일 14시부터 16시까지 정기 점검이 진행됩니다. 양해 부탁드립니다.',
    BUse: 'ON',
  },
  {
    AltCode: 2,
    Title: '시스템 복구 안내',
    Content: '시스템이 정상 복구되었습니다. 이용에 참고 부탁드립니다.',
    BUse: 'ON',
  },
  {
    AltCode: 3,
    Title: '하천 순찰 안내',
    Content: '금일 하천 순찰이 예정되어 있습니다. 안전에 유의하시기 바랍니다.',
    BUse: 'ON',
  },
];

// 예경보 멘트 (기존 저장된 경보 + 멘트관리에서 추가한 것)
export const MOCK_ALERT_MENTS: MessageMent[] = [
  {
    AltCode: 101,
    Title: '[기본] 호우주의보',
    Content: '현재 호우주의보가 발효중입니다. 하천 근처 접근을 자제하시고 안전에 유의하시기 바랍니다.',
    BUse: 'ON',
  },
  {
    AltCode: 102,
    Title: '[기본] 호우경보',
    Content: '호우경보가 발효되었습니다. 저지대 주민은 즉시 높은 곳으로 대피하시기 바랍니다.',
    BUse: 'ON',
  },
  {
    AltCode: 103,
    Title: '[기본] 경보해제',
    Content: '호우주의보가 해제되었습니다. 하천 수위가 안정될 때까지 주의하시기 바랍니다.',
    BUse: 'ON',
  },
  {
    AltCode: 104,
    Title: '긴급 대피 방송',
    Content: '긴급 상황입니다. 모든 인원은 즉시 지정된 대피소로 이동하시기 바랍니다.',
    BUse: 'ON',
  },
];

// 방송 내역 샘플
export const MOCK_HISTORY: BroadcastItem[] = [
  {
    BCode: 101,
    Title: '호우주의보 1차 방송',
    BType: 'general',
    BrdType: 'tts',
    TTSContent: '현재 호우주의보가 발효중입니다.',
    BrdDate: '2026-02-27 09:00:00',
    BRepeat: 3,
    RevType: 'now',
    standbyCount: 0,
    successCount: 5,
    failCount: 1,
  },
  {
    BCode: 102,
    Title: '정기 점검 예약방송',
    BType: 'reserve',
    BrdType: 'tts',
    TTSContent: '금일 14시부터 점검이 진행됩니다.',
    BrdDate: '2026-02-27 14:00:00',
    BRepeat: 1,
    RevType: 'reserve',
    standbyCount: 3,
    successCount: 0,
    failCount: 0,
  },
  {
    BCode: 103,
    Title: '긴급 경보 발령',
    BType: 'level2',
    BrdType: 'alert',
    TTSContent: '긴급 경보가 발령되었습니다.',
    BrdDate: '2026-02-26 15:30:00',
    BRepeat: 5,
    RevType: 'now',
    standbyCount: 0,
    successCount: 8,
    failCount: 0,
  },
  {
    BCode: 104,
    Title: '해제 방송',
    BType: 'level4',
    BrdType: 'tts',
    TTSContent: '경보가 해제되었습니다.',
    BrdDate: '2026-02-26 18:00:00',
    BRepeat: 2,
    RevType: 'now',
    standbyCount: 0,
    successCount: 6,
    failCount: 2,
  },
];

// 방송 상세 샘플 (BCode: 101 기준)
export const MOCK_DETAIL: BroadcastDetailItem[] = [
  {
    BCode: 101,
    CD_DIST_OBSV: '001',
    NM_DIST_OBSV: '강남관측소',
    ConnPhone: '01012345678',
    BrdStatus: 'end',
    RetDate: '',
  },
  {
    BCode: 101,
    CD_DIST_OBSV: '002',
    NM_DIST_OBSV: '서초관측소',
    ConnPhone: '01023456789',
    BrdStatus: 'end',
    RetDate: '',
  },
  {
    BCode: 101,
    CD_DIST_OBSV: '003',
    NM_DIST_OBSV: '송파관측소',
    ConnPhone: '01034567890',
    BrdStatus: 'end',
    RetDate: '',
  },
  {
    BCode: 101,
    CD_DIST_OBSV: '004',
    NM_DIST_OBSV: '관악관측소',
    ConnPhone: '01045678901',
    BrdStatus: 'fail',
    RetDate: '',
  },
  {
    BCode: 101,
    CD_DIST_OBSV: '005',
    NM_DIST_OBSV: '영등포관측소',
    ConnPhone: '01056789012',
    BrdStatus: 'end',
    RetDate: '',
  },
  {
    BCode: 101,
    CD_DIST_OBSV: '006',
    NM_DIST_OBSV: '마포관측소',
    ConnPhone: '01067890123',
    BrdStatus: 'end',
    RetDate: '',
  },
];

// CID 샘플
export const MOCK_CID_LIST: CidItem[] = [
  {
    CidCode: 1,
    CD_DIST_OBSV: '001',
    NM_DIST_OBSV: '강남관측소',
    Cid: '012-2345-6789',
    CStatus: 'end',
    RegDate: '2026-02-20 10:00:00',
  },
  {
    CidCode: 2,
    CD_DIST_OBSV: '002',
    NM_DIST_OBSV: '서초관측소',
    Cid: '012-2345-6780',
    CStatus: 'end',
    RegDate: '2026-02-20 10:05:00',
  },
  {
    CidCode: 3,
    CD_DIST_OBSV: '005',
    NM_DIST_OBSV: '영등포관측소',
    Cid: '012-5678-9012',
    CStatus: 'ing',
    RegDate: '2026-02-27 09:30:00',
  },
  {
    CidCode: 4,
    CD_DIST_OBSV: '007',
    NM_DIST_OBSV: '용산관측소',
    Cid: '012-7890-1234',
    CStatus: 'error',
    RegDate: '2026-02-27 09:35:00',
  },
];

// -------------- 장비 샘플 (차단기) --------------

// 차단기 장비 목록
export const MOCK_GATE_EQUIPMENTS: GateEquipment[] = [
  {
    CD_DIST_OBSV: '0123',
    NM_DIST_OBSV: '우보차단기 1',
    ConnIP: '192.168.1.6',
    ConnPort: '4096',
    DTL_ADRES: '경기도 성남시 중원구 갈마치로 215',
    LAT: '37.5012',
    LON: '127.0396',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '0124',
    NM_DIST_OBSV: '우보차단기 2',
    ConnIP: '192.168.2.6',
    ConnPort: '4096',
    DTL_ADRES: '경기도 성남시 중원구 갈마치로 215',
    LAT: '37.5045',
    LON: '126.9970',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '0125',
    NM_DIST_OBSV: '우보차단기 3',
    ConnIP: '192.168.3.6',
    ConnPort: '4096',
    DTL_ADRES: '경기도 성남시 중원구 갈마치로 215',
    LAT: '37.5133',
    LON: '127.1001',
    USE_YN: '1',
  },
  {
    CD_DIST_OBSV: '0126',
    NM_DIST_OBSV: '우보차단기 4',
    ConnIP: '192.168.4.6',
    ConnPort: '4096',
    DTL_ADRES: '경기도 성남시 중원구 갈마치로 215',
    LAT: '37.4784',
    LON: '126.9516',
    USE_YN: '1',
  },
];

// 차단기 현재상태
export const MOCK_GATE_STATUSES: GateStatus[] = [
  { CD_DIST_OBSV: '0123', Gate: 'open', RegDate: '2026-03-03 10:01:11' },
  { CD_DIST_OBSV: '0124', Gate: 'close', RegDate: '2026-03-03 09:40:15' },
  { CD_DIST_OBSV: '0125', Gate: 'close', RegDate: '2026-03-02 17:10:33' },
  { CD_DIST_OBSV: '0126', Gate: 'open', RegDate: '2026-02-28 15:24:22' },
];

// 차단기 제어 이력
export const MOCK_GATE_HISTORY: GateControlHistory[] = [
  {
    GCtrCode: 1,
    CD_DIST_OBSV: '0213',
    NM_DIST_OBSV: '우보차단기 1',
    Gate: 'open',
    GStatus: 'end',
    RegDate: '2026-03-03 08:00:00',
  },
  {
    GCtrCode: 2,
    CD_DIST_OBSV: '0214',
    NM_DIST_OBSV: '우보차단기 2',
    Gate: 'close',
    GStatus: 'end',
    RegDate: '2026-03-03 07:30:00',
  },
  {
    GCtrCode: 3,
    CD_DIST_OBSV: '0213',
    NM_DIST_OBSV: '우보차단기 3',
    Gate: 'close',
    GStatus: 'end',
    RegDate: '2026-03-02 18:00:00',
  },
  {
    GCtrCode: 4,
    CD_DIST_OBSV: '0215',
    NM_DIST_OBSV: '우보차단기 4',
    Gate: 'open',
    GStatus: 'error',
    RegDate: '2026-03-02 15:30:00',
  },
  {
    GCtrCode: 5,
    CD_DIST_OBSV: '0216',
    NM_DIST_OBSV: '우보차단기 5',
    Gate: 'close',
    GStatus: 'end',
    RegDate: '2026-03-02 14:00:00',
  },
  {
    GCtrCode: 6,
    CD_DIST_OBSV: '0214',
    NM_DIST_OBSV: '우보차단기 6',
    Gate: 'open',
    GStatus: 'end',
    RegDate: '2026-03-01 09:00:00',
  },
  {
    GCtrCode: 7,
    CD_DIST_OBSV: '0215',
    NM_DIST_OBSV: '우보차단기 7',
    Gate: 'close',
    GStatus: 'ing',
    RegDate: '2026-03-01 08:00:00',
  },
  {
    GCtrCode: 8,
    CD_DIST_OBSV: '0213',
    NM_DIST_OBSV: '우보차단기 8',
    Gate: 'open',
    GStatus: 'end',
    RegDate: '2026-02-28  16:00:00',
  },
];
