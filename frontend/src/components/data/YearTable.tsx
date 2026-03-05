import { useState, useMemo } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { formatValue, getUnit, getSensorColor } from '@/lib/data-utils';
import { generateYearData } from '@/mocks/data-mock';
import { DataChart } from '@/components/data/DataChart';
import type { SensorType, WaterUnit, YearDataRow } from '@/types/data';

interface YearTableProps {
  sensorType: SensorType;
  waterUnit?: WaterUnit;
}

export function YearTable({ sensorType, waterUnit }: YearTableProps) {
  const currentYear = new Date().getFullYear();
  const [year, setYear] = useState(currentYear);
  const [searchYear, setSearchYear] = useState(year);
  const [tableOpen, setTableOpen] = useState(false);

  const data = useMemo(() => generateYearData(sensorType), [sensorType, searchYear]);

  const themeColor = getSensorColor(sensorType);
  const unit = getUnit(sensorType, waterUnit);
  const months = Array.from({ length: 12 }, (_, i) => i + 1);

  // rowSpan 그룹핑 (변위 채널 병합용)
  const rowSpanMap = useMemo(() => {
    const map = new Map<string, number>();
    for (const row of data) {
      map.set(row.CD_DIST_OBSV, (map.get(row.CD_DIST_OBSV) ?? 0) + 1);
    }
    return map;
  }, [data]);

  const isFirstOfGroup = (row: YearDataRow, idx: number): boolean => {
    if (idx === 0) return true;
    return data[idx - 1].CD_DIST_OBSV !== row.CD_DIST_OBSV;
  };

  // 차트 데이터
  const chartLabels = months.map((m) => `${m}월`);
  const chartDatasets = useMemo(() => {
    return data.map((row) => ({
      label: row.subChannel ? `${row.NM_DIST_OBSV}_${row.subChannel}` : row.NM_DIST_OBSV,
      data: row.values,
    }));
  }, [data]);

  return (
    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      {/*  검색 조건 바  */}
      <div className="border-border flex flex-wrap items-center gap-3 border-b px-5 py-3">
        <span className="text-foreground text-sm font-semibold">{searchYear}년</span>
        <span className="text-muted-foreground text-xs">(단위 : {unit})</span>

        <div className="ml-auto flex items-center gap-2">
          <input
            type="number"
            value={year}
            min={2000}
            max={2099}
            onChange={(e) => setYear(Number(e.target.value))}
            className="border-border w-24 rounded-md border px-2.5 py-1.5 text-center text-xs"
          />
          <Button size="sm" onClick={() => setSearchYear(year)}>
            <Search className="h-3.5 w-3.5" />
            검색
          </Button>
        </div>
      </div>

      {/* 차트 영역 */}
      <div className="border-border border-b px-5 py-4">
        <DataChart sensorType={sensorType} labels={chartLabels} datasets={chartDatasets} waterUnit={waterUnit} />
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

      {/*  데이터 테이블  */}
      {tableOpen && (
        <div className="max-h-[600px] overflow-auto">
          <Table className="table-fixed text-center">
            <colgroup>
              <col className="w-28" />
              {sensorType === 'dplace' && <col className="w-14" />}
              {months.map((m) => (
                <col key={m} className="w-16" />
              ))}
            </colgroup>

            <TableHeader>
              <TableRow>
                <TableHead
                  className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                  style={{ backgroundColor: themeColor }}
                >
                  지역명
                </TableHead>
                {sensorType === 'dplace' && (
                  <TableHead
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    채널
                  </TableHead>
                )}
                {months.map((m) => (
                  <TableHead
                    key={m}
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    {m}월
                  </TableHead>
                ))}
              </TableRow>
            </TableHeader>

            <TableBody>
              {data.map((row, idx) => (
                <TableRow key={`${row.CD_DIST_OBSV}-${row.subChannel ?? 0}`} className="hover:bg-slate-50">
                  {isFirstOfGroup(row, idx) && (
                    <TableCell
                      rowSpan={rowSpanMap.get(row.CD_DIST_OBSV)}
                      className="border-r border-slate-200 bg-slate-50 text-xs font-bold"
                    >
                      {row.NM_DIST_OBSV}
                    </TableCell>
                  )}

                  {sensorType === 'dplace' && (
                    <TableCell className="bg-slate-50 text-xs font-bold">{row.subChannel}</TableCell>
                  )}

                  {row.values.map((val, colIdx) => (
                    <TableCell key={colIdx} className="text-xs">
                      {val !== null ? (
                        <span style={{ color: '#4900FF' }}>{formatValue(val, sensorType, waterUnit)}</span>
                      ) : (
                        '-'
                      )}
                    </TableCell>
                  ))}
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}
