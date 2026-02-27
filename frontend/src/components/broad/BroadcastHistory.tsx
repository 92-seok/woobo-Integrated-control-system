import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Clock, Trash2, ArrowLeft, ChevronLeft, ChevronRight, RefreshCw, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_HISTORY, MOCK_DETAIL } from '../../mocks/mock';
import { BTYPE_LABELS, STATUS_CONFIG } from '../../types/broad';
import type { BroadcastItem, BroadcastDetailItem } from '../../types/broad';

const PER_PAGE = 10;
const tableHeaders = ['번호', '제목', '유형', '방송일시', '대기', '성공', '실패'];

export function BroadcastHistory() {
  // 목록 상태
  const [history] = useState<BroadcastItem[]>(MOCK_HISTORY);
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [currentPage, setCurrentPage] = useState(1);

  // 상세보기 상태 (null = 목록, 값 = 상세)
  const [detailBCode, setDetailBCode] = useState<number | null>(null);
  const [detailItems, setDetailItems] = useState<BroadcastDetailItem[]>([]);
  const [detailInfo, setDetailInfo] = useState<BroadcastItem | null>(null);
  const [retryingId, setRetryingId] = useState<string | null>(null);

  // 페이징
  const totalPages = Math.max(1, Math.ceil(history.length / PER_PAGE));
  const safePage = Math.min(currentPage, totalPages);
  const paged = history.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  // 전체선택
  const allSelected = history.length > 0 && history.every((r) => selectedIds.has(String(r.BCode)));
  const someSelected = history.some((r) => selectedIds.has(String(r.BCode)));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    if (checked === true) {
      setSelectedIds(new Set(history.map((r) => String(r.BCode))));
    } else {
      setSelectedIds(new Set());
    }
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    next.has(id) ? next.delete(id) : next.add(id);
    setSelectedIds(next);
  };

  // 삭제
  const handleDelete = () => {
    if (selectedIds.size === 0) return;
    if (!confirm(`${selectedIds.size}건의 방송 내역을 삭제하시겠습니까?`)) return;

    // API 연동 시 fetch DELETE 호출
    console.log('삭제 대상: ', Array.from(selectedIds));
    alert(`${selectedIds.size}건 삭제 완료(MOCK)`);
    setSelectedIds(new Set());
  };

  // 상세보기 진입
  const openDetail = (item: BroadcastItem) => {
    setDetailBCode(item.BCode);
    setDetailInfo(item);
    // TODO: API 연동 시 fetch(`/api/broad/history/${item.BCode}`)
    setDetailItems(MOCK_DETAIL.filter((d) => d.BCode === item.BCode));
  };

  // 재발송 (mock)
  const handleRetry = async (equipCode: string) => {
    if (!confirm('이 장비에 방송을 재발송하시겠습니까?')) return;
    setRetryingId(equipCode);
    // TODO: API 연동 시 fetch POST /api/broad/retry
    console.log('재발송:', { equipCode, bCode: detailBCode });
    await new Promise((r) => setTimeout(r, 800));
    alert('재발송 요청 완료 (mock)');
    setRetryingId(null);
  };

  // -------------
  // 상세보기 모드
  // -------------

  if (detailBCode !== null && detailInfo) {
    return (
      <div className="flex flex-col gap-4">
        {/* 돌아가기 버튼 */}
        <button
          type="button"
          onClick={() => setDetailBCode(null)}
          className="text-muted-foreground hover:text-foreground flex items-center gap-1.5 self-start rounded-md px-3 py-1.5 text-xs font-medium transition-colors hover:bg-white"
        >
          <ArrowLeft className="h-3.5 w-3.5" />
          목록으로 돌아가기
        </button>

        <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
          {/* 방송 정보 요약 */}
          <div className="border-border border-b px-5 py-4">
            <h3 className="text-foreground mb-3 text-sm font-bold">{detailInfo.Title}</h3>
            <div className="grid grid-cols-4 gap-4 text-xs">
              <div>
                <span className="text-muted-foreground">방송유형</span>
                <p className="text-foreground mt-0.5 font-medium">
                  <span className="inline-block rounded-full bg-blue-50 px-2 py-0.5 text-blue-700 ring-1 ring-blue-200/60">
                    {BTYPE_LABELS[detailInfo.BType] ?? detailInfo.BType}
                  </span>
                </p>
              </div>
              <div>
                <span className="text-muted-foreground">방송일시</span>
                <p className="font-mono-data text-foreground mt-0.5">{detailInfo.BrdDate}</p>
              </div>
              <div>
                <span className="text-muted-foreground">반복횟수</span>
                <p className="text-foreground mt-0.5">{detailInfo.BRepeat}회</p>
              </div>
              <div>
                <span className="text-muted-foreground">방송내용</span>
                <p className="text-foreground mt-0.5 line-clamp-2">{detailInfo.TTSContent}</p>
              </div>
            </div>
          </div>

          {/* 장비별 상태 테이블 */}
          <Table>
            <colgroup>
              <col className="w-16" />
              <col />
              <col className="w-36" />
              <col className="w-24" />
              <col className="w-24" />
            </colgroup>
            <TableHeader>
              <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
                {['번호', '장비명', '전화번호', '상태', '재발송'].map((h) => (
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
              {detailItems.map((d, idx) => {
                const status = STATUS_CONFIG[d.BrdStatus] ?? STATUS_CONFIG.fail;
                const canRetry = d.BrdStatus === 'error' || d.BrdStatus === 'fail';
                return (
                  <TableRow
                    key={d.CD_DIST_OBSV}
                    className={cn(
                      'border-border/60 hover:bg-muted/20 transition-colors',
                      idx === detailItems.length - 1 && 'border-b-0'
                    )}
                  >
                    <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                      {d.CD_DIST_OBSV}
                    </TableCell>
                    <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                      {d.NM_DIST_OBSV}
                    </TableCell>
                    <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                      {d.ConnPhone}
                    </TableCell>
                    <TableCell className={cn('px-3 py-3 text-center text-xs font-semibold', status.color)}>
                      {status.label}
                    </TableCell>
                    <TableCell className="px-3 py-3 text-center">
                      {canRetry && (
                        <button
                          type="button"
                          onClick={() => handleRetry(d.CD_DIST_OBSV)}
                          disabled={retryingId === d.CD_DIST_OBSV}
                          className="text-muted-foreground hover:text-primary inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs transition-colors hover:bg-blue-50 disabled:opacity-50"
                        >
                          {retryingId === d.CD_DIST_OBSV ? (
                            <Loader2 className="h-3.5 w-3.5 animate-spin" />
                          ) : (
                            <RefreshCw className="h-3.5 w-3.5" />
                          )}
                          재발송
                        </button>
                      )}
                    </TableCell>
                  </TableRow>
                );
              })}
              {detailItems.length === 0 && (
                <TableRow>
                  <TableCell colSpan={5} className="text-muted-foreground py-10 text-center text-sm">
                    상세 데이터가 없습니다.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </div>
      </div>
    );
  }

  // -------------
  // 목록 모드
  // -------------
  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <Clock className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">방송 내역</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {history.length}건
          </span>
        </div>
        <div className="flex items-center gap-3">
          {/* 페이징 */}
          <div className="flex items-center gap-1.5">
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
          {/* 삭제 버튼 */}
          <button
            type="button"
            onClick={handleDelete}
            disabled={selectedIds.size === 0}
            className={cn(
              'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm transition-all active:scale-[0.98]',
              selectedIds.size > 0
                ? 'bg-red-500 text-white hover:bg-red-600'
                : 'bg-muted text-muted-foreground cursor-not-allowed opacity-50'
            )}
          >
            <Trash2 className="h-3.5 w-3.5" />
            {selectedIds.size > 0 ? `${selectedIds.size}건 삭제` : '삭제'}
          </button>
        </div>
      </div>

      <Table>
        <colgroup>
          <col className="w-12" />
          <col className="w-16" />
          <col />
          <col className="w-24" />
          <col className="w-44" />
          <col className="w-16" />
          <col className="w-16" />
          <col className="w-16" />
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
          {paged.map((item, idx) => {
            const isSelected = selectedIds.has(String(item.BCode));
            return (
              <TableRow
                key={item.BCode}
                onClick={() => openDetail(item)}
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
                      onCheckedChange={() => toggleOne(String(item.BCode))}
                      aria-label={`${item.Title} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {String(item.BCode).padStart(3, '0')}
                </TableCell>
                <TableCell className="text-foreground px-3 py-3 text-center text-sm font-semibold">
                  {item.Title}
                </TableCell>
                <TableCell className="px-3 py-3 text-center">
                  <span className="inline-block rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-blue-200/60">
                    {BTYPE_LABELS[item.BType] ?? item.BType}
                  </span>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {item.BrdDate}
                </TableCell>
                <TableCell className="font-mono-data px-3 py-3 text-center text-xs text-gray-500">
                  {item.standbyCount ?? 0}
                </TableCell>
                <TableCell className="font-mono-data px-3 py-3 text-center text-xs font-semibold text-emerald-600">
                  {item.successCount ?? 0}
                </TableCell>
                <TableCell className="font-mono-data px-3 py-3 text-center text-xs font-semibold text-red-500">
                  {item.failCount ?? 0}
                </TableCell>
              </TableRow>
            );
          })}
          {paged.length === 0 && (
            <TableRow>
              <TableCell colSpan={8} className="text-muted-foreground py-10 text-center text-sm">
                방송 내역이 없습니다.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>
    </div>
  );
}

export default BroadcastHistory;
