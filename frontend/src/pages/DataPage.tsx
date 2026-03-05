import { useState, useMemo } from 'react';
import { Clock, CalendarDays, CalendarRange, CalendarClock, Calendar, Download, RefreshCw } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { SENSOR_CONFIG, TIME_VIEW_CONFIG } from '@/types/data';
import type { SensorType, TimeViewType, WaterUnit } from '@/types/data';
import { getSensorColor } from '@/lib/data-utils';
import { MOCK_DATA_EQUIPMENTS } from '@/mocks/data-mock';
import { TimeTable } from '@/components/data/TimeTable';
import { DayTable } from '@/components/data/DayTable';
import { MonthTable } from '@/components/data/MonthTable';
import { YearTable } from '@/components/data/YearTable';
import { PeriodTable } from '@/components/data/PeriodTable';

//  탭 아이콘 매핑
const tabIcons: Record<TimeViewType, typeof Clock> = {
  time: Clock,
  day: CalendarDays,
  month: CalendarRange,
  year: Calendar,
  period: CalendarClock,
};

//  센서 타입 목록 (PHP dataFrame.php의 menuKindArr 대응)
const sensorTypes = Object.entries(SENSOR_CONFIG) as [SensorType, (typeof SENSOR_CONFIG)[SensorType]][];

//  조회 방식 탭 목록
const timeViewTabs = Object.entries(TIME_VIEW_CONFIG) as [TimeViewType, { label: string }][];

export function DataPage() {
  const [sensorType, setSensorType] = useState<SensorType>('rain');
  const [activeTab, setActiveTab] = useState<TimeViewType>('day');
  const [waterUnit, setWaterUnit] = useState<WaterUnit>('m');

  // 현재 센서의 관측소 목록
  const equipments = useMemo(() => MOCK_DATA_EQUIPMENTS[sensorType] ?? [], [sensorType]);

  // 센서 변경 시 수위 단위 초기화
  const handleSensorChange = (type: SensorType) => {
    setSensorType(type);
    if (type === 'water') setWaterUnit('m');
  };

  const handleExcelDownload = () => {
    alert('엑셀 다운로드 기능은 API 연동 후 사용 가능합니다.');
  };

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/*  헤더  */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">데이터검색</h1>
          <p className="text-muted-foreground text-xs">센서 관측 데이터 조회 및 분석</p>
        </div>
        <div className="flex items-center gap-4">
          <div className="hidden items-center gap-2 sm:flex">
            <span
              className="h-2 w-2 rounded-full bg-emerald-500"
              style={{ animation: 'pulse-dot 2s ease-in-out infinite' }}
            />
            <span className="text-muted-foreground text-xs">시스템 정상</span>
          </div>
          <div className="bg-primary text-primary-foreground flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold shadow-sm">
            관
          </div>
        </div>
      </header>

      {/*  센서 타입 선택 바  */}
      <div className="border-border flex items-center gap-2 border-b bg-white px-6 py-2.5">
        <span className="text-muted-foreground mr-1 text-xs font-medium">센서</span>
        {sensorTypes.map(([type, config]) => (
          <button
            key={type}
            type="button"
            onClick={() => handleSensorChange(type)}
            className={cn(
              'rounded-md px-3 py-1.5 text-xs font-semibold transition-all duration-150',
              sensorType === type ? 'text-white shadow-sm' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'
            )}
            style={sensorType === type ? { backgroundColor: getSensorColor(type) } : undefined}
          >
            {config.label}
          </button>
        ))}

        {/* 수위 단위 전환 (water 선택 시만 표시) */}
        {sensorType === 'water' && (
          <div className="border-border ml-auto flex items-center gap-1 rounded-md border p-0.5">
            {(['m', 'cm', 'mm'] as WaterUnit[]).map((u) => (
              <button
                key={u}
                type="button"
                onClick={() => setWaterUnit(u)}
                className={cn(
                  'rounded px-2 py-1 text-[11px] font-semibold transition-all',
                  waterUnit === u ? 'bg-primary text-white' : 'text-muted-foreground hover:bg-slate-100'
                )}
              >
                {u}
              </button>
            ))}
          </div>
        )}
      </div>

      {/*  탭 네비게이션  */}
      <div className="border-border border-b bg-white px-6">
        <nav className="flex">
          {timeViewTabs.map(([value, config]) => {
            const Icon = tabIcons[value];
            const isActive = activeTab === value;
            return (
              <button
                key={value}
                type="button"
                onClick={() => setActiveTab(value)}
                className={cn(
                  'relative flex items-center gap-2 px-4 py-3.5 text-sm font-medium transition-colors duration-150',
                  isActive ? 'text-primary' : 'text-muted-foreground hover:text-foreground'
                )}
              >
                <Icon className="h-4 w-4" />
                {config.label}
                {isActive && <span className="bg-primary absolute right-0 bottom-0 left-0 h-[2px] rounded-t-full" />}
              </button>
            );
          })}
        </nav>
      </div>

      {/*  콘텐츠 영역  */}
      <div className="bg-grid flex-1 overflow-y-auto p-6">
        <div className="animate-fade-in-up flex flex-col gap-5">
          {activeTab === 'time' && <TimeTable sensorType={sensorType} equipments={equipments} waterUnit={waterUnit} />}
          {activeTab === 'day' && <DayTable sensorType={sensorType} waterUnit={waterUnit} />}
          {activeTab === 'month' && <MonthTable sensorType={sensorType} waterUnit={waterUnit} />}
          {activeTab === 'year' && <YearTable sensorType={sensorType} waterUnit={waterUnit} />}
          {activeTab === 'period' && (
            <PeriodTable sensorType={sensorType} equipments={equipments} waterUnit={waterUnit} />
          )}

          {/* 하단 액션 버튼 */}
          <div className="mx-auto flex items-center justify-center gap-3">
            <Button
              size="sm"
              className="text-white hover:opacity-90"
              style={{ backgroundColor: getSensorColor(sensorType) }}
              onClick={handleExcelDownload}
            >
              <Download className="h-3.5 w-3.5" />
              엑셀다운
            </Button>
            <Button
              size="sm"
              className="bg-slate-500 text-white hover:bg-slate-600"
              onClick={() => window.location.reload()}
            >
              <RefreshCw className="h-3.5 w-3.5" />
              새로고침
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default DataPage;
