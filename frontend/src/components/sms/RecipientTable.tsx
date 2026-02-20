import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { UserPlus, Search } from 'lucide-react';

export interface Recipient {
  Idx: number;
  Name: string;
  Division: string;
  Position: string;
  PhoneNumber: string;
}

// Props 타입 정의
interface RecipientTableProps {
  recipients: Recipient[];
  selectedIds: Set<string>;
  onSelectedChange: (ids: Set<string>) => void;
}

const tableHeaders = ['번호', '부서명', '직책', '이름', '연락처'];

// Props로 selectedIds, onSelectedChange 받기
export function RecipientTable({ recipients, selectedIds, onSelectedChange }: RecipientTableProps) {
  const [searchQuery, setSearchQuery] = useState('');

  const filteredRecipients = recipients.filter(
    (r) =>
      (r.Name ?? '').includes(searchQuery) ||
      (r.Division ?? '').includes(searchQuery) ||
      (r.Position ?? '').includes(searchQuery) ||
      (r.PhoneNumber ?? '').includes(searchQuery)
  );

  const allSelected = filteredRecipients.length > 0 && filteredRecipients.every((r) => selectedIds.has(String(r.Idx)));
  const someSelected = filteredRecipients.some((r) => selectedIds.has(String(r.Idx)));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      onSelectedChange(new Set(filteredRecipients.map((r) => String(r.Idx))));
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
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
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
          <col className="w-12" />
          <col className="w-12" />
          <col />
          <col />
          <col />
          <col />
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
          {filteredRecipients.map((recipient, idx) => {
            const isSelected = selectedIds.has(String(recipient.Idx));
            return (
              <TableRow
                key={recipient.Idx}
                onClick={() => toggleOne(String(recipient.Idx))}
                className={cn(
                  'border-border/60 cursor-pointer transition-colors',
                  isSelected ? 'bg-primary/5 hover:bg-primary/8' : 'hover:bg-muted/30',
                  idx === filteredRecipients.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-3 py-3 text-center" onClick={(e) => e.stopPropagation()}>
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={(checked) => {
                        if (checked === true) {
                          toggleOne(String(recipient.Idx));
                        } else {
                          const n = new Set(selectedIds);
                          n.delete(String(recipient.Idx));
                          onSelectedChange(n);
                        }
                      }}
                      aria-label={`${recipient.Name} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {String(recipient.Idx).padStart(3, '0')}
                </TableCell>

                <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">
                  {recipient.Division}
                </TableCell>
                <TableCell className="px-2 py-2.5 text-center">
                  <span className="inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap text-blue-700 ring-1 ring-blue-200/60">
                    {recipient.Position}
                  </span>
                </TableCell>
                <TableCell
                  className={cn(
                    'px-3 py-3 text-center text-sm font-semibold',
                    isSelected ? 'text-primary' : 'text-foreground'
                  )}
                >
                  {recipient.Name}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {recipient.PhoneNumber}
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
