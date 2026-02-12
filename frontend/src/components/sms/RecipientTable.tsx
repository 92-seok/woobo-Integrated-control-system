import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { UserPlus, Search } from 'lucide-react';

export interface Recipient {
  id: string;
  name: string;
  department: string;
  position: string;
  alias: string;
  phone: string;
}

const sampleRecipients: Recipient[] = [
  {
    id: '1',
    name: '김철수',
    department: '관제운영팀',
    position: '팀장',
    alias: '철수',
    phone: '010-1234-5678',
  },
  {
    id: '2',
    name: '이영희',
    department: '관제운영팀',
    position: '대리',
    alias: '영희',
    phone: '010-2345-6789',
  },
  {
    id: '3',
    name: '박민수',
    department: '시설관리팀',
    position: '과장',
    alias: '민수',
    phone: '010-3456-7890',
  },
  {
    id: '4',
    name: '정수진',
    department: '시설관리팀',
    position: '사원',
    alias: '수진',
    phone: '010-4567-8901',
  },
  {
    id: '5',
    name: '최동현',
    department: 'IT운영팀',
    position: '부장',
    alias: '동현',
    phone: '010-5678-9012',
  },
];

export function RecipientTable() {
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [searchQuery, setSearchQuery] = useState('');

  const filteredRecipients = sampleRecipients.filter(
    (r) =>
      r.name.includes(searchQuery) ||
      r.department.includes(searchQuery) ||
      r.position.includes(searchQuery) ||
      r.phone.includes(searchQuery)
  );

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      setSelectedIds(new Set(filteredRecipients.map((r) => r.id)));
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

  return (
    <div className="rounded-xl border border-border bg-card">
      <div className="flex items-center justify-between border-b border-border px-5 py-4">
        <div className="flex items-center gap-2">
          <UserPlus className="h-4 w-4 text-primary" />
          <h3 className="text-sm font-semibold text-card-foreground">수신자 선택</h3>
          {selectedIds.size > 0 && (
            <span className="rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground">
              {selectedIds.size}명 선택
            </span>
          )}
        </div>
        <div className="relative">
          <Search className="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted-foreground" />
          <input
            type="text"
            placeholder="검색..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="h-8 w-48 rounded-lg border border-border bg-background pl-8 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>
      </div>

      <Table className="table-fixed">
        <colgroup>
          <col className="w-3" />
          <col className="w-3" />
          <col className="w-20" />
          <col className="w-35" />
          <col className="w-10" />
          <col className="w-10" />
          <col className="w-15" />
        </colgroup>
        <TableHeader>
          <TableRow className="h-10 border-border bg-muted/50 hover:bg-muted/50">
            <TableHead className="w-10 px-1 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              <span className="flex items-center justify-center">선택</span>
            </TableHead>
            <TableHead className="w-12 px-1 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              번호
            </TableHead>
            <TableHead className="w-20 px-2 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              이름
            </TableHead>
            <TableHead className="w-24 px-2 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              부서명
            </TableHead>
            <TableHead className="w-16 px-2 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              직책
            </TableHead>
            <TableHead className="w-14 px-2 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              별칭
            </TableHead>
            <TableHead className="w-28 px-2 py-2 text-center align-middle text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              연락처
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {filteredRecipients.map((recipient, idx) => (
            <TableRow
              key={recipient.id}
              className={cn(
                'border-border transition-colors hover:bg-muted/30',
                selectedIds.has(recipient.id) && 'bg-primary/5',
                idx === filteredRecipients.length - 1 && 'border-b-0'
              )}
            >
              <TableCell className="w-10 px-1 py-2 text-center align-middle">
                <div className="flex items-center justify-center">
                  <Checkbox
                    checked={selectedIds.has(recipient.id)}
                    onCheckedChange={(checked) => {
                      if (checked === true) toggleOne(recipient.id);
                      else
                        setSelectedIds((prev) => {
                          const n = new Set(prev);
                          n.delete(recipient.id);
                          return n;
                        });
                    }}
                    aria-label={`${recipient.name} 선택`}
                  />
                </div>
              </TableCell>
              <TableCell className="w-12 px-1 py-2 text-center align-middle text-sm font-medium text-card-foreground">
                {recipient.id}
              </TableCell>
              <TableCell className="w-20 px-2 py-2 text-center align-middle text-sm font-medium text-card-foreground">
                {recipient.name}
              </TableCell>
              <TableCell className="w-24 px-2 py-2 text-center align-middle text-sm text-muted-foreground">
                {recipient.department}
              </TableCell>
              <TableCell className="w-16 px-2 py-2 text-center align-middle">
                <span className="inline-flex rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                  {recipient.position}
                </span>
              </TableCell>
              <TableCell className="w-14 px-2 py-2 text-center align-middle text-sm text-muted-foreground">
                {recipient.alias}
              </TableCell>
              <TableCell className="w-28 px-2 py-2 text-center align-middle text-sm font-mono text-muted-foreground">
                {recipient.phone}
              </TableCell>
            </TableRow>
          ))}
          {filteredRecipients.length === 0 && (
            <TableRow>
              <TableCell colSpan={7} className="py-8 text-center text-sm text-muted-foreground">
                검색 결과가 없습니다.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>
    </div>
  );
}
