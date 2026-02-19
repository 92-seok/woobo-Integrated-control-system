import { useState } from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2, BookUser } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Contact {
  id: string;
  name: string;
  department: string;
  position: string;
  phone: string;
}

const sampleContacts: Contact[] = [
  { id: '1', name: '김철수', department: '관제운영팀', position: '팀장', phone: '010-1234-5678' },
  { id: '2', name: '이영희', department: '관제운영팀', position: '대리', phone: '010-2345-6789' },
  { id: '3', name: '박민수', department: '시설관리팀', position: '과장', phone: '010-3456-7890' },
  { id: '4', name: '정수진', department: '시설관리팀', position: '사원', phone: '010-4567-8901' },
  { id: '5', name: '최동현', department: 'IT운영팀', position: '부장', phone: '010-5678-9012' },
];

const tableHeaders = ['번호', '직책', '이름', '부서명', '연락처', '관리'];

export function ContactsManager() {
  const [contacts] = useState<Contact[]>(sampleContacts);

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex items-center justify-between border-b px-5 py-3">
        <div className="flex items-center gap-2.5">
          <BookUser className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">연락처 목록</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {contacts.length}명
          </span>
        </div>
        <button
          type="button"
          className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]"
        >
          <Plus className="h-3.5 w-3.5" />
          연락처 추가
        </button>
      </div>

      <Table className="table-fixed">
        <colgroup>
          <col className="w-30" />
          <col className="w-30" />
          <col className="w-30" />
          <col />
          <col className="w-40" />
          <col className="w-30" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
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
          {contacts.map((contact, idx) => (
            <TableRow
              key={contact.id}
              className={cn(
                'border-border/60 hover:bg-muted/20 transition-colors',
                idx === contacts.length - 1 && 'border-b-0'
              )}
            >
              <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                {contact.id.padStart(3, '0')}
              </TableCell>
              <TableCell className="px-3 py-3 text-center">
                <span className="inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap text-blue-700 ring-1 ring-blue-200/60">
                  {contact.position}
                </span>
              </TableCell>
              <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                {contact.name}
              </TableCell>
              <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">
                {contact.department}
              </TableCell>
              <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                {contact.phone}
              </TableCell>
              <TableCell className="px-3 py-3 text-center">
                <div className="inline-flex items-center gap-1">
                  <button
                    type="button"
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-blue-50 hover:text-blue-600"
                    aria-label={`${contact.name} 수정`}
                  >
                    <Pencil className="h-3.5 w-3.5" />
                  </button>
                  <button
                    type="button"
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-red-50 hover:text-red-500"
                    aria-label={`${contact.name} 삭제`}
                  >
                    <Trash2 className="h-3.5 w-3.5" />
                  </button>
                </div>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}
