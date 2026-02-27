import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { Radio, Search, ChevronLeft, ChevronRight } from 'lucide-react';
import type { Equipment, BroadGroup } from '../../types/broad';

interface EquipmentSelectorProps {
  equipments: Equipment[];
  groups: BroadGroup[];
  selectedIds: Set<string>;
  onSelectedChange: (ids: Set<string>) => void;
}

const PER_PAGE = 5; // 한 페이지당 표시 개수

const tableHeaders = ['번호', '장비명', '주소명', '전화번호', '상태'];

const statusBadge: Record<string, { label: string; className: string }> = {
  OK: { label: '정상', className: 'bg-blue-50 text-blue-700 ring-blue-200/60' },
  ING: { label: '연결중', className: 'bg-amber-50 text-amber-700 ring-amber-200/60' },
  FAIL: { label: '오류', className: 'bg-red-50 text-red-700 ring-red-200/60' },
};

export function EquipmentSelector({ equipments, groups, selectedIds, onSelectedChange }: EquipmentSelectorProps) {
  const [searchQuery, setSearchQuery] = useState('');
  const [activeGroup, setActiveGroup] = useState<string>('all');
  const [currentPage, setCurrentPage] = useState(1);

  // 그룹 필터
  const groupFiltered =
    activeGroup === 'all'
      ? equipments
      : equipments.filter((e) => {
          const group = groups.find((g) => String(g.GCode) === activeGroup);
          return group?.BEquip.split(',').includes(e.CD_DIST_OBSV);
        });

  // 검색 필터
  const filtered = groupFiltered.filter(
    (e) =>
      e.NM_DIST_OBSV.includes(searchQuery) ||
      e.ConnPhone.includes(searchQuery) ||
      e.CD_DIST_OBSV.includes(searchQuery) ||
      e.DSCODE.includes(searchQuery)
  );

  // 페이징 계산
  const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
  const safePage = Math.min(currentPage, totalPages);
  const paged = filtered.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  // 그룹/검색 변경 시 1페이지로 리셋
  const handleGroupChange = (value: string) => {
    setActiveGroup(value);
    setCurrentPage(1);
  };
  const handleSearchChange = (value: string) => {
    setSearchQuery(value);
    setCurrentPage(1);
  };

  // 전체선택: 현재 페이지가 아닌 "필터된 전체" 기준
  // → 페이지 이동해도 선택 유지됨
  const allFilteredSelected = filtered.length > 0 && filtered.every((e) => selectedIds.has(e.CD_DIST_OBSV));
  const someFilteredSelected = filtered.some((e) => selectedIds.has(e.CD_DIST_OBSV));

  const toggleAll = (checked: boolean | 'indeterminate') => {
    const next = new Set(selectedIds);
    if (checked === true) {
      // 필터된 전체 장비를 추가 (기존 다른 그룹 선택도 유지)
      filtered.forEach((e) => next.add(e.CD_DIST_OBSV));
    } else {
      // 필터된 장비만 제거 (다른 그룹 선택은 유지)
      filtered.forEach((e) => next.delete(e.CD_DIST_OBSV));
    }
    onSelectedChange(next);
  };

  const toggleOne = (id: string) => {
    const next = new Set(selectedIds);
    next.has(id) ? next.delete(id) : next.add(id);
    onSelectedChange(next);
  };

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
        <div className="flex items-center gap-2.5">
          <Radio className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">장비 선택</h3>
          {selectedIds.size > 0 && (
            <span className="bg-primary text-primary-foreground rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm">
              {selectedIds.size}대 선택
            </span>
          )}
        </div>
        <div className="relative">
          <Search className="text-muted-foreground absolute top-1/2 left-2.5 h-3.5 w-3.5 -translate-y-1/2" />
          <input
            type="text"
            placeholder="장비명, 주소, 전화번호 검색..."
            value={searchQuery}
            onChange={(e) => handleSearchChange(e.target.value)}
            className="border-border text-foreground placeholder:text-muted-foreground/60 focus:border-primary/60 focus:ring-primary/10 h-8 w-56 rounded-md border bg-white pr-3 pl-8 text-xs transition-colors outline-none focus:ring-2"
          />
        </div>
      </div>

      {/* 그룹 필터 + 페이징 */}
      <div className="border-border flex items-center justify-between border-b px-5 py-2.5">
        {/* 좌측: 그룹 버튼 */}
        <div className="flex flex-wrap items-center gap-2">
          <span className="text-muted-foreground text-xs font-medium">그룹:</span>
          {[{ value: 'all', label: '전체' }, ...groups.map((g) => ({ value: String(g.GCode), label: g.GName }))].map(
            (item) => (
              <button
                key={item.value}
                type="button"
                onClick={() => handleGroupChange(item.value)}
                className={cn(
                  'rounded-full px-3 py-1 text-xs font-medium transition-colors',
                  activeGroup === item.value
                    ? 'bg-primary text-primary-foreground'
                    : 'bg-muted text-muted-foreground hover:bg-muted/80'
                )}
              >
                {item.label}
              </button>
            )
          )}
        </div>

        {/* 우측: 페이징 */}
        <div className="flex items-center gap-1.5">
          <span className="text-muted-foreground mr-1 text-[11px]">
            {filtered.length}건 중 {(safePage - 1) * PER_PAGE + 1}-{Math.min(safePage * PER_PAGE, filtered.length)}
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

      {/* 테이블 */}
      <Table className="table-fixed">
        <colgroup>
          <col className="w-12" />
          <col className="w-14" />
          <col />
          <col />
          <col className="w-36" />
          <col className="w-20" />
        </colgroup>
        <TableHeader>
          <TableRow className="border-border bg-muted/30 hover:bg-muted/30">
            <TableHead className="px-3 py-2.5 text-center">
              <div className="flex items-center justify-center">
                <Checkbox
                  checked={allFilteredSelected ? true : someFilteredSelected ? 'indeterminate' : false}
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
          {paged.map((equip, idx) => {
            const isSelected = selectedIds.has(equip.CD_DIST_OBSV);
            const badge = statusBadge[equip.LastStatus] ?? statusBadge.FAIL;
            return (
              <TableRow
                key={equip.CD_DIST_OBSV}
                onClick={() => toggleOne(equip.CD_DIST_OBSV)}
                className={cn(
                  'border-border/60 cursor-pointer transition-colors',
                  isSelected ? 'bg-primary/5 hover:bg-primary/8' : 'hover:bg-muted/30',
                  idx === paged.length - 1 && 'border-b-0'
                )}
              >
                <TableCell className="px-3 py-3 text-center" onClick={(e) => e.stopPropagation()}>
                  <div className="flex items-center justify-center">
                    <Checkbox
                      checked={isSelected}
                      onCheckedChange={() => toggleOne(equip.CD_DIST_OBSV)}
                      aria-label={`${equip.NM_DIST_OBSV} 선택`}
                    />
                  </div>
                </TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {equip.CD_DIST_OBSV}
                </TableCell>
                <TableCell
                  className={cn(
                    'px-3 py-3 text-center text-sm font-semibold',
                    isSelected ? 'text-primary' : 'text-foreground'
                  )}
                >
                  {equip.NM_DIST_OBSV}
                </TableCell>
                {/* 주소명 컬럼 추가 */}
                <TableCell className="text-muted-foreground px-3 py-3 text-center text-xs">{equip.DSCODE}</TableCell>
                <TableCell className="font-mono-data text-muted-foreground px-3 py-3 text-center text-xs">
                  {equip.ConnPhone}
                </TableCell>
                <TableCell className="px-3 py-3 text-center">
                  <span
                    className={cn(
                      'inline-block rounded-full px-2.5 py-0.5 text-xs font-medium ring-1',
                      badge.className
                    )}
                  >
                    {badge.label}
                  </span>
                </TableCell>
              </TableRow>
            );
          })}
          {paged.length === 0 && (
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

export default EquipmentSelector;
