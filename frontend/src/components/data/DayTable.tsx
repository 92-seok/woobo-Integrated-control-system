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
  hasMaxColumn,
  hasMinColumn,
  hasSumColumn,
} from '@/lib/data-utils';
import { generateDayData } from '@/mocks/data-mock';
import { DataChart } from '@/components/data/DataChart';
import type { SensorType, WaterUnit, DayDataRow } from '@/types/data';

interface DayTableProps {
  sensorType: SensorType;
  waterUnit?: WaterUnit;
}

export function DayTable({ sensorType, waterUnit }: DayTableProps) {
  const [date, setDate] = useState(getToday());
  const [searchDate, setSearchDate] = useState(date);
  const [tableOpen, setTableOpen] = useState(false);

  const data = useMemo(() => generateDayData(sensorType), [sensorType, searchDate]);

  const themeColor = getSensorColor(sensorType);
  const unit = getUnit(sensorType, waterUnit);
  const hours = Array.from({ length: 24 }, (_, i) => i + 1);

  // ── rowSpan 그룹핑 ──
  const rowSpanMap = useMemo(() => {
    const map = new Map<string, number>();
    for (const row of data) {
      map.set(row.CD_DIST_OBSV, (map.get(row.CD_DIST_OBSV) ?? 0) + 1);
    }
    return map;
  }, [data]);

  const isFirstOfGroup = (row: DayDataRow, idx: number): boolean => {
    if (idx === 0) return true;
    return data[idx - 1].CD_DIST_OBSV !== row.CD_DIST_OBSV;
  };

  const showMax = hasMaxColumn(sensorType);
  const showMin = hasMinColumn(sensorType);
  const showSum = hasSumColumn(sensorType);

  // ── 차트 데이터: 관측소별 dataset (PHP Day.php $graphData 대응) ──
  const chartDatasets = useMemo(() => {
    return data.map((row) => ({
      label: row.subChannel ? `${row.NM_DIST_OBSV}_${row.subChannel}` : row.NM_DIST_OBSV,
      data: row.values,
    }));
  }, [data]);

  return (
    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      {/* ── 검색 조건 바 ── */}
      <div className="border-border flex flex-wrap items-center gap-3 border-b px-5 py-3">
        <span className="text-foreground text-sm font-semibold">{searchDate.replace(/-/g, '.')}</span>
        <span className="text-muted-foreground text-xs">(단위 : {unit})</span>

        <div className="ml-auto flex items-center gap-2">
          <input
            type="date"
            value={date}
            onChange={(e) => setDate(e.target.value)}
            className="border-border rounded-md border px-2.5 py-1.5 text-xs"
          />
          <Button size="sm" onClick={() => setSearchDate(date)}>
            <Search className="h-3.5 w-3.5" />
            검색
          </Button>
        </div>
      </div>

      {/* ── 차트 영역 ── */}
      <div className="border-border border-b px-5 py-4">
        <DataChart sensorType={sensorType} labels={hours} datasets={chartDatasets} waterUnit={waterUnit} />
      </div>

      {/* ── 테이블 접기/펼치기 토글 ── */}
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

      {/* ── 데이터 테이블 ── */}
      {tableOpen && (
        <div className="max-h-[600px] overflow-auto">
          <Table className="table-fixed text-center">
            <colgroup>
              <col className="w-28" />
              {sensorType === 'dplace' && <col className="w-14" />}
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

                  {showMax && (
                    <TableCell className="bg-orange-50 text-xs font-bold">
                      {formatValue(row.dayMax ?? null, sensorType, waterUnit)}
                    </TableCell>
                  )}
                  {showMin && (
                    <TableCell className="bg-blue-50 text-xs font-bold">
                      {formatValue(row.dayMin ?? null, sensorType, waterUnit)}
                    </TableCell>
                  )}
                  {showSum && (
                    <TableCell className="text-xs font-bold text-red-700">
                      {formatValue(row.daySum ?? null, sensorType, waterUnit)}
                    </TableCell>
                  )}
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}
