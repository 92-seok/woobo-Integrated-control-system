import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { UserPlus, Search } from 'lucide-react';

export interface Recipient {
  id: string;
  name: string;
  department: string;
  position: string;
  phone: string;
}

// export 해서 SmsPage에서 받을 수 있게
// eslint-disable-next-line react-refresh/only-export-components
export const sampleRecipients: Recipient[] = [
  { id: '1', name: '김철수', department: '관제운영팀', position: '팀장', phone: '010-1234-5678' },
  { id: '2', name: '이영희', department: '관제운영팀', position: '대리', phone: '010-2345-6789' },
  { id: '3', name: '박민수', department: '시설관리팀', position: '과장', phone: '010-3456-7890' },
  { id: '4', name: '정수진', department: '시설관리팀', position: '사원', phone: '010-4567-8901' },
  { id: '5', name: '최동현', department: 'IT운영팀', position: '부장', phone: '010-5678-9012' },
];

const tableHeaders = ['번호', '이름', '부서명', '직책', '연락처'];

// Props 타입 정의
interface RecipientTableProps {
  selectedIds: Set<string>;
  onSelectedChange: (ids: Set<string>) => void;
}

// Props로 selectedIds, onSelectedChange 받기
export function RecipientTable({ selectedIds, onSelectedChange }: RecipientTableProps) {
  // const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [searchQuery, setSearchQuery] = useState('');

  const filteredRecipients = sampleRecipients.filter(
    (r) =>
      r.name.includes(searchQuery) ||
      r.department.includes(searchQuery) ||
      r.position.includes(searchQuery) ||
      r.phone.includes(searchQuery)
  );

  const allSelected = filteredRecipients.length > 0 && filteredRecipients.every((r) => selectedIds.has(r.id));
  const someSelected = filteredRecipients.some((r) => selectedIds.has(r.id));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      onSelectedChange(new Set(filteredRecipients.map((r) => r.id)));
    } else {
      onSelectedChange(new Set());
    }
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    if (next.has(id)) {
      next.delete(id);
    } else {
      next.add(id);
    }
    onSelectedChange(next);
  };

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex items-center justify-between border-b px-5 py-3">
        <div className="flex items-center gap-2.5">
          <UserPlus className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">수신자 선택</h3>
          {selectedIds.size > 0 && (
            <span className="bg-primary text-primary-foreground rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm">
              {selectedIds.size}명 선택
            </span>
          )}
        </div>
        <div className="relative">
          <Search className="text-muted-foreground absolute top-1/2 left-2.5 h-3.5 w-3.5 -translate-y-1/2" />
          <input
            type="text"
            placeholder="이름, 부서, 연락처 검색..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="border-border text-foreground placeholder:text-muted-foreground/60 focus:border-primary/60 focus:ring-primary/10 h-8 w-52 rounded-md border bg-white pr-3 pl-8 text-xs transition-colors outline-none focus:ring-2"
          />
        </div>
      </div>

      <Table className="table-fixed">
        <colgroup>
          <col className="w-10" />
          <col className="w-12" />
          <col className="w-20" />
          <col />
          <col className="w-16" />
          <col className="w-36" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
            <TableHead className="px-2 py-2.5 text-center">
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
                className="text-muted-foreground px-2 py-2.5 text-center text-xs font-semibold tracking-wide uppercase"
              >
                {h}
              </TableHead>
            ))}
          </TableRow>
        </TableHeader>
        <TableBody>
          {filteredRecipients.map((recipient, idx) => {
            const isSelected = selectedIds.has(recipient.id);
            return (
              <TableRow
                key={recipient.id}
                onClick={() => toggleOne(recipient.id)}
                className={cn(
                  'border-border/60 cursor-pointer transition-colors',
                  isSelected ? 'bg-primary/5 hover:bg-primary/8' : 'hover:bg-muted/30',
                  idx === filteredRecipients.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-2 py-2.5 text-center" onClick={(e) => e.stopPropagation()}>
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={(checked) => {
                        if (checked === true) {
                          toggleOne(recipient.id);
                        } else {
                          const n = new Set(selectedIds);
                          n.delete(recipient.id);
                          onSelectedChange(n);
                        }
                      }}
                      aria-label={`${recipient.name} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-2 py-2.5 text-center text-xs">
                  {recipient.id.padStart(3, '0')}
                </TableCell>
                <TableCell
                  className={cn(
                    'px-2 py-2.5 text-center text-sm font-semibold',
                    isSelected ? 'text-primary' : 'text-foreground'
                  )}
                >
                  {recipient.name}
                </TableCell>
                <TableCell className="text-muted-foreground px-2 py-2.5 text-center text-sm">
                  {recipient.department}
                </TableCell>
                <TableCell className="px-2 py-2.5 text-center">
                  <span className="inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap text-blue-700 ring-1 ring-blue-200/60">
                    {recipient.position}
                  </span>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-2 py-2.5 text-center text-xs">
                  {recipient.phone}
                </TableCell>
              </TableRow>
            );
          })}
          {filteredRecipients.length === 0 && (
            <TableRow>
              <TableCell colSpan={6} className="text-muted-foreground py-10 text-center text-sm">
                검색 결과가 없습니다.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>
    </div>
  );
}
