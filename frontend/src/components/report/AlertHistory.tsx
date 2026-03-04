import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { AlertStatus } from '@/types/report';

/** 경보 레벨 라벨 */
function levelLabel(kind: string): string {
  const map: Record<string, string> = {
    level1: '레벨1',
    level2: '레벨2',
    level3: '레벨3',
    level4: '레벨4',
  };
  return map[kind] ?? '-';
}

/** 경보 레벨 배지 색상 */
function levelColor(kind: string): string {
  const map: Record<string, string> = {
    level1: 'bg-blue-100 text-blue-700',
    level2: 'bg-yellow-100 text-yellow-700',
    level3: 'bg-orange-100 text-orange-700',
    level4: 'bg-red-100 text-red-700',
  };
  return map[kind] ?? 'bg-slate-100 text-slate-500';
}

/** 발생사유 코드 → 한글 */
function occurLabel(occur: string): string {
  const codes = occur.split(',');
  const map: Record<string, string> = {
    '01': '강우',
    '02': '수위',
    '03': '변위',
    manual: '수동제어',
  };
  return codes.map((c) => map[c.trim()] ?? c).join(', ');
}

/** 상태 라벨 + 배지 */
function statusBadge(status: string): { label: string; className: string } {
  const map: Record<string, { label: string; className: string }> = {
    'm-start': { label: '수동 시작', className: 'bg-purple-100 text-purple-700' },
    start: { label: '시작', className: 'bg-blue-100 text-blue-700' },
    ing: { label: '발령 중', className: 'bg-red-100 text-red-700' },
    end: { label: '종료', className: 'bg-slate-100 text-slate-500' },
  };
  return map[status] ?? { label: '대기 중', className: 'bg-slate-100 text-slate-400' };
}

interface AlertHistoryProps {
  alerts: AlertStatus[];
}

export function AlertHistory({ alerts }: AlertHistoryProps) {
  return (
    <div className="mx-auto w-full max-w-3xl">
      <div className="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div className="flex items-center justify-between rounded-t-lg bg-slate-100 px-4 py-2.5">
          <span className="text-sm font-semibold text-slate-700">◈ 경보현황</span>
          <span className="text-xs text-slate-500">최근 5건</span>
        </div>
        <Table>
          <TableHeader>
            <TableRow className="bg-purple-800/90 hover:bg-purple-800/90">
              <TableHead className="text-center text-xs font-semibold text-white">지구명</TableHead>
              <TableHead className="text-center text-xs font-semibold text-white">경보발령단계</TableHead>
              <TableHead className="hidden text-center text-xs font-semibold text-white md:table-cell">
                발령시간
              </TableHead>
              <TableHead className="hidden text-center text-xs font-semibold text-white md:table-cell">
                종료시간
              </TableHead>
              <TableHead className="text-center text-xs font-semibold text-white">발생사유</TableHead>
              <TableHead className="text-center text-xs font-semibold text-white">상태</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {alerts.map((row, i) => {
              const badge = statusBadge(row.IStatus);
              return (
                <TableRow key={`${row.GCode}-${i}`} className="hover:bg-muted/20">
                  <TableCell className="text-center text-xs">{row.GName}</TableCell>
                  <TableCell className="text-center text-xs">
                    <span
                      className={`inline-block rounded-full px-2 py-0.5 text-xs font-medium ${levelColor(row.IsuKind)}`}
                    >
                      {levelLabel(row.IsuKind)}
                    </span>
                  </TableCell>
                  <TableCell className="hidden text-center text-xs md:table-cell">{row.IsuSrtDate ?? '-'}</TableCell>
                  <TableCell className="hidden text-center text-xs md:table-cell">{row.IsuEndDate ?? '-'}</TableCell>
                  <TableCell className="text-center text-xs">{occurLabel(row.Occur)}</TableCell>
                  <TableCell className="text-center text-xs">
                    <span className={`inline-block rounded-full px-2 py-0.5 text-xs font-medium ${badge.className}`}>
                      {badge.label}
                    </span>
                  </TableCell>
                </TableRow>
              );
            })}
            {alerts.length === 0 && (
              <TableRow>
                <TableCell colSpan={6} className="py-8 text-center text-sm text-slate-400">
                  경보 내역이 없습니다.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
