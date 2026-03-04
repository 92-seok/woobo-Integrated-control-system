import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type {
  WaterLevelData,
  RainfallData,
  DisplacementData,
  SoilMoistureData,
  TiltData,
  SnowData,
  FloodData,
} from '@/types/report';

/** 날짜 포맷 헬퍼 (mm월 dd일) */
function formatDate(daysAgo: number): string {
  const d = new Date();
  d.setDate(d.getDate() - daysAgo);
  return `${String(d.getMonth() + 1).padStart(2, '0')}월 ${String(d.getDate()).padStart(2, '0')}일`;
}

/** 섹션 헤더 */
function SectionTitle({ title, unit }: { title: string; unit: string }) {
  return (
    <div className="flex items-center justify-between rounded-t-lg bg-slate-100 px-4 py-2.5">
      <span className="text-sm font-semibold text-slate-700">◈ {title}</span>
      <span className="text-xs text-slate-500">단위 : {unit}</span>
    </div>
  );
}

// ── 수위 테이블 ──
function WaterLevelTable({ data }: { data: WaterLevelData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="수위" unit="M" />
      <Table>
        <TableHeader>
          <TableRow className="bg-sky-400/90 hover:bg-sky-400/90">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">현재</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => (
            <TableRow key={row.CD_DIST_OBSV} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(3)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 강우 테이블 ──
function RainfallTable({ data }: { data: RainfallData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="강우" unit="mm" />
      <Table>
        <TableHeader>
          <TableRow className="bg-indigo-400/90 hover:bg-indigo-400/90">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">{formatDate(2)}</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">{formatDate(1)}</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">{formatDate(0)}</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">합계</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => {
            const yy = row.yyesterday ?? 0;
            const y = row.yesterday ?? 0;
            const t = row.today ?? 0;
            return (
              <TableRow key={row.CD_DIST_OBSV} className="hover:bg-muted/20">
                <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
                <TableCell className="text-center text-xs">
                  {row.yyesterday != null ? row.yyesterday.toFixed(1) : '-'}
                </TableCell>
                <TableCell className="text-center text-xs">
                  {row.yesterday != null ? row.yesterday.toFixed(1) : '-'}
                </TableCell>
                <TableCell className="text-center text-xs">{row.today != null ? row.today.toFixed(1) : '-'}</TableCell>
                <TableCell className="text-center text-xs font-medium">{(yy + y + t).toFixed(1)}</TableCell>
              </TableRow>
            );
          })}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 변위 테이블 ──
function DisplacementTable({ data }: { data: DisplacementData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="변위" unit="mm" />
      <Table>
        <TableHeader>
          <TableRow className="bg-orange-400/80 hover:bg-orange-400/80">
            <TableHead className="w-[30%] text-center text-xs font-semibold text-white" colSpan={2}>
              지역명
            </TableHead>
            <TableHead className="w-[30%] text-center text-xs font-semibold text-white">현재</TableHead>
            <TableHead className="w-[30%] text-center text-xs font-semibold text-white">누적</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row, i) => (
            <TableRow key={`${row.CD_DIST_OBSV}-${row.SUB_OBSV}-${i}`} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="w-[10%] text-center text-xs">{row.SUB_OBSV}</TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(1)}</TableCell>
              <TableCell className="text-center text-xs">{row.accumulated.toFixed(1)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 함수비 테이블 ──
function SoilMoistureTable({ data }: { data: SoilMoistureData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="함수비율" unit="%" />
      <Table>
        <TableHeader>
          <TableRow className="bg-orange-400/80 hover:bg-orange-400/80">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">현재</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => (
            <TableRow key={row.NM_DIST_OBSV} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(1)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 경사 테이블 ──
function TiltTable({ data }: { data: TiltData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="경사" unit="°" />
      <Table>
        <TableHeader>
          <TableRow className="bg-orange-400/80 hover:bg-orange-400/80">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">현재</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => (
            <TableRow key={row.NM_DIST_OBSV} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(1)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 적설 테이블 ──
function SnowTable({ data }: { data: SnowData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="적설" unit="Cm" />
      <Table>
        <TableHeader>
          <TableRow className="bg-teal-500/80 hover:bg-teal-500/80">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">현재</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">누적</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => (
            <TableRow key={row.CD_DIST_OBSV} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(1)}</TableCell>
              <TableCell className="text-center text-xs">{row.accumulated.toFixed(1)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 침수 테이블 ──
function FloodTable({ data }: { data: FloodData[] }) {
  return (
    <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
      <SectionTitle title="침수" unit="Cm" />
      <Table>
        <TableHeader>
          <TableRow className="bg-rose-400/80 hover:bg-rose-400/80">
            <TableHead className="text-center text-xs font-semibold text-white">지역명</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">감지상태</TableHead>
            <TableHead className="text-center text-xs font-semibold text-white">현재</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.map((row) => (
            <TableRow key={row.CD_DIST_OBSV} className="hover:bg-muted/20">
              <TableCell className="text-center text-xs">{row.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center font-mono text-xs tracking-wider">
                {row.status
                  .split('')
                  .map((c) => (c === '0' ? 'X' : 'O'))
                  .join('')}
              </TableCell>
              <TableCell className="text-center text-xs">{row.current.toFixed(1)}</TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

// ── 메인 컴포넌트 ──
interface DailyReportProps {
  activeObservations: string[];
  waterLevel: WaterLevelData[];
  rainfall: RainfallData[];
  displacement: DisplacementData[];
  soilMoisture: SoilMoistureData[];
  tilt: TiltData[];
  snow: SnowData[];
  flood: FloodData[];
}

export function DailyReport({
  activeObservations,
  waterLevel,
  rainfall,
  displacement,
  soilMoisture,
  tilt,
  snow,
  flood,
}: DailyReportProps) {
  const has = (code: string) => activeObservations.includes(code);

  return (
    <div className="mx-auto flex w-full max-w-3xl flex-col gap-5">
      {/* 보고서 헤더 */}
      <div className="rounded-lg border border-slate-200 bg-white p-6 text-center shadow-sm">
        <h2 className="text-lg font-bold text-slate-800">통합관제시스템 현황</h2>
        <p className="mt-1 text-sm text-slate-500">[재난안전대책본부]</p>
        <p className="mt-2 text-xs text-slate-400">
          {new Date().getFullYear()}년 {String(new Date().getMonth() + 1).padStart(2, '0')}월{' '}
          {String(new Date().getDate()).padStart(2, '0')}일 Day Report
        </p>
      </div>

      {/* 수위 (02) */}
      {has('02') && <WaterLevelTable data={waterLevel} />}

      {/* 강우 (01) */}
      {has('01') && <RainfallTable data={rainfall} />}

      {/* 변위 (03) */}
      {has('03') && <DisplacementTable data={displacement} />}

      {/* 함수비 (04) */}
      {has('04') && soilMoisture.length > 0 && <SoilMoistureTable data={soilMoisture} />}

      {/* 경사 (08) */}
      {has('08') && <TiltTable data={tilt} />}

      {/* 적설 (06) */}
      {has('06') && <SnowTable data={snow} />}

      {/* 침수 (21) */}
      {has('21') && <FloodTable data={flood} />}
    </div>
  );
}
