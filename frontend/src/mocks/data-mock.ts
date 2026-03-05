import type {
  SensorType,
  DataEquipment,
  TimeDataRow,
  DayDataRow,
  MonthDataRow,
  YearDataRow,
  PeriodDataRow,
} from '@/types/data';
// 헬퍼 : 랜덤 값 배열
// null을 섞어서 실제 결축치가 있는 상황을 재현
function generateArray(length: number, max: number, nullRate = 0.1): (number | null)[] {
  return Array.from({ length }, () => (Math.random() < nullRate ? null : Math.round(Math.random() * max * 10) / 10));
}

// 센서별 장비 목록
// SELECT * FROM wb_equip WHERE GB_OBSV = '코드' AND USE_YN = '1'
export const MOCK_DATA_EQUIPMENTS: Record<SensorType, DataEquipment[]> = {
  rain: [
    { CD_DIST_OBSV: '0101', NM_DIST_OBSV: '강남관측소', GB_OBSV: '01', USE_YN: '1' },
    { CD_DIST_OBSV: '0102', NM_DIST_OBSV: '서초관측소', GB_OBSV: '01', USE_YN: '1' },
    { CD_DIST_OBSV: '0103', NM_DIST_OBSV: '송파관측소', GB_OBSV: '01', USE_YN: '1' },
    { CD_DIST_OBSV: '0104', NM_DIST_OBSV: '관악관측소', GB_OBSV: '01', USE_YN: '1' },
  ],
  water: [
    { CD_DIST_OBSV: '0201', NM_DIST_OBSV: '우보교', GB_OBSV: '02', USE_YN: '1' },
    { CD_DIST_OBSV: '0202', NM_DIST_OBSV: '갈마치교', GB_OBSV: '02', USE_YN: '1' },
    { CD_DIST_OBSV: '0203', NM_DIST_OBSV: '태평교', GB_OBSV: '02', USE_YN: '1' },
  ],
  dplace: [
    { CD_DIST_OBSV: '0301', NM_DIST_OBSV: '갈마치 1구간', GB_OBSV: '03', SubOBCount: 3, USE_YN: '1' },
    { CD_DIST_OBSV: '0302', NM_DIST_OBSV: '갈마치 2구간', GB_OBSV: '03', SubOBCount: 2, USE_YN: '1' },
  ],
  soil: [
    { CD_DIST_OBSV: '0401', NM_DIST_OBSV: '갈마치 1지구', GB_OBSV: '04', USE_YN: '1' },
    { CD_DIST_OBSV: '0402', NM_DIST_OBSV: '갈마치 2지구', GB_OBSV: '04', USE_YN: '1' },
  ],
  snow: [
    { CD_DIST_OBSV: '0601', NM_DIST_OBSV: '강남관측소', GB_OBSV: '06', USE_YN: '1' },
    { CD_DIST_OBSV: '0602', NM_DIST_OBSV: '서초관측소', GB_OBSV: '06', USE_YN: '1' },
  ],
  tilt: [
    { CD_DIST_OBSV: '0801', NM_DIST_OBSV: '갈마치 1구간', GB_OBSV: '08', USE_YN: '1' },
    { CD_DIST_OBSV: '0802', NM_DIST_OBSV: '갈마치 2구간', GB_OBSV: '08', USE_YN: '1' },
  ],
  flood: [
    { CD_DIST_OBSV: '2101', NM_DIST_OBSV: '강남역 지하도', GB_OBSV: '21', USE_YN: '1' },
    { CD_DIST_OBSV: '2102', NM_DIST_OBSV: '서초역 지하도', GB_OBSV: '21', USE_YN: '1' },
    { CD_DIST_OBSV: '2103', NM_DIST_OBSV: '잠실역 지하도', GB_OBSV: '21', USE_YN: '1' },
  ],
};

