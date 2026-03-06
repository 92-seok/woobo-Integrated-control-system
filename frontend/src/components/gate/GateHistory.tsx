import { useState, useMemo } from 'react';
import { Search, ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { GateControlHistory } from '@/types/gate';
import { GATE_STATUS_CONFIG, GSTATUS_CONFIG } from '@/types/gate';

const PER_PAGE = 10;

interface GateHistoryProps {
  history: GateControlHistory[];
}

export function GateHistory({ history }: GateHistoryProps) {
  // 기본값: 7일 전 ~ 오늘
  const [startDate, setStartDate] = useState(() => new Date(Date.now() - 7 * 86400000).toISOString().slice(0, 10));
  const [endDate, setEndDate] = useState(() => new Date().toISOString().slice(0, 10));
  const [page, setPage] = useState(1);

  // 날짜 필터링
  const filtered = useMemo(() => {
    return history.filter((item) => {
      const date = item.RegDate.slice(0, 10);
      return date >= startDate && date <= endDate;
    });
  }, [history, startDate, endDate]);

  // 페이지네이션
  const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
  const paged = filtered.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  const handleSearch = () => {
    setPage(1); // 검색 시 1페이지로
  };

  return (
    <div className="space-y-4">
      {/* 검색 바 */}
      <div className="border-border flex flex-wrap items-center gap-3 rounded-lg border bg-white p-4 shadow-sm">
        <label className="text-sm font-medium">조회기간</label>
        <input
          type="date"
          value={startDate}
          onChange={(e) => setStartDate(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        />
        <span className="text-muted-foreground text-sm">~</span>
        <input
          type="date"
          value={endDate}
          onChange={(e) => setEndDate(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        />
        <Button size="sm" onClick={handleSearch}>
          <Search className="mr-1.5 h-3.5 w-3.5" />
          검색
        </Button>
        <span className="text-muted-foreground ml-auto text-xs">총 {filtered.length}건</span>
      </div>

      {/* 테이블 */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <Table className="table-fixed">
          <colgroup>
            <col className="w-12" />
            <col />
            <col className="w-24" />
            <col className="w-24" />
            <col className="w-44" />
          </colgroup>
          <TableHeader>
            <TableRow className="bg-muted/30 hover:bg-muted/30">
              <TableHead className="px-3 py-2.5 text-center text-xs">No</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">차단기명</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">제어</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">처리상태</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">일시</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {paged.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground px-3 py-10 text-center text-sm">
                  조회된 이력이 없습니다.
                </TableCell>
              </TableRow>
            ) : (
              paged.map((item, idx) => {
                const gateInfo = GATE_STATUS_CONFIG[item.Gate] ?? { label: item.Gate, color: '' };
                const statusInfo = GSTATUS_CONFIG[item.GStatus] ?? { label: item.GStatus, color: '' };

                return (
                  <TableRow key={item.GCtrCode}>
                    <TableCell className="px-3 py-3 text-center text-sm">{(page - 1) * PER_PAGE + idx + 1}</TableCell>
                    <TableCell className="px-3 py-3 text-center font-medium">
                      {item.NM_DIST_OBSV ?? item.CD_DIST_OBSV}
                    </TableCell>
                    <TableCell className="px-3 py-3 text-center">
                      <span className={cn('text-sm font-semibold', gateInfo.color)}>{gateInfo.label}</span>
                    </TableCell>
                    <TableCell className="px-3 py-3 text-center">
                      <span className={cn('text-sm font-semibold', statusInfo.color)}>{statusInfo.label}</span>
                    </TableCell>
                    <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">
                      {item.RegDate}
                    </TableCell>
                  </TableRow>
                );
              })
            )}
          </TableBody>
        </Table>

        {/* 페이지네이션 */}
        {totalPages > 1 && (
          <div className="border-border flex items-center justify-center gap-1 border-t px-4 py-3">
            <Button variant="ghost" size="sm" disabled={page === 1} onClick={() => setPage(page - 1)}>
              <ChevronLeft className="h-4 w-4" />
            </Button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
              <Button
                key={p}
                variant={p === page ? 'default' : 'ghost'}
                size="sm"
                onClick={() => setPage(p)}
                className="w-8"
              >
                {p}
              </Button>
            ))}
            <Button variant="ghost" size="sm" disabled={page === totalPages} onClick={() => setPage(page + 1)}>
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}
