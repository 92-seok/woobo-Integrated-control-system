import { useState } from 'react';
import { createPortal } from 'react-dom';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import { Plus, Trash2, Phone, ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_CID_LIST } from '../../mocks/mock';
import type { Equipment, CidItem } from '../../types/broad';

const PER_PAGE = 5;
const tableHeaders = ['번호', '장비명', 'CID', '등록일시', '상태'];

// CID 상태 뱃지
const cidStatusConfig: Record<string, { label: string; className: string }> = {
  start: { label: '등록중', className: 'bg-gray-100 text-gray-600 ring-gray-200' },
  ing: { label: '등록중', className: 'bg-gray-100 text-gray-600 ring-gray-200' },
  end: { label: '등록완료', className: 'bg-blue-50 text-blue-700 ring-blue-200/60' },
  error: { label: '등록오류', className: 'bg-red-50 text-red-700 ring-red-200/60' },
};

interface CidManagerProps {
  equipments: Equipment[];
}

export function CidManager({ equipments }: CidManagerProps) {
  const [cidList, setCidList] = useState<CidItem[]>(MOCK_CID_LIST);
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [currentPage, setCurrentPage] = useState(1);
  const [modalOpen, setModalOpen] = useState(false);

  // ── 등록 폼 ──
  const [formEquip, setFormEquip] = useState('');
  const [formCid, setFormCid] = useState('');

  // ── 페이징 ──
  const totalPages = Math.max(1, Math.ceil(cidList.length / PER_PAGE));
  const safePage = Math.min(currentPage, totalPages);
  const paged = cidList.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  // ── 전체선택 ──
  const allSelected = cidList.length > 0 && cidList.every((c) => selectedIds.has(String(c.CidCode)));
  const someSelected = cidList.some((c) => selectedIds.has(String(c.CidCode)));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      setSelectedIds(new Set(cidList.map((c) => String(c.CidCode))));
    } else {
      setSelectedIds(new Set());
    }
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    next.has(id) ? next.delete(id) : next.add(id);
    setSelectedIds(next);
  };

  // ── CID 등록 (mock: 로컬 state에 추가) ──
  const handleAdd = () => {
    if (!formEquip) {
      alert('장비를 선택하세요.');
      return;
    }
    if (!formCid.trim()) {
      alert('CID 번호를 입력하세요.');
      return;
    }

    const equip = equipments.find((e) => e.CD_DIST_OBSV === formEquip);
    const maxCode = Math.max(0, ...cidList.map((c) => c.CidCode));
    const newCid: CidItem = {
      CidCode: maxCode + 1,
      CD_DIST_OBSV: formEquip,
      NM_DIST_OBSV: equip?.NM_DIST_OBSV ?? '',
      Cid: formCid.trim(),
      CStatus: 'start',
      RegDate: new Date().toISOString().slice(0, 19).replace('T', ' '),
    };

    setCidList([newCid, ...cidList]);
    setModalOpen(false);
    setFormEquip('');
    setFormCid('');
    // TODO: API 연동 시 POST /api/broad/cid
  };

  // ── CID 삭제 ──
  const handleDelete = () => {
    if (selectedIds.size === 0) return;
    if (!confirm(`${selectedIds.size}건의 CID를 삭제하시겠습니까?`)) return;
    setCidList(cidList.filter((c) => !selectedIds.has(String(c.CidCode))));
    setSelectedIds(new Set());
    // TODO: API 연동 시 DELETE /api/broad/cid/:id
  };

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* ── 카드 헤더 ── */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <Phone className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">CID 관리</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {cidList.length}건
          </span>
        </div>
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={handleDelete}
            disabled={selectedIds.size === 0}
            className={cn(
              'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all',
              selectedIds.size > 0
                ? 'bg-red-500 text-white hover:bg-red-600'
                : 'bg-muted text-muted-foreground cursor-not-allowed opacity-50'
            )}
          >
            <Trash2 className="h-3.5 w-3.5" />
            삭제
          </button>
          <button
            type="button"
            onClick={() => setModalOpen(true)}
            className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]"
          >
            <Plus className="h-3.5 w-3.5" />
            CID 등록
          </button>
        </div>
      </div>

      {/* ── 페이징 바 ── */}
      <div className="border-border flex items-center justify-end border-b px-5 py-2">
        <div className="flex items-center gap-1.5">
          <span className="text-muted-foreground mr-1 text-[11px]">
            {(safePage - 1) * PER_PAGE + 1}-{Math.min(safePage * PER_PAGE, cidList.length)}
            {' / '}
            {cidList.length}건
          </span>
          <button
            type="button"
            onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
            disabled={safePage <= 1}
            className="border-border rounded border p-1 transition-colors hover:bg-gray-100 disabled:opacity-30"
          >
            <ChevronLeft className="h-3.5 w-3.5" />
          </button>
          <span className="text-foreground min-w-[3rem] text-center text-xs font-semibold">
            {safePage} / {totalPages}
          </span>
          <button
            type="button"
            onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
            disabled={safePage >= totalPages}
            className="border-border rounded border p-1 transition-colors hover:bg-gray-100 disabled:opacity-30"
          >
            <ChevronRight className="h-3.5 w-3.5" />
          </button>
        </div>
      </div>

      {/* ── 테이블 ── */}
      <Table>
        <colgroup>
          <col className="w-12" />
          <col className="w-16" />
          <col />
          <col className="w-36" />
          <col className="w-44" />
          <col className="w-24" />
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
          {paged.map((cid, idx) => {
            const isSelected = selectedIds.has(String(cid.CidCode));
            const status = cidStatusConfig[cid.CStatus] ?? cidStatusConfig.error;
            return (
              <TableRow
                key={cid.CidCode}
                className={cn(
                  'border-border/60 transition-colors',
                  isSelected ? 'bg-primary/5' : 'hover:bg-muted/20',
                  idx === paged.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-3 py-3 text-center">
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={() => toggleOne(String(cid.CidCode))}
                      aria-label={`CID ${cid.Cid} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {String(cid.CidCode).padStart(3, '0')}
                </TableCell>
                <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                  {cid.NM_DIST_OBSV}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {cid.Cid}
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {cid.RegDate}
                </TableCell>
                <TableCell className="px-3 py-3 text-center">
                  <span
                    className={cn(
                      'inline-block rounded-full px-2.5 py-0.5 text-xs font-medium ring-1',
                      status.className
                    )}
                  >
                    {status.label}
                  </span>
                </TableCell>
              </TableRow>
            );
          })}
          {paged.length === 0 && (
            <TableRow>
              <TableCell colSpan={6} className="text-muted-foreground py-10 text-center text-sm">
                등록된 CID가 없습니다.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>

      {/* ── CID 등록 모달 ── */}
      {modalOpen &&
        createPortal(
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
              <h2 className="mb-1 text-sm font-semibold text-gray-800">CID 등록</h2>
              <p className="text-muted-foreground mb-4 text-xs">장비에 발신번호(CID)를 등록합니다.</p>

              <div className="flex flex-col gap-3">
                <div className="flex items-center gap-3">
                  <label className="w-16 shrink-0 text-xs font-medium text-gray-600">장비</label>
                  <select
                    value={formEquip}
                    onChange={(e) => setFormEquip(e.target.value)}
                    className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  >
                    <option value="">장비 선택</option>
                    {equipments.map((e) => (
                      <option key={e.CD_DIST_OBSV} value={e.CD_DIST_OBSV}>
                        {e.NM_DIST_OBSV} ({e.CD_DIST_OBSV})
                      </option>
                    ))}
                  </select>
                </div>
                <div className="flex items-center gap-3">
                  <label className="w-16 shrink-0 text-xs font-medium text-gray-600">CID</label>
                  <input
                    type="text"
                    value={formCid}
                    onChange={(e) => setFormCid(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && handleAdd()}
                    placeholder="발신번호 입력 (예: 0212345678)"
                    className="flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  />
                </div>
              </div>

              <div className="mt-5 flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => {
                    setModalOpen(false);
                    setFormEquip('');
                    setFormCid('');
                  }}
                  className="text-gray-60 rounded-md border border-gray-300 px-4 py-2 text-xs hover:bg-gray-50"
                >
                  취소
                </button>
                <button
                  type="button"
                  onClick={handleAdd}
                  className="bg-primary text-primary-foreground rounded-md px-4 py-2 text-xs font-semibold"
                >
                  등록
                </button>
              </div>
            </div>
          </div>,
          document.body
        )}
    </div>
  );
}

export default CidManager;