// 시간별 데이터 (60분 x 24시간 그리드, 장비 1개 기준)
// rain 기준 max : 5mm / 분
export function generateTimeData(sensorType: SensorType): TimeDataRow[] {
  const maxMap: Record<SensorType, number> = {
    rain: 5,
    water: 3000,
    dplace: 15,
    soil: 50,
    snow: 100,
    tilt: 5,
    flood: 200,
  };
  return Array.from({ length: 60 }, (_, minute) => ({
    minute,
    values: generateArray(24, maxMap[sensorType], 0.15),
  }));
}
// 일별 데이터
// 관측소별 × 24시간, 최대/최소/합계 포함
export function generateDayData(sensorType: SensorType): DayDataRow[] {
  const equips = MOCK_DATA_EQUIPMENTS[sensorType];
  const maxMap: Record<SensorType, number> = {
    rain: 20,
    water: 3500,
    dplace: 25,
    soil: 60,
    snow: 150,
    tilt: 8,
    flood: 300,
  };
  const rows: DayDataRow[] = [];

  for (const eq of equips) {
    // 변위는 서브채널별로 행 생성
    const channels = sensorType === 'dplace' && eq.SubOBCount ? eq.SubOBCount : 1;
    for (let ch = 1; ch <= channels; ch++) {
      const values = generateArray(24, maxMap[sensorType], 0.1);
      const nums = values.filter((v): v is number => v !== null);
      rows.push({
        CD_DIST_OBSV: eq.CD_DIST_OBSV,
        NM_DIST_OBSV: eq.NM_DIST_OBSV,
        subChannel: sensorType === 'dplace' ? ch : undefined,
        values,
        dayMax: nums.length > 0 ? Math.max(...nums) : null,
        dayMin: nums.length > 0 ? Math.min(...nums) : null,
        daySum:
          sensorType === 'rain' && nums.length > 0 ? Math.round(nums.reduce((a, b) => a + b, 0) * 10) / 10 : undefined,
      });
    }
  }
  return rows;
}

// 월별 데이터
// 관측소별 × 31일
export function generateMonthData(sensorType: SensorType): MonthDataRow[] {
  const equips = MOCK_DATA_EQUIPMENTS[sensorType];
  const maxMap: Record<SensorType, number> = {
    rain: 80,
    water: 4000,
    dplace: 30,
    soil: 70,
    snow: 300,
    tilt: 10,
    flood: 500,
  };
  const rows: MonthDataRow[] = [];

  for (const eq of equips) {
    const channels = sensorType === 'dplace' && eq.SubOBCount ? eq.SubOBCount : 1;
    for (let ch = 1; ch <= channels; ch++) {
      const values = generateArray(31, maxMap[sensorType], 0.12);
      const nums = values.filter((v): v is number => v !== null);
      rows.push({
        CD_DIST_OBSV: eq.CD_DIST_OBSV,
        NM_DIST_OBSV: eq.NM_DIST_OBSV,
        subChannel: sensorType === 'dplace' ? ch : undefined,
        values,
        dayMax: nums.length > 0 ? Math.max(...nums) : null,
        dayMin: nums.length > 0 ? Math.min(...nums) : null,
        daySum:
          sensorType === 'rain' && nums.length > 0 ? Math.round(nums.reduce((a, b) => a + b, 0) * 10) / 10 : undefined,
      });
    }
  }
  return rows;
}

// 연별 데이터
// 관측소별 × 12월
export function generateYearData(sensorType: SensorType): YearDataRow[] {
  const equips = MOCK_DATA_EQUIPMENTS[sensorType];
  const maxMap: Record<SensorType, number> = {
    rain: 300,
    water: 5000,
    dplace: 50,
    soil: 80,
    snow: 800,
    tilt: 15,
    flood: 1000,
  };
  const rows: YearDataRow[] = [];

  for (const eq of equips) {
    const channels = sensorType === 'dplace' && eq.SubOBCount ? eq.SubOBCount : 1;
    for (let ch = 1; ch <= channels; ch++) {
      rows.push({
        CD_DIST_OBSV: eq.CD_DIST_OBSV,
        NM_DIST_OBSV: eq.NM_DIST_OBSV,
        subChannel: sensorType === 'dplace' ? ch : undefined,
        values: generateArray(12, maxMap[sensorType], 0.08),
      });
    }
  }
  return rows;
}

// 기간별 데이터
// 최근 7일 × 24시간
export function generatePeriodData(sensorType: SensorType, days = 7): PeriodDataRow[] {
  const maxMap: Record<SensorType, number> = {
    rain: 20,
    water: 3500,
    dplace: 25,
    soil: 60,
    snow: 150,
    tilt: 8,
    flood: 300,
  };
  const rows: PeriodDataRow[] = [];
  const today = new Date();

  for (let d = days - 1; d >= 0; d--) {
    const date = new Date(today);
    date.setDate(date.getDate() - d);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');

    const values = generateArray(24, maxMap[sensorType], 0.1);
    const nums = values.filter((v): v is number => v !== null);
    rows.push({
      regDate: `${y}-${m}-${dd}`,
      values,
      dayMax: nums.length > 0 ? Math.max(...nums) : null,
      dayMin: nums.length > 0 ? Math.min(...nums) : null,
      daySum:
        sensorType === 'rain' && nums.length > 0 ? Math.round(nums.reduce((a, b) => a + b, 0) * 10) / 10 : undefined,
    });
  }
  return rows;
}
