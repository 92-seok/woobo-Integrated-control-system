// 센서 타입
export type SensorType = 'rain' | 'water' | 'dplace' | 'soil' | 'snow' | 'tilt' | 'flood';

// 조회 방식(탭)
export type TimeViewType = 'time' | 'day' | 'month' | 'year' | 'period';

// 센서 메타 설정
export interface SensorConfig {
  code: string; // GB_OBSV 코드
  label: string; // 한글이름
  unit: string; // 기본 표시 단위
  color: string; // 테마색상
  decimal: number; // 소수점 자릿수
  divisor: number; // DB값(mm)
}

// 장비
export interface DataEquipment {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  GB_OBSV: string;
  SubOBCount?: number; // 변위 전용
  USE_YN: string;
}

// 시간별 검색 조건
export interface TimeSearchParams {
  sensorType: SensorType;
  area: string; // CD_DIST_OBSV
  date: string; // YYYY-MM-DD
  equip?: number; // 변위 채널 보호
}

// 시간별 데이터 행 : 60 x 24시간 그리드
export interface TimeDataRow {
  minute: number; // 0~59
  values: (number | null)[]; // 24개 (0 ~ 23시)
}

// 일별 검색 조건
export interface DaySearchParams {
  sensorType: SensorType;
  date: string; // YYYY-MM-DD
}

// 일별 데이터 행 : 장비별 x 24시간
export interface DayDataRow {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  subChannel?: number; // 변위 전용
  values: (number | null)[]; // 24개 (0 ~ 23시)
  dayMax?: number | null;
  dayMin?: number | null;
  daySum?: number | null;
}

// 월별 검색 조건 (Month.php 대응)
export interface MonthSearchParams {
  sensorType: SensorType;
  yearMonth: string; // YYYY-MM
}

// 월별 데이터 행: 관측소별 × 31일
export interface MonthDataRow {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  subChannel?: number;
  values: (number | null)[]; // 31개 (1~31일)
  dayMax?: number | null;
  dayMin?: number | null;
  daySum?: number | null;
}

// 연별 검색 조건
export interface YearSearchParams {
  sensorType: SensorType;
  year: string; // YYYY
}

// 연별 데이터 행: 관측소별 × 12월
export interface YearDataRow {
  CD_DIST_OBSV: string;
  NM_DIST_OBSV: string;
  subChannel?: number;
  values: (number | null)[]; // 12개 (1~12월)
}

// 기간별 검색 조건
export interface PeriodSearchParams {
  sensorType: SensorType;
  area: string; // CD_DIST_OBSV
  startDate: string; // YYYY-MM-DD
  endDate: string; // YYYY-MM-DD
  equip?: number; // 변위 채널 번호
}

// 기간별 데이터 행: 날짜별 × 24시간
export interface PeriodDataRow {
  regDate: string; // YYYY-MM-DD
  values: (number | null)[]; // 24개 (1~24시)
  dayMax?: number | null;
  dayMin?: number | null;
  daySum?: number | null;
}

// 침수 상태 행 (flood 전용 보조 행)
export interface FloodStatusRow {
  label: string; // '침수1', '침수2', '침수3'
  values: string[]; // 'O', 'X', '-'
}

// 센서 설정 상수 맵
export const SENSOR_CONFIG: Record<SensorType, SensorConfig> = {
  rain: { code: '01', label: '강우', unit: 'mm', color: '#738bd5', decimal: 1, divisor: 1 },
  water: { code: '02', label: '수위', unit: 'm', color: '#5fbaef', decimal: 2, divisor: 1000 },
  dplace: { code: '03', label: '변위', unit: 'mm', color: '#e49479', decimal: 1, divisor: 1 },
  soil: { code: '04', label: '함수비', unit: '%', color: '#e49479', decimal: 1, divisor: 1 },
  snow: { code: '06', label: '적설', unit: 'cm', color: '#8643ae', decimal: 1, divisor: 10 },
  tilt: { code: '08', label: '경사', unit: '°', color: '#e49479', decimal: 2, divisor: 1 },
  flood: { code: '21', label: '침수', unit: 'cm', color: '#ff8184', decimal: 1, divisor: 10 },
};

// 조회 방식(탭) 라벨
export const TIME_VIEW_CONFIG: Record<TimeViewType, { label: string }> = {
  time: { label: '시간별' },
  day: { label: '일별' },
  month: { label: '월별' },
  year: { label: '연별' },
  period: { label: '기간별' },
};

// 수위 단위 전환 옵션 (water 전용)
export const WATER_UNIT_OPTIONS = [
  { value: 'm', label: 'm', divisor: 1000, decimal: 2 },
  { value: 'cm', label: 'cm', divisor: 10, decimal: 1 },
  { value: 'mm', label: 'mm', divisor: 1, decimal: 0 },
] as const;

export type WaterUnit = (typeof WATER_UNIT_OPTIONS)[number]['value'];
