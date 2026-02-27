import { useState } from 'react';
import { createPortal } from 'react-dom';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import { Plus, Trash2, FileText, Volume2, AlertTriangle, ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_TTS_MENTS, MOCK_ALERT_MENTS } from '../../mocks/mock';
import type { MessageMent } from '../../types/broad';

const PER_PAGE = 10;
const tableHeaders = ['번호', '제목', '내용'];

type MentTab = 'tts' | 'alert';

export function MessageTemplate() {
  // 탭 상태
  const [mentTab, setMentTab] = useState<MentTab>('tts');

  // 데이터 (추후 API 교체)
  const [ttsMents, setTtsMents] = useState<MessageMent[]>(MOCK_TTS_MENTS);
  const [alertMents, setAlertMents] = useState<MessageMent[]>(MOCK_ALERT_MENTS);

  // 현재 탭에 해당하는 데이터
  const currentMents = mentTab === 'tts' ? ttsMents : alertMents;
  const setCurrentMents = mentTab === 'tts' ? setTtsMents : setAlertMents;

  // 페이징
  const [currentPage, setCurrentPage] = useState(1);
  const totalPages = Math.max(1, Math.ceil(currentMents.length / PER_PAGE));
  const safePage = Math.min(currentPage, totalPages);
  const paged = currentMents.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  // 선택
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());

  const allSelected = currentMents.length > 0 && currentMents.every((m) => selectedIds.has(String(m.AltCode)));
  const someSelected = currentMents.some((m) => selectedIds.has(String(m.AltCode)));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      setSelectedIds(new Set(currentMents.map((m) => String(m.AltCode))));
    } else {
      setSelectedIds(new Set());
    }
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    next.has(id) ? next.delete(id) : next.add(id);
    setSelectedIds(next);
  };

  // 모달
  const [modalOpen, setModalOpen] = useState(false);
  const [modalMode, setModalMode] = useState<'add' | 'edit'>('add');
  const [editTarget, setEditTarget] = useState<MessageMent | null>(null);
  const [formTitle, setFormTitle] = useState('');
  const [formContent, setFormContent] = useState('');

  const openAddModal = () => {
    setFormTitle('');
    setFormContent('');
    setModalMode('add');
    setModalOpen(true);
  };

  const openEditModal = (ment: MessageMent) => {
    setFormTitle(ment.Title);
    setFormContent(ment.Content);
    setEditTarget(ment);
    setModalMode('edit');
    setModalOpen(true);
  };

  // 저장 (mock: 로컬 state에 추가/수정)
  const handleSubmit = () => {
    if (!formTitle.trim() || !formContent.trim()) {
      alert('제목과 내용을 모두 입력하세요.');
      return;
    }

    if (modalMode === 'add') {
      // 새 AltCode는 현재 최대값 + 1
      const maxCode = Math.max(0, ...currentMents.map((m) => m.AltCode));
      const newMent: MessageMent = {
        AltCode: maxCode + 1,
        Title: formTitle.trim(),
        Content: formContent.trim(),
        BUse: 'ON',
      };
      setCurrentMents([...currentMents, newMent]);
      // TODO: API 연동 시 POST /api/broad/ment { type: mentTab, ... }
    }

    if (modalMode === 'edit' && editTarget) {
      setCurrentMents(
        currentMents.map((m) =>
          m.AltCode === editTarget.AltCode ? { ...m, Title: formTitle.trim(), Content: formContent.trim() } : m
        )
      );
      // TODO: API 연동 시 PUT /api/broad/ment/:id
    }

    setModalOpen(false);
  };

  // 일괄 삭제 (mock: 로컬 state에서 제거)
  const handleBatchDelete = () => {
    if (selectedIds.size === 0) return;
    if (!confirm(`${selectedIds.size}건의 멘트를 삭제하시겠습니까?`)) return;
    setCurrentMents(currentMents.filter((m) => !selectedIds.has(String(m.AltCode))));
    setSelectedIds(new Set());
    // TODO: API 연동 시 DELETE /api/broad/ment/:id
  };

  // 탭 변경 시 선택/페이지 초기화
  const handleTabChange = (tab: MentTab) => {
    setMentTab(tab);
    setSelectedIds(new Set());
    setCurrentPage(1);
  };

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* ── 카드 헤더 ── */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <FileText className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">멘트 관리</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {currentMents.length}건
          </span>
        </div>
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={handleBatchDelete}
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
            onClick={openAddModal}
            className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]"
          >
            <Plus className="h-3.5 w-3.5" />
            멘트 추가
          </button>
        </div>
      </div>

      {/* ── TTS / 예경보 서브탭 + 페이징 ── */}
      <div className="border-border flex items-center justify-between border-b px-5 py-2">
        {/* 좌측: 서브탭 */}
        <div className="flex gap-1.5">
          <button
            type="button"
            onClick={() => handleTabChange('tts')}
            className={cn(
              'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition-all',
              mentTab === 'tts' ? 'bg-primary text-white shadow-sm' : 'bg-muted text-muted-foreground hover:bg-muted/80'
            )}
          >
            <Volume2 className="h-3 w-3" />
            TTS 멘트
          </button>
          <button
            type="button"
            onClick={() => handleTabChange('alert')}
            className={cn(
              'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold transition-all',
              mentTab === 'alert'
                ? 'bg-orange-500 text-white shadow-sm'
                : 'bg-muted text-muted-foreground hover:bg-muted/80'
            )}
          >
            <AlertTriangle className="h-3 w-3" />
            예경보 멘트
          </button>
        </div>

        {/* 우측: 페이징 */}
        <div className="flex items-center gap-1.5">
          <span className="text-muted-foreground mr-1 text-[11px]">
            {(safePage - 1) * PER_PAGE + 1}-{Math.min(safePage * PER_PAGE, currentMents.length)}
            {' / '}
            {currentMents.length}건
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
          <col className="w-48" />
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
          {paged.map((ment, idx) => {
            const isSelected = selectedIds.has(String(ment.AltCode));
            return (
              <TableRow
                key={ment.AltCode}
                onClick={() => openEditModal(ment)}
                className={cn(
                  'border-border/60 cursor-pointer transition-colors',
                  isSelected ? 'bg-primary/5 hover:bg-primary/8' : 'hover:bg-muted/20',
                  idx === paged.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-3 py-3 text-center" onClick={(e) => e.stopPropagation()}>
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={() => toggleOne(String(ment.AltCode))}
                      aria-label={`${ment.Title} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {String(ment.AltCode).padStart(3, '0')}
                </TableCell>
                <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                  {ment.Title}
                </TableCell>
                <TableCell className="text-muted-foreground px-3 py-3 text-sm">
                  <p className="line-clamp-2">{ment.Content}</p>
                </TableCell>
              </TableRow>
            );
          })}
          {paged.length === 0 && (
            <TableRow>
              <TableCell colSpan={4} className="text-muted-foreground py-10 text-center text-sm">
                등록된 멘트가 없습니다.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>

      {/* ── 추가/수정 모달 ── */}
      {modalOpen &&
        createPortal(
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
              <h2 className="mb-1 text-sm font-semibold text-gray-800">
                {modalMode === 'add' ? '멘트 추가' : '멘트 수정'}
              </h2>
              <p className="text-muted-foreground mb-4 text-xs">
                {mentTab === 'tts' ? 'TTS 멘트' : '예경보 멘트'}
                {modalMode === 'add' ? '를 새로 등록합니다.' : '를 수정합니다.'}
              </p>

              <div className="flex flex-col gap-3">
                <div className="flex flex-col gap-1.5">
                  <label className="text-xs font-medium text-gray-600">제목</label>
                  <input
                    type="text"
                    value={formTitle}
                    onChange={(e) => setFormTitle(e.target.value.slice(0, 50))}
                    placeholder="멘트 제목 (50자 이내)"
                    className="rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                  />
                </div>
                <div className="flex flex-col gap-1.5">
                  <label className="text-xs font-medium text-gray-600">내용</label>
                  <textarea
                    value={formContent}
                    onChange={(e) => setFormContent(e.target.value)}
                    placeholder="멘트 내용을 입력하세요"
                    rows={6}
                    className="resize-none rounded-md border border-gray-300 px-3 py-2 text-sm leading-relaxed outline-none focus:border-blue-500"
                  />
                </div>
              </div>

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
                  className={cn(
                    'rounded-md px-4 py-2 text-xs font-semibold text-white',
                    mentTab === 'tts' ? 'bg-primary' : 'bg-orange-500'
                  )}
                >
                  {modalMode === 'add' ? '등록' : '수정'}
                </button>
              </div>
            </div>
          </div>,
          document.body
        )}
    </div>
  );
}

export default MessageTemplate;
