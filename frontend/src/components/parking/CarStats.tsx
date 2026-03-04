import { useState } from 'react';
import { Search } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import type { CarStatsRow } from '@/types/parking';

type StatsView = 'day' | 'month' | 'year';

interface CarStatsProps {
  dayStats: CarStatsRow[];
  monthStats: CarStatsRow[];
  yearStats: CarStatsRow[];
}

// 시간 헤더 (일별: 0~23, 월별: 1~31, 연별: 1~12)
const DAY_HEADERS = Array.from({ length: 24 }, (_, i) => String(i));
const MONTH_HEADERS = Array.from({ length: 31 }, (_, i) => String(i + 1));
const YEAR_HEADERS = Array.from({ length: 12 }, (_, i) => String(i + 1));

export function CarStats({ dayStats, monthStats, yearStats }: CarStatsProps) {
  const [view, setView] = useState<StatsView>('day');

  const now = new Date();
  const [year, setYear] = useState(now.getFullYear());
  const [month, setMonth] = useState(now.getMonth() + 1);
  const [day, setDay] = useState(now.getDate());

  const years = Array.from({ length: now.getFullYear() - 2020 + 1 }, (_, i) => 2021 + i);
  const months = Array.from({ length: 12 }, (_, i) => i + 1);
  const days = Array.from({ length: 31 }, (_, i) => i + 1);

  // 뷰별 헤더 + 데이터 매핑
  const viewConfig = {
    day: { headers: DAY_HEADERS, data: dayStats },
    month: { headers: MONTH_HEADERS, data: monthStats },
    year: { headers: YEAR_HEADERS, data: yearStats },
  };

  const { headers, data } = viewConfig[view];
  const emptyValues = headers.map(() => 0);

  const parkNames = [...new Set(data.map((r) => r.ParkGroupName))];

  const subTabs: { value: StatsView; label: string }[] = [
    { value: 'day', label: '# 일별' },
    { value: 'month', label: '# 월별' },
    { value: 'year', label: '# 연별' },
  ];

  return (
    <div className="space-y-4">
      {/* 서브 탭 */}
      <div className="flex gap-1">
        {subTabs.map((tab) => (
          <button
            key={tab.value}
            type="button"
            onClick={() => setView(tab.value)}
            className={cn(
              'rounded-md px-3 py-1.5 text-xs font-semibold transition-colors',
              view === tab.value
                ? 'bg-primary text-primary-foreground'
                : 'bg-muted text-muted-foreground hover:bg-muted/80'
            )}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {/* 검색 바 */}
      <div className="border-border flex flex-wrap items-center gap-3 rounded-lg border bg-white p-4 shadow-sm">
        <select
          value={year}
          onChange={(e) => setYear(Number(e.target.value))}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        >
          {years.map((y) => (
            <option key={y} value={y}>
              {y}
            </option>
          ))}
        </select>
        <span className="text-sm">년</span>

        {(view === 'day' || view === 'month') && (
          <>
            <select
              value={month}
              onChange={(e) => setMonth(Number(e.target.value))}
              className="border-border rounded-md border px-3 py-1.5 text-sm"
            >
              {months.map((m) => (
                <option key={m} value={m}>
                  {String(m).padStart(2, '0')}
                </option>
              ))}
            </select>
            <span className="text-sm">월</span>
          </>
        )}

        {view === 'day' && (
          <>
            <select
              value={day}
              onChange={(e) => setDay(Number(e.target.value))}
              className="border-border rounded-md border px-3 py-1.5 text-sm"
            >
              {days.map((d) => (
                <option key={d} value={d}>
                  {String(d).padStart(2, '0')}
                </option>
              ))}
            </select>
            <span className="text-sm">일</span>
          </>
        )}

        <Button
          size="sm"
          onClick={() => {
            /* TODO: API 검색 */
          }}
        >
          <Search className="mr-1.5 h-3.5 w-3.5" />
          검색
        </Button>
        <Button variant="outline" size="sm" onClick={() => alert('엑셀다운 (TODO: API 연동)')}>
          엑셀다운
        </Button>
      </div>

      {/* 통계 테이블 */}
      <div className="border-border overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table className={cn('w-full table-fixed text-xs', view === 'month' && 'min-w-[1100px]')}>
          <thead>
            <tr className="bg-muted/30">
              <th className="border-border w-24 border-r px-2 py-2 text-center font-semibold">주차장</th>
              <th className="border-border w-12 border-r px-2 py-2 text-center font-semibold">상태</th>
              {headers.map((h) => (
                <th key={h} className="border-border border-r px-1 py-2 text-center font-medium">
                  {h}
                </th>
              ))}
              <th
                className="border-border w-10 border-r px-2 py-2 text-center font-semibold"
                style={{
                  backgroundColor: '#FAE4D6',
                }}
              >
                최고
              </th>
              <th className="w-10 px-2 py-2 text-center font-semibold">계</th>
            </tr>
          </thead>
          <tbody>
            {parkNames.flatMap((name) => {
              const inRow = data.find((r) => r.ParkGroupName === name && r.direction === 'in');
              const outRow = data.find((r) => r.ParkGroupName === name && r.direction === 'out');
              return [
                <tr key={`${name}-in`} className="border-border border-t">
                  <td rowSpan={2} className="border-border border-r px-2 py-2 text-center font-medium">
                    {name}
                  </td>
                  <td className="border-border border-r px-2 py-2 text-center">입차</td>
                  {(inRow?.values ?? emptyValues).map((v, i) => (
                    <td key={i} className="border-border border-r px-1 py-2 text-center">
                      {v}
                    </td>
                  ))}
                  <td
                    className="border-border border-r px-2 py-2 text-center font-bold"
                    style={{ backgroundColor: '#FAE4D6' }}
                  >
                    {inRow?.max ?? 0}
                  </td>
                  <td className="px-2 py-2 text-center font-bold text-red-600">{inRow?.total ?? 0}</td>
                </tr>,
                <tr key={`${name}-out`} className="border-border border-t">
                  <td className="border-border border-r px-2 py-2 text-center">출차</td>
                  {(outRow?.values ?? emptyValues).map((v, i) => (
                    <td key={i} className="border-border border-r px-1 py-2 text-center">
                      {v}
                    </td>
                  ))}
                  <td
                    className="border-border border-r px-2 py-2 text-center font-bold"
                    style={{ backgroundColor: '#FAE4D6' }}
                  >
                    {outRow?.max ?? 0}
                  </td>
                  <td className="px-2 py-2 text-center font-bold text-red-600">{outRow?.total ?? 0}</td>
                </tr>,
              ];
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
