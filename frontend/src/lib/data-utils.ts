import { SENSOR_CONFIG } from '@/types/data';
import type { SensorType, WaterUnit } from '@/types/data';

// DB 원시값(mm)을 센서 타입에 맞게 변환 & 포맷팅
//  * water → number_format($value / 1000, 2)
//  * snow  → number_format($value / 10, 1)
//  * rain  → number_format($value, 1)

export function formatValue(value: number | null | undefined, sensorType: SensorType, waterUnit?: WaterUnit): string {
  if (value === null || value === undefined) return '-';

  let divisor = SENSOR_CONFIG[sensorType].divisor;
  let decimal = SENSOR_CONFIG[sensorType].decimal;

  // 수위는 단위 전환 가능 (m / cm / mm)

  if (sensorType === 'water' && waterUnit) {
    switch (waterUnit) {
      case 'm':
        divisor = 1000;
        decimal = 2;
        break;
      case 'cm':
        divisor = 10;
        decimal = 1;
        break;
      case 'mm':
        divisor = 1;
        decimal = 0;
        break;
    }
  }

  return (value / divisor).toFixed(decimal);
}

// 센서 타입의 표시 단위 문자열 번환 (수위는 waterUnit에 따라 동적 변경)
export function getUnit(sensorType: SensorType, waterUnit?: WaterUnit) {
  if (sensorType === 'water' && waterUnit) return waterUnit;
  return SENSOR_CONFIG[sensorType].unit;
}

// 센서 타입 테마 색상 반환
export function getSensorColor(sensorType: SensorType): string {
  return SENSOR_CONFIG[sensorType].color;
}

// 센서 타입 한글 라벨 반환
export function getSensorLabel(sensorType: SensorType): string {
  return SENSOR_CONFIG[sensorType].label;
}

// 센서 '최대' 열 표시하는지 (침수 제외 모든 센서)
export function hasMaxColumn(sensorType: SensorType): boolean {
  return sensorType !== 'flood';
}

// 센서 '최소' 열 표시하는지 (수위만)
export function hasMinColumn(sensorType: SensorType): boolean {
  return sensorType === 'water';
}

// 센서 '합계' 열 표시하는지 (강우만)
export function hasSumColumn(sensorType: SensorType): boolean {
  return sensorType === 'rain';
}

// 날짜 문자열 포맷 유틸
// '2026-03-05' -> { year: '2026', month: '03', day: '05' }
export function parseDate(dateStr: string) {
  const [year, month, day] = dateStr.split('-');
  return { year, month, day };
}

// 오늘 날짜를 YYYY-MM-DD 형식으로 반환
export function getToday(): string {
  const d = new Date();
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${dd}`;
}

// 7일 전 날짜를 YYYY-MM-DD 형식으로 반환
// preiod 탭 기본 시작일로 사용
export function getWeekAgo(): string {
  const d = new Date();
  d.setDate(d.getDate() - 7);
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${dd}`;
}
