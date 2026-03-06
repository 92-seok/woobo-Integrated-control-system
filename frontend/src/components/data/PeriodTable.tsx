import { useState, useMemo } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import {
  formatValue,
  getUnit,
  getSensorColor,
  getToday,
  getWeekAgo,
  hasMaxColumn,
  hasMinColumn,
  hasSumColumn,
} from '@/lib/data-utils';
import { generatePeriodData } from '@/mocks/data-mock';
import { DataChart } from '@/components/data/DataChart';
import type { SensorType, DataEquipment, WaterUnit } from '@/types/data';

const MAX_CHART_DAYS = 14;

interface PeriodTableProps {
  sensorType: SensorType;
  equipments: DataEquipment[];
  waterUnit?: WaterUnit;
}

export function PeriodTable({ sensorType, equipments, waterUnit }: PeriodTableProps) {
  const [selectedArea, setSelectedArea] = useState(equipments[0]?.CD_DIST_OBSV ?? '');
  const [startDate, setStartDate] = useState(getWeekAgo());
  const [endDate, setEndDate] = useState(getToday());

  // 검색 실행 시점의 조건 저장
  const [searchParams, setSearchParams] = useState({ startDate, endDate, selectedArea });
  const [tableOpen, setTableOpen] = useState(true);

  // 변위 채널 선택
  const selectedEquip = equipments.find((e) => e.CD_DIST_OBSV === selectedArea);
  const [channel, setChannel] = useState(1);

  // 기간 일수 계산 → mock 데이터 생성
  const dayCount = useMemo(() => {
    const s = new Date(searchParams.startDate);
    const e = new Date(searchParams.endDate);
    const diff = Math.ceil((e.getTime() - s.getTime()) / (1000 * 60 * 60 * 24)) + 1;
    return Math.max(1, Math.min(diff, 60)); // 최대 60일 제한
  }, [searchParams]);

  // eslint-disable-next-line react-hooks/exhaustive-deps -- API 연동 시 searchParams 필요
  const data = useMemo(() => generatePeriodData(sensorType, dayCount), [sensorType, dayCount, searchParams]);

  const themeColor = getSensorColor(sensorType);
  const unit = getUnit(sensorType, waterUnit);
  const hours = Array.from({ length: 24 }, (_, i) => i + 1);

  const showMax = hasMaxColumn(sensorType);
  const showMin = hasMinColumn(sensorType);
  const showSum = hasSumColumn(sensorType);

  const handleAreaChange = (code: string) => {
    setSelectedArea(code);
    setChannel(1);
    setSearchParams((prev) => ({ ...prev, selectedArea: code }));
  };

  const handleSearch = () => {
    setSearchParams({ startDate, endDate, selectedArea });
  };

  // 차트 데이터: 날짜별 dataset (x축: 1~24식간)
  const chartDatasets = useMemo(() => {
    return data.map((row) => {
      const [, m, d] = row.regDate.split('-');
      return {
        label: `${m}/${d}`,
        data: row.values,
      };
    });
  }, [data]);

  const showChart = dayCount <= MAX_CHART_DAYS;

  return (
    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      {/* 검색 조건 바 */}
      <div className="border-border flex flex-wrap items-center gap-3 border-b px-5 py-3">
        <span className="text-muted-foreground text-xs">(단위 : {unit})</span>

        <div className="ml-auto flex flex-wrap items-center gap-2">
          {/* 관측소 선택 */}
          <select
            value={selectedArea}
            onChange={(e) => handleAreaChange(e.target.value)}
            className="border-border rounded-md border px-2.5 py-1.5 text-xs"
          >
            {equipments.map((eq) => (
              <option key={eq.CD_DIST_OBSV} value={eq.CD_DIST_OBSV}>
                {eq.NM_DIST_OBSV}
              </option>
            ))}
          </select>

          {/* 변위 채널 선택 */}
          {sensorType === 'dplace' && selectedEquip?.SubOBCount && (
            <select
              value={channel}
              onChange={(e) => setChannel(Number(e.target.value))}
              className="border-border rounded-md border px-2.5 py-1.5 text-xs"
            >
              {Array.from({ length: selectedEquip.SubOBCount }, (_, i) => i + 1).map((ch) => (
                <option key={ch} value={ch}>
                  {ch} 채널
                </option>
              ))}
            </select>
          )}

          {/* 시작일 */}
          <input
            type="date"
            value={startDate}
            max={endDate}
            onChange={(e) => setStartDate(e.target.value)}
            className="border-border rounded-md border px-2.5 py-1.5 text-xs"
          />
          <span className="text-muted-foreground text-xs">~</span>
          {/* 종료일 */}
          <input
            type="date"
            value={endDate}
            min={startDate}
            onChange={(e) => setEndDate(e.target.value)}
            className="border-border rounded-md border px-2.5 py-1.5 text-xs"
          />

          <Button size="sm" onClick={handleSearch}>
            <Search className="h-3.5 w-3.5" />
            검색
          </Button>
        </div>
      </div>

      {/* 차트 영역 */}
      <div className="border-border border-b px-5 py-4">
        {showChart ? (
          <DataChart sensorType={sensorType} labels={hours} datasets={chartDatasets} waterUnit={waterUnit} />
        ) : (
          <div className="flex h-25 items-center justify-center">
            <span className="text-muted-foreground text-sm">
              {MAX_CHART_DAYS}일 이내의 기간만 차트로 표시됩니다. (현재 {dayCount}일)
            </span>
          </div>
        )}
      </div>

      {/* 테이블 접기/펼치기 토글 */}
      <button
        type="button"
        onClick={() => setTableOpen((prev) => !prev)}
        className="border-border flex w-full items-center justify-between border-b px-5 py-2.5 transition-colors hover:bg-slate-50"
      >
        <span className="text-foreground text-sm font-semibold">데이터 보기</span>
        <ChevronDown
          className={cn('text-muted-foreground h-4 w-4 transition-transform duration-200', tableOpen && 'rotate-180')}
        />
      </button>

      {/* 데이터 테이블 */}
      {tableOpen && (
        <div className="max-h-150 overflow-auto">
          <Table className="table-fixed text-center">
            <colgroup>
              <col className="w-28" />
              {hours.map((h) => (
                <col key={h} className="w-14" />
              ))}
              {showMax && <col className="w-16" />}
              {showMin && <col className="w-16" />}
              {showSum && <col className="w-16" />}
            </colgroup>

            <TableHeader>
              <TableRow>
                <TableHead
                  className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                  style={{ backgroundColor: themeColor }}
                >
                  날짜
                </TableHead>
                {hours.map((h) => (
                  <TableHead
                    key={h}
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    {h}
                  </TableHead>
                ))}
                {showMax && (
                  <TableHead
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    {sensorType === 'water' ? '최대' : '최고'}
                  </TableHead>
                )}
                {showMin && (
                  <TableHead
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    최소
                  </TableHead>
                )}
                {showSum && (
                  <TableHead
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    계
                  </TableHead>
                )}
              </TableRow>
            </TableHeader>

            <TableBody>
              {data.map((row) => {
                // '2026-03-05' → '2026년 03월 05일'
                const [y, m, d] = row.regDate.split('-');
                return (
                  <TableRow key={row.regDate} className="hover:bg-slate-50">
                    <TableCell className="bg-slate-50 p-0 px-2 py-1 text-xs font-bold whitespace-nowrap">
                      {y}년 {m}월 {d}일
                    </TableCell>

                    {row.values.map((val, colIdx) => (
                      <TableCell key={colIdx} className="p-0 px-2 py-1 text-xs">
                        {val !== null ? (
                          <span style={{ color: '#4900FF' }}>{formatValue(val, sensorType, waterUnit)}</span>
                        ) : (
                          '-'
                        )}
                      </TableCell>
                    ))}

                    {showMax && (
                      <TableCell className="bg-orange-50 p-0 px-2 py-1 text-xs font-bold">
                        {formatValue(row.dayMax ?? null, sensorType, waterUnit)}
                      </TableCell>
                    )}
                    {showMin && (
                      <TableCell className="bg-blue-50 p-0 px-2 py-1 text-xs font-bold">
                        {formatValue(row.dayMin ?? null, sensorType, waterUnit)}
                      </TableCell>
                    )}
                    {showSum && (
                      <TableCell className="p-0 px-2 py-1 text-xs font-bold text-red-700">
                        {formatValue(row.daySum ?? null, sensorType, waterUnit)}
                      </TableCell>
                    )}
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}
