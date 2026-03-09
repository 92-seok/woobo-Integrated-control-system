// WB_Equip 테이블 (전광판, GB_OBSV = '18')
export interface DisplayEquipment {
  CD_DIST_OBSV: string; // 장비코드 (PK)
  GB_OBSV: string; // 장비구분 ('18' = 전광판)
  NM_DIST_OBSV: string; // 장비명
  DTL_ADRES: string; // 설치지역
  ConnModel: string; // 로거모델 (EWDPL_LAN | EWDPL_C16 | EWDISPLAY_LAN)
  ConnIP: string; // IP주소
  ConnPort: string; // 포트
  SizeX: number; // 가로 크기(px)
  SizeY: number; // 세로 크기(px)
  LastStatus: string; // 전원상태 (ok | error)
  USE_YN: string; // 사용여부
}

// wb_display (시나리오)
export interface DisplayScenario {
  DisCode: number; // 시나리오코드 (PK, auto_increment)
  CD_DIST_OBSV: string; // 장비코드 (PK)
  SaveType: string; // 저장유형 ('local')
  DisEffect: string; // 표시효과 (1~8)
  DisSpeed: string; // 표시속도 (1~8)
  DisTime: number; // 표시유지시간 (1~20초)
  EndEffect: string; // 완료효과 (1~7)
  EndSpeed: string; // 완료속도 (1~8)
  StrTime: string; // 표시시작 시간
  EndTime: string; // 표시종료 시간
  Relay: string; // 릴레이 (0~15, 4비트 바이너리)
  ViewImg: string; // 미리보기 이미지 경로
  SendImg: string; // 전송 이미지/데이터
  HtmlData: string; // HTML 원본 데이터
  DisType: string; // 시나리오 타입 ('ad')
  Exp_YN: string; // 표출여부 ('Y' | 'N')
  RegDate: string; // 등록일시
}

// wb_displayment (지정문구)
export interface DisplayMent {
  disCode: number; // 문구코드(PK)
  Title: string; // 문구 제목
  HtmlData: string; // 문구 HTML 내용
}

// 표시효과 옵션
export const DIS_EFFECT_OPTIONS = [
  { value: '1', label: '즉시 표시' },
  { value: '2', label: '좌측으로 스크롤' },
  { value: '3', label: '위로 스크롤' },
  { value: '4', label: '아래로 스크롤' },
  { value: '5', label: '레이져 효과' },
  { value: '6', label: '중심에서 상하로 벌어짐' },
  { value: '7', label: '상하에서 중심으로 모여듬' },
  { value: '8', label: '1단으로 좌측 스크롤' },
] as const;

// 완료효과 옵션
export const END_EFFECT_OPTIONS = [
  { value: '1', label: '위로 스크롤' },
  { value: '2', label: '아래로 스크롤' },
  { value: '3', label: '위아래로 벌어짐' },
  { value: '4', label: '중심으로 모여듬' },
  { value: '5', label: '즉시 사라짐' },
  { value: '6', label: '화면반전' },
  { value: '7', label: '좌측으로 사라짐' },
] as const;

// 시나리오 저장 요청 페이로드
export interface SaveScenarioPayload {
  type: 'insert' | 'update';
  cdDistObsv: string;
  disCode?: number;
  viewType: 'image' | 'text' | 'video' | 'c16';
  disEffect: string;
  disSpeed: string;
  disTime: number;
  endEffect: string;
  endSpeed: string;
  strDate: string;
  endDate: string;
  relay: string;
  viewImg: string;
  sendImg: string;
  htmlData: string;
}
