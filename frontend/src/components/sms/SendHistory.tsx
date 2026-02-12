import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Clock, CheckCircle2, XCircle } from 'lucide-react';

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
  success: { icon: CheckCircle2, label: '전송완료', className: 'text-emerald-500' },
  failed: { icon: XCircle, label: '전송실패', className: 'text-destructive' },
  pending: { icon: Clock, label: '전송대기중', className: 'text-amber-500' },
};

export function SendHistory() {
  return (
    <div className="rounded-xl border border-border bg-card">
      <Table className="table-fixed">
        <colgroup>
          <col className="w-24" />
          <col className="w-auto" />
          <col className="w-50" />
          <col className="w-50" />
          <col className="w-50" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/50 hover:bg-muted/50">
            <TableHead className="w-[5%] text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              번호
            </TableHead>
            <TableHead className="text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              제목
            </TableHead>
            <TableHead className="w-[15%] text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              수신자
            </TableHead>
            <TableHead className="w-[15%] text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              발송일시
            </TableHead>
            <TableHead className="w-[8%] text-center text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              상태
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {sampleHistory.map((item, idx) => {
            const config = statusConfig[item.status];
            const StatusIcon = config.icon;
            return (
              <TableRow
                key={item.id}
                className={`border-border transition-colors hover:bg-muted/30 ${idx === sampleHistory.length - 1 ? 'border-b-0' : ''}`}
              >
                <TableCell className="text-center text-sm font-medium text-card-foreground">
                  {item.id}
                </TableCell>
                <TableCell className="text-center text-sm font-medium text-card-foreground">
                  {item.title}
                </TableCell>
                <TableCell className="text-center text-sm text-muted-foreground">
                  {item.recipient}
                </TableCell>
                <TableCell className="text-center text-sm font-mono text-muted-foreground">
                  {item.date}
                </TableCell>
                <TableCell className="text-center">
                  <span
                    className={`inline-flex items-center gap-1.5 text-xs font-medium ${config.className}`}
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
