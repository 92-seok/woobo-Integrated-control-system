import { useEffect, useState } from 'react';
import { createPortal } from 'react-dom';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Pencil, Trash2, BookUser } from 'lucide-react';
import { cn } from '@/lib/utils';

interface Contact {
  Idx: number;
  Name: string;
  Division: string;
  Position: string;
  PhoneNumber: string;
}

const tableHeaders = ['번호', '부서명', '직책', '이름', '연락처', '관리'];

interface ContactsManagerProps {
  onRefresh?: () => void;
}

export function ContactsManager({ onRefresh }: ContactsManagerProps) {
  const [contacts, setContacts] = useState<Contact[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // 모달 state
  const [modalOpen, setModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'add' | 'edit' | 'delete'>('add');
  const [selectedContact, setSelectedContact] = useState<Contact | null>(null);

  const [formData, setFormData] = useState({
    Name: '',
    Division: '',
    Position: '',
    PhoneNumber: '',
  });

  // API 호출
  const handleSubmit = () => {
    if (modalMode === 'add') {
      fetch('/api/smsuser', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      }).then(() => {
        // 성공시 목록 새로고침
        fetchContacts();
        onRefresh?.();
        setModalOpen(false);
      });
    }

    if (modalMode === 'edit') {
      fetch(`/api/smsuser/${selectedContact?.Idx}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      }).then(() => {
        fetchContacts();
        onRefresh?.();
        setModalOpen(false);
      });
    }

    if (modalMode === 'delete') {
      fetch(`/api/smsuser/${selectedContact?.Idx}`, {
        method: 'DELETE',
      }).then(() => {
        fetchContacts();
        onRefresh?.();
        setModalOpen(false);
      });
    }
  };

  const fetchContacts = () => {
    fetch('/api/smsuser')
      .then((res) => res.json())
      .then((data) => {
        setContacts(data);
        setLoading(false);
      })
      .catch((err) => {
        setError(err.message);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchContacts();
  }, []);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const openAddModal = () => {
    setFormData({ Name: '', Division: '', Position: '', PhoneNumber: '' });
    setModalMode('add');
    setModalOpen(true);
  };

  const openEditModal = (contact: Contact) => {
    setFormData({
      Name: contact.Name,
      Division: contact.Division,
      Position: contact.Position,
      PhoneNumber: contact.PhoneNumber,
    });
    setSelectedContact(contact);
    setModalMode('edit');
    setModalOpen(true);
  };

  const openDeleteModal = (contact: Contact) => {
    setSelectedContact(contact);
    setModalMode('delete');
    setModalOpen(true);
  };

  if (loading) return <div className="text-muted-foreground p-10 text-center text-sm">로딩 중...</div>;
  if (error) return <div className="text-muted-foreground p-10 text-center text-sm">오류: {error}</div>;

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <BookUser className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">연락처 목록</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {contacts.length}명
          </span>
        </div>
        <button
          type="button"
          onClick={openAddModal}
          className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]"
        >
          <Plus className="h-3.5 w-3.5" />
          연락처 추가
        </button>
      </div>

      <Table className="table-fixed">
        <colgroup>
          <col className="w-10" />
          <col className="w-30" />
          <col className="w-30" />
          <col className="w-30" />
          <col className="w-40" />
          <col className="w-50" />
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
              key={contact.Idx}
              className={cn(
                'border-border/60 hover:bg-muted/20 transition-colors',
                idx === contacts.length - 1 && 'border-b-0'
              )}
            >
              <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                {String(contact.Idx).padStart(3, '0')}
              </TableCell>
              <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">{contact.Division}</TableCell>
              <TableCell className="px-3 py-3 text-center">
                <span className="inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap text-blue-700 ring-1 ring-blue-200/60">
                  {contact.Position}
                </span>
              </TableCell>
              <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                {contact.Name}
              </TableCell>
              <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                {contact.PhoneNumber}
              </TableCell>
              <TableCell className="px-3 py-3 text-center">
                <div className="inline-flex items-center gap-1">
                  <button
                    type="button"
                    onClick={() => openEditModal(contact)}
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-blue-50 hover:text-blue-600"
                    aria-label={`${contact.Name} 수정`}
                  >
                    <Pencil className="h-3.5 w-3.5" />
                  </button>
                  <button
                    type="button"
                    onClick={() => openDeleteModal(contact)}
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-red-50 hover:text-red-500"
                    aria-label={`${contact.Name} 삭제`}
                  >
                    <Trash2 className="h-3.5 w-3.5" />
                  </button>
                </div>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
      {modalOpen &&
        createPortal(
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
              {/* 모달 제목 */}
              <h2 className="mb-4 text-sm font-semibold text-gray-800">
                {modalMode === 'add' && '연락처 추가'}
                {modalMode === 'edit' && '연락처 수정'}
                {modalMode === 'delete' && '연락처 삭제'}
              </h2>

              {/* 추가/수정 폼 */}
              {modalMode !== 'delete' && (
                <div className="flex flex-col gap-3">
                  <div className="flex items-center gap-3">
                    <label className="w-16 shrink-0 text-xs font-medium text-gray-600">이름</label>
                    <input
                      name="Name"
                      placeholder="이름"
                      value={formData.Name}
                      onChange={handleChange}
                      className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="flex items-center gap-3">
                    <label className="w-16 shrink-0 text-xs font-medium text-gray-600">부서명</label>
                    <input
                      name="Division"
                      placeholder="부서명"
                      value={formData.Division}
                      onChange={handleChange}
                      className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="flex items-center gap-3">
                    <label className="w-16 shrink-0 text-xs font-medium text-gray-600">직책</label>
                    <input
                      name="Position"
                      placeholder="직책"
                      value={formData.Position}
                      onChange={handleChange}
                      className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    />
                  </div>
                  <div className="flex items-center gap-3">
                    <label className="w-16 shrink-0 text-xs font-medium text-gray-600">번호</label>
                    <input
                      name="PhoneNumber"
                      placeholder="전화번호"
                      value={formData.PhoneNumber}
                      onChange={handleChange}
                      className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    />
                  </div>
                </div>
              )}

              {/* 삭제 확인 */}
              {modalMode === 'delete' && (
                <div className="flex flex-col gap-2 rounded-md bg-gray-50 p-4 text-sm text-gray-600">
                  <div className="flex gap-3">
                    <span className="w-16 shrink-0 font-medium text-gray-500">이름</span>
                    <span>{selectedContact?.Name}</span>
                  </div>
                  <div className="flex gap-3">
                    <span className="w-16 shrink-0 font-medium text-gray-500">부서명</span>
                    <span>{selectedContact?.Division}</span>
                  </div>
                  <div className="flex gap-3">
                    <span className="w-16 shrink-0 font-medium text-gray-500">직책</span>
                    <span>{selectedContact?.Position}</span>
                  </div>
                  <div className="flex gap-3">
                    <span className="w-16 shrink-0 font-medium text-gray-500">전화번호</span>
                    <span>{selectedContact?.PhoneNumber}</span>
                  </div>
                  <p className="mt-2 text-xs text-red-500">위 연락처를 삭제하시겠습니까?</p>
                </div>
              )}

              {/* 버튼 */}
              <div className="mt-5 flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setModalOpen(false)}
                  className="rounded-md border border-gray-300 px-4 py-2 text-xs text-gray-600 hover:bg-gray-50"
                >
                  취소
                </button>
                <button
                  type="button"
                  onClick={handleSubmit}
                  className="bg-primary text-primary-foreground rounded-md px-4 py-2 text-xs font-semibold"
                >
                  {modalMode === 'delete' ? '삭제' : '저장'}
                </button>
              </div>
            </div>
          </div>,
          document.body
        )}
    </div>
  );
}
