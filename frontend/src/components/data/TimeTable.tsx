import { useState, useMemo } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Search, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { formatValue, getUnit, getSensorColor, getToday } from '@/lib/data-utils';
import { generateTimeData } from '@/mocks/data-mock';
import { DataChart } from '@/components/data/DataChart';
import type { SensorType, DataEquipment, WaterUnit } from '@/types/data';

interface TimeTableProps {
  sensorType: SensorType;
  equipments: DataEquipment[];
  waterUnit?: WaterUnit;
}

export function TimeTable({ sensorType, equipments, waterUnit }: TimeTableProps) {
  const [selectedArea, setSelectedArea] = useState(equipments[0]?.CD_DIST_OBSV ?? '');
  const [date, setDate] = useState(getToday());
  const [searchDate, setSearchDate] = useState(date);
  const [tableOpen, setTableOpen] = useState(false);

  const selectedEquip = equipments.find((e) => e.CD_DIST_OBSV === selectedArea);
  const [channel, setChannel] = useState(1);

  const data = useMemo(() => generateTimeData(sensorType), [sensorType, searchDate, selectedArea, channel]);

  const themeColor = getSensorColor(sensorType);
  const unit = getUnit(sensorType, waterUnit);
  const hours = Array.from({ length: 24 }, (_, i) => i);

  // 차트 데이터: 시간별 합계 (PHP Time.php의 $chartData 대응)
  const chartDatasets = useMemo(() => {
    const hourlySum = Array.from({ length: 24 }, (_, h) => {
      let sum = 0;
      let count = 0;
      for (const row of data) {
        if (row.values[h] !== null) {
          sum += row.values[h]!;
          count++;
        }
      }
      if (count === 0) return null;
      // rain/flood: 합계, 나머지: 평균
      return sensorType === 'rain' || sensorType === 'flood' ? sum : sum / count;
    });

    const areaName = equipments.find((e) => e.CD_DIST_OBSV === selectedArea)?.NM_DIST_OBSV ?? '';
    return [{ label: areaName, data: hourlySum }];
  }, [data, selectedArea, equipments, sensorType]);

  const handleAreaChange = (code: string) => {
    setSelectedArea(code);
    setChannel(1);
  };

  return (
    <div className="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
      {/* 검색 조건 바 */}
      <div className="border-border flex flex-wrap items-center gap-3 border-b px-5 py-3">
        <span className="text-foreground text-sm font-semibold">{searchDate.replace(/-/g, '.')}</span>
        <span className="text-muted-foreground text-xs">(단위 : {unit})</span>

        <div className="ml-auto flex items-center gap-2">
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

      {/* 차트 영역 */}
      <div className="border-border border-b px-5 py-4">
        <DataChart sensorType={sensorType} labels={hours} datasets={chartDatasets} waterUnit={waterUnit} />
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
        <div className="max-h-[600px] overflow-auto">
          <Table className="table-fixed text-center">
            <colgroup>
              <col className="w-16" />
              {hours.map((h) => (
                <col key={h} className="w-14" />
              ))}
            </colgroup>

            <TableHeader>
              <TableRow>
                <TableHead
                  className="sticky top-0 z-10 text-center text-xs font-bold text-white"
                  style={{ backgroundColor: themeColor }}
                >
                  시간
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
              </TableRow>
            </TableHeader>

            <TableBody>
              {data.map((row) => (
                <TableRow key={row.minute} className="hover:bg-slate-50">
                  <TableCell className="bg-slate-50 text-xs font-bold">{row.minute}분</TableCell>
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
