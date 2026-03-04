// ── 수위 (GB_OBSV='02') ──
export interface WaterLevelData {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  current: number;
}

// ── 강우 (GB_OBSV='01') ──
export interface RainfallData {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  yyesterday: number | null;
  yesterday: number | null;
  today: number | null;
}

// ── 변위 (GB_OBSV='03') ──
export interface DisplacementData {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  SUB_OBSV: string;
  current: number;
  accumulated: number;
}

// ── 함수비 (GB_OBSV='04') ──
export interface SoilMoistureData {
  NM_DIST_OBSV: string;
  current: number;
}

// ── 경사 (GB_OBSV='08') ──
export interface TiltData {
  NM_DIST_OBSV: string;
  current: number;
}

// ── 적설 (GB_OBSV='06') ──
export interface SnowData {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  current: number;
  accumulated: number;
}

// ── 침수 (GB_OBSV='21') ──
export interface FloodData {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  status: string;
  current: number;
}

// ── 경보현황 ──
export interface AlertStatus {
  GCode: number;
  GName: string;
  IsuKind: string;
  IsuSrtDate: string | null;
  IsuEndDate: string | null;
  Occur: string;
  IStatus: string;
}
