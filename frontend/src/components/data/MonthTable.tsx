import { useState, useMemo } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { formatValue, getUnit, getSensorColor, hasMaxColumn, hasMinColumn, hasSumColumn } from '@/lib/data-utils';
import { generateMonthData } from '@/mocks/data-mock';
import { DataChart } from '@/components/data/DataChart';
import type { SensorType, WaterUnit, MonthDataRow } from '@/types/data';

interface MonthTableProps {
  sensorType: SensorType;
  waterUnit?: WaterUnit;
}

export function MonthTable({ sensorType, waterUnit }: MonthTableProps) {
  // 현재 년-월 기본값
  const now = new Date();
  const defaultYM = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

  const [yearMonth, setYearMonth] = useState(defaultYM);
  const [searchYM, setSearchYM] = useState(yearMonth);
  const [tableOpen, setTableOpen] = useState(true);

  // eslint-disable-next-line react-hooks/exhaustive-deps -- API 연동 시 searchYM 필요
  const data = useMemo(() => generateMonthData(sensorType), [sensorType, searchYM]);

  const themeColor = getSensorColor(sensorType);
  const unit = getUnit(sensorType, waterUnit);
  const days = Array.from({ length: 31 }, (_, i) => i + 1);

  // rowSpan 그룹핑 (변위 채널 병합용)
  const rowSpanMap = useMemo(() => {
    const map = new Map<string, number>();
    for (const row of data) {
      map.set(row.CD_DIST_OBSV, (map.get(row.CD_DIST_OBSV) ?? 0) + 1);
    }
    return map;
  }, [data]);

  const isFirstOfGroup = (row: MonthDataRow, idx: number): boolean => {
    if (idx === 0) return true;
    return data[idx - 1].CD_DIST_OBSV !== row.CD_DIST_OBSV;
  };

  const showMax = hasMaxColumn(sensorType);
  const showMin = hasMinColumn(sensorType);
  const showSum = hasSumColumn(sensorType);

  // 표시용 년월 포맷
  const [dispYear, dispMonth] = searchYM.split('-');

  // 차트 데이터: 장비별 dataset
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
        <span className="text-foreground text-sm font-semibold">
          {dispYear}년 {dispMonth}월
        </span>
        <span className="text-muted-foreground text-xs">(단위 : {unit})</span>

        <div className="ml-auto flex items-center gap-2">
          <input
            type="month"
            value={yearMonth}
            onChange={(e) => setYearMonth(e.target.value)}
            className="border-border rounded-md border px-2.5 py-1.5 text-xs"
          />
          <Button size="sm" onClick={() => setSearchYM(yearMonth)}>
            <Search className="h-3.5 w-3.5" />
            검색
          </Button>
        </div>
      </div>

      {/* 차트 영역 */}
      <div className="border-border border-b px-5 py-4">
        <DataChart sensorType={sensorType} labels={days} datasets={chartDatasets} waterUnit={waterUnit} />
      </div>

      {/* 테이블 접기/펼치기 토글 */}
      <button
        type="button"
        onClick={() => setTableOpen((prev) => !prev)}
        className="border-border flex w-full items-center justify-between border-b px-5 py-2.5 transition-colors hover:bg-slate-50"
      >
        <span className="text-foreground text-sm font-semibold">데이터 보기</span>
        <ChevronDown
          className={cn(`text-muted-foreground h-4 w-4 transition-transform duration-200`, tableOpen && 'rotate-180')}
        />
      </button>

      {/*  데이터 테이블  */}
      {tableOpen && (
        <div className="max-h-150 overflow-auto">
          <Table className="table-fixed text-center">
            <colgroup>
              <col className="w-28" />
              {sensorType === 'dplace' && <col className="w-14" />}
              {days.map((d) => (
                <col key={d} className="w-12" />
              ))}
              {showMax && <col className="w-14" />}
              {showMin && <col className="w-14" />}
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
                {days.map((d) => (
                  <TableHead
                    key={d}
                    className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                    style={{ backgroundColor: themeColor }}
                  >
                    {d}
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
                      className="border-r border-slate-200 bg-slate-50 p-0 px-2 py-1 text-xs font-bold"
                    >
                      {row.NM_DIST_OBSV}
                    </TableCell>
                  )}

                  {sensorType === 'dplace' && (
                    <TableCell className="bg-slate-50 p-0 px-2 py-1 text-xs font-bold">{row.subChannel}</TableCell>
                  )}

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
              ))}
            </TableBody>
          </Table>
        </div>
      )}
    </div>
  );
}
