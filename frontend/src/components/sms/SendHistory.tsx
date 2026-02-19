import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Clock, CheckCircle2, XCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

interface HistoryItem {
  id: string;
  title: string;
  recipient: string;
  date: string;
  status: 'success' | 'failed' | 'pending';
}

const sampleHistory: HistoryItem[] = [
  { id: '1', title: '긴급 점검 안내', recipient: '관제운영팀 (3명)', date: '2026-02-12 14:30', status: 'success' },
  { id: '2', title: '시스템 업데이트 공지', recipient: '전체 (10명)', date: '2026-02-12 10:15', status: 'success' },
  { id: '3', title: '야간 순찰 안내', recipient: '김철수 외 2명', date: '2026-02-11 22:00', status: 'failed' },
  { id: '4', title: '월간 보고 요청', recipient: '시설관리팀 (4명)', date: '2026-02-11 09:00', status: 'success' },
  { id: '5', title: '비상 연락 테스트', recipient: 'IT운영팀 (2명)', date: '2026-02-10 16:45', status: 'pending' },
];

const statusConfig = {
  success: {
    icon: CheckCircle2,
    label: '전송완료',
    className: 'text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200',
  },
  failed: {
    icon: XCircle,
    label: '전송실패',
    className: 'text-red-700 bg-red-50 ring-1 ring-red-200',
  },
  pending: {
    icon: Clock,
    label: '전송대기중',
    className: 'text-amber-700 bg-amber-50 ring-1 ring-amber-200',
  },
};

const tableHeaders = ['번호', '제목', '수신자', '발송일시', '상태'];

export function SendHistory() {
  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex items-center gap-2.5 border-b px-5 py-3">
        <Clock className="text-primary h-4 w-4" />
        <h3 className="font-jakarta text-foreground text-sm font-semibold">발송 내역</h3>
        <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
          {sampleHistory.length}건
        </span>
      </div>

      <Table className="table-fixed">
        <colgroup>
          <col className="w-20" />
          <col />
          <col className="w-50" />
          <col className="w-50" />
          <col className="w-30" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
            {tableHeaders.map((h) => (
              <TableHead
                key={h}
                className="text-muted-foreground px-4 py-2.5 text-center text-xs font-semibold tracking-wide uppercase"
              >
                {h}
              </TableHead>
            ))}
          </TableRow>
        </TableHeader>
        <TableBody>
          {sampleHistory.map((item, idx) => {
            const config = statusConfig[item.status];
            const StatusIcon = config.icon;
            return (
              <TableRow
                key={item.id}
                className={cn(
                  'border-border/60 hover:bg-muted/20 transition-colors',
                  idx === sampleHistory.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="font-mono-data text-muted-foreground px-4 py-3 text-center text-xs">
                  {item.id.padStart(3, '0')}
                </TableCell>
                <TableCell className="text-foreground px-4 py-3 text-center text-sm font-semibold">
                  {item.title}
                </TableCell>
                <TableCell className="text-muted-foreground px-4 py-3 text-center text-sm">{item.recipient}</TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-4 py-3 text-center text-xs">
                  {item.date}
                </TableCell>
                <TableCell className="px-2 py-4 text-center">
                  <span
                    className={cn(
                      'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                      config.className
                    )}
                  >
                    <StatusIcon className="h-3.5 w-3.5" />
                    {config.label}
                  </span>
                </TableCell>
              </TableRow>
            );
          })}
        </TableBody>
      </Table>
    </div>
  );
}
