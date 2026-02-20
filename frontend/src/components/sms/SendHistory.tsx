import { useState, useEffect } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Clock, Trash2 } from 'lucide-react';
import { cn } from '@/lib/utils';

interface HistoryItem {
  Idx: number;
  Title: string;
  Content: string;
  RegDate: string;
  DataCount: number;
  DataSuccess: number;
  DataFail: number;
  DataError: number;
  DataIng: number;
}

const tableHeaders = ['번호', '제목', '내용', '발송일시', '처리건수', '전송중', '전송완료', '전송실패', '전송오류'];

export function SendHistory() {
  const [history, setHistory] = useState<HistoryItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());

  const fetchHistory = () => {
    fetch('/api/smslist')
      .then((res) => res.json())
      .then((data) => {
        console.log('발송내역 API: ', JSON.stringify(data[0], null, 2));
        setHistory(data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchHistory();
  }, []);

  const allSelected = history.length > 0 && history.every((r) => selectedIds.has(String(r.Idx)));
  const someSelected = history.some((r) => selectedIds.has(String(r.Idx)));

  const toggleAll = (check: boolean | 'indeterminate') => {
    if (check === true) {
      setSelectedIds(new Set(history.map((r) => String(r.Idx))));
    } else {
      setSelectedIds(new Set());
    }
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    if (next.has(id)) {
      next.delete(id);
    } else {
      next.add(id);
    }
    setSelectedIds(next);
  };

  const handleDelete = () => {
    if (selectedIds.size === 0) return;
    Promise.all(
      Array.from(selectedIds).map((id) =>
        fetch(`/api/smslist/${id}`, { method: 'DELETE' }).then(() => {
          setSelectedIds(new Set());
          fetchHistory();
        })
      )
    );
  };

  if (loading) return <div className="text-muted-foreground p-10 text-center text-sm">로딩 중...</div>;
  if (error) return <div className="text-muted-foreground p-10 text-center text-sm">오류: {error}</div>;

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <Clock className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">발송 내역</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {history.length}건
          </span>
        </div>
        <button
          type="button"
          onClick={handleDelete}
          disabled={selectedIds.size === 0}
          className={cn(
            'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]',
            selectedIds.size > 0
              ? 'bg-red-500 text-white hover:bg-red-200'
              : 'bg-muted text-muted-foreground cursor-net-allowed opacity-50'
          )}
        >
          <Trash2 className="h-3.5 w-3.5" />
          {selectedIds.size > 0 ? `${selectedIds.size}건 삭제` : '삭제'}
        </button>
      </div>

      <Table>
        <colgroup>
          <col className="w-12" />
          <col className="w-12" />
          <col className="w-40" />
          <col />
          <col className="w-50" />
          <col className="w-25" />
          <col className="w-25" />
          <col className="w-25" />
          <col className="w-25" />
          <col className="w-25" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
            <TableHead className="px-3 py-2.5 text-center">
              <div className="flex items-center justify-center">
                <Checkbox
                  checked={allSelected ? true : someSelected ? 'indeterminate' : false}
                  onCheckedChange={toggleAll}
                  aria-label="전체 선택"
                />
              </div>
            </TableHead>
            {tableHeaders.map((h) => (
              <TableHead
                key={h}
                className="text-muted-foreground px-3 py-2.5 text-center text-xs font-semibold tracking-wide uppercase"
              >
                {h}
              </TableHead>
            ))}
          </TableRow>
        </TableHeader>
        <TableBody>
          {history.map((item, idx) => {
            const isSelected = selectedIds.has(String(item.Idx));
            return (
              <TableRow
                key={item.Idx}
                onClick={() => toggleOne(String(item.Idx))}
                className={cn(
                  'border-border/60 cursor-pointer transition-colors',
                  isSelected ? 'bg-primary/5 hover:bg-primary/8' : 'hover:bg-muted/20',
                  idx === history.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-3 py-3 text-center" onClick={(e) => e.stopPropagation()}>
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={() => toggleOne(String(item.Idx))}
                      aria-label={`${item.Title} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {String(item.Idx).padStart(3, '0')}
                </TableCell>
                <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">{item.Title}</TableCell>
                <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">{item.Content}</TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.RegDate}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.DataCount}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.DataIng}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.DataFail}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.DataError}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.DataSuccess}
                </TableCell>
              </TableRow>
            );
          })}
        </TableBody>
      </Table>
    </div>
  );
}
