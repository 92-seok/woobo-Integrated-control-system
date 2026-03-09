// 차단기 장비 (GB_OBSV = 20)
export interface GateEquipment {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  DTL_ADRES: string;
  LastDate: Date;
  ConnIP: string;
  ConnPort: string;
  Gate: string;
  Light: string;
  Sound: string;
  Status: string;
}

// 차단기 제어
export interface GateSendDto {
  Devices: string[];
  Gate: 'open' | 'close';
  Status: 'start';
}

// 차단기 현재 상태
export interface GateStatus {
  CD_DIST_OBSV: string;
  Gate: 'open' | 'close';
  RegDate: string;
}
// 처단기 제어 이력
export interface GateControlHistory {
  GCtrCode: number;
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  Gate: 'open' | 'close';
  GStatus: 'start' | 'ing' | 'end' | 'error';
  RetData: string | null;
  RegDate: string;
}

// 차단기 상태 라벨, 색상
export const GATE_STATUS_CONFIG: Record<string, { label: string; color: string }> = {
  open: { label: '열림', color: 'text-emerald-600' },
  close: { label: '닫힘', color: 'text-red-500' },
  check: { label: '오류', color: 'text-amber-500' },
};

// 처리 상태 라벨, 색상
export const GSTATUS_CONFIG: Record<string, { label: string; color: string }> = {
  start: { label: '대기', color: 'text-gray-500' },
  ing: { label: '처리중', color: 'text-blue-500' },
  end: { label: '완료', color: 'text-emerald-600' },
  error: { label: '오류', color: 'text-red-500' },
};
