// ─── 주차장 그룹 (wb_parkgategroup 테이블) ───
export interface ParkingGroup {
  ParkGroupCode: number;
  ParkGroupName: string;
  ParkGroupAddr: string;
  ParkJoinGate: string; // 쉼표 구분 차단기 코드 목록
}

// ─── LPR 장비 (wb_equip WHERE GB_OBSV='LP') ───
export interface ParkingGate {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  DTL_ADRES: string;
}

// ─── 차량 입출차 내역 (hns_lprdata / wb_parkcarhist) ───
export interface CarRecord {
  idx: number;
  CarNumber: string;
  EventDateTime: string; // '2026-03-03 08:30:00'
  DeviceCode: '01' | '02'; // 01=입차, 02=출차
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  ParkGroupName: string;
}

// ─── 입출차 통계 행 ───
export interface CarStatsRow {
  ParkGroupName: string;
  direction: 'in' | 'out';
  values: number[]; // 시간별(24) or 일별(31) or 월별(12)
  max: number;
  total: number;
}

// ─── PM2 서비스 상태 ───
export interface ServiceProcess {
  name: string;
  status: 'online' | 'stopped' | 'errored';
  cpu: number;
  memory: number;
  restarts: number;
  uptime: number; // ms
}

// ─── 상수 ───
export const DEVICE_CODE_LABELS: Record<string, string> = {
  '01': '입차',
  '02': '출차',
};

export const CAR_TYPE_OPTIONS = [
  { value: '0', label: '입차' },
  { value: '1', label: '출차' },
  { value: '3', label: '입/출차' },
  { value: '2', label: '현재 주차' },
] as const;

export const SERVICE_STATUS_CONFIG: Record<string, { label: string; color: string }> = {
  online: { label: '실행 중', color: 'text-emerald-600' },
  stopped: { label: '중지됨', color: 'text-red-500' },
  errored: { label: '오류', color: 'text-amber-500' },
};
