import { useState } from 'react';
import { createPortal } from 'react-dom';
import { Plus, Pencil, Trash2, ChevronLeft, ChevronRight, Warehouse } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { ParkingGroup, ParkingGate } from '@/types/parking';

interface ParkingGroupManageProps {
  groups: ParkingGroup[];
  gates: ParkingGate[];
  onGroupsChange: (groups: ParkingGroup[]) => void;
}

type FormMode = 'idle' | 'add' | 'edit';

const PER_PAGE = 10;

export function ParkingGroupManage({ groups, gates, onGroupsChange }: ParkingGroupManageProps) {
  const [mode, setMode] = useState<FormMode>('idle');
  const [editCode, setEditCode] = useState<number | null>(null);
  const [formName, setFormName] = useState('');
  const [formAddr, setFormAddr] = useState('');
  const [formGates, setFormGates] = useState<Set<string>>(new Set());
  const [page, setPage] = useState(1);

  // 페이지네이션
  const totalPages = Math.max(1, Math.ceil(groups.length / PER_PAGE));
  const safePage = Math.min(page, totalPages);
  const paged = groups.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  const openAdd = () => {
    setMode('add');
    setFormName('');
    setFormAddr('');
    setFormGates(new Set());
    setEditCode(null);
  };

  const openEdit = (group: ParkingGroup) => {
    setMode('edit');
    setFormName(group.ParkGroupName);
    setFormAddr(group.ParkGroupAddr);
    setFormGates(new Set(group.ParkJoinGate.split(',').filter(Boolean)));
    setEditCode(group.ParkGroupCode);
  };

  const handleSave = () => {
    if (!formName.trim()) {
      alert('주차장 이름은 필수 입력입니다.');
      return;
    }
    const joinGate = Array.from(formGates).join(',');

    if (mode === 'add') {
      const newCode = groups.length > 0 ? Math.max(...groups.map((g) => g.ParkGroupCode)) + 1 : 1;
      onGroupsChange([
        ...groups,
        { ParkGroupCode: newCode, ParkGroupName: formName, ParkGroupAddr: formAddr, ParkJoinGate: joinGate },
      ]);
    } else {
      onGroupsChange(
        groups.map((g) =>
          g.ParkGroupCode === editCode
            ? { ...g, ParkGroupName: formName, ParkGroupAddr: formAddr, ParkJoinGate: joinGate }
            : g
        )
      );
    }
    setMode('idle');
    alert('저장되었습니다.');
  };

  const handleDelete = (code: number, name: string) => {
    if (!confirm(`"${name}" 주차장그룹을 삭제하시겠습니까?`)) return;
    onGroupsChange(groups.filter((g) => g.ParkGroupCode !== code));
    alert('삭제되었습니다.');
  };

  const toggleGate = (code: string) => {
    setFormGates((prev) => {
      const next = new Set(prev);
      if (next.has(code)) next.delete(code);
      else next.add(code);
      return next;
    });
  };

  const toggleAllGates = (checked: boolean) => {
    setFormGates(checked ? new Set(gates.map((g) => g.CD_DIST_OBSV)) : new Set());
  };

  return (
    <div className="space-y-4">
      {/* 목록 테이블 */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <div className="border-border bg-muted/40 flex items-center justify-between border-b px-5 py-3">
          <div className="flex items-center gap-2.5">
            <Warehouse className="text-primary h-4 w-4" />
            <h3 className="text-sm font-semibold">주차장그룹</h3>
            <span className="bg-primary text-primary-foreground rounded-full px-2.5 py-0.5 text-xs">
              {groups.length}건
            </span>
          </div>
          <Button size="sm" onClick={openAdd}>
            <Plus className="mr-1.5 h-3.5 w-3.5" />
            추가
          </Button>
        </div>

        <Table className="table-fixed">
          <colgroup>
            <col className="w-12" />
            <col />
            <col />
            <col className="w-40" />
            <col className="w-20" />
          </colgroup>
          <TableHeader>
            <TableRow className="bg-muted/30 hover:bg-muted/30">
              <TableHead className="px-3 py-2.5 text-center text-xs">No</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">주차장</TableHead>
              <TableHead className="hidden px-3 py-2.5 text-center text-xs md:table-cell">주소</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">차단기</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">관리</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {paged.map((group, idx) => (
              <TableRow key={group.ParkGroupCode}>
                <TableCell className="px-3 py-3 text-center text-sm">{(safePage - 1) * PER_PAGE + idx + 1}</TableCell>
                <TableCell className="px-3 py-3 text-center font-medium">{group.ParkGroupName}</TableCell>
                <TableCell className="text-muted-foreground hidden px-3 py-3 text-center text-sm md:table-cell">
                  {group.ParkGroupAddr}
                </TableCell>
                <TableCell className="text-muted-foreground px-3 py-3 text-center font-mono text-xs">
                  {group.ParkJoinGate}
                </TableCell>
                <TableCell className="px-3 py-3 text-center">
                  <div className="flex items-center justify-center gap-1">
                    <Button variant="ghost" size="sm" onClick={() => openEdit(group)} className="h-7 w-7 p-0">
                      <Pencil className="h-3.5 w-3.5" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(group.ParkGroupCode, group.ParkGroupName)}
                      className="h-7 w-7 p-0 text-red-500 hover:text-red-600"
                    >
                      <Trash2 className="h-3.5 w-3.5" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>

        {/* 페이지네이션 */}
        {totalPages > 1 && (
          <div className="border-border flex items-center justify-center gap-1 border-t px-4 py-3">
            <Button variant="ghost" size="sm" disabled={safePage === 1} onClick={() => setPage(safePage - 1)}>
              <ChevronLeft className="h-4 w-4" />
            </Button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
              <Button
                key={p}
                variant={p === safePage ? 'default' : 'ghost'}
                size="sm"
                onClick={() => setPage(p)}
                className="w-8"
              >
                {p}
              </Button>
            ))}
            <Button variant="ghost" size="sm" disabled={safePage === totalPages} onClick={() => setPage(safePage + 1)}>
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
        )}
      </div>

      {/* 등록/수정 모달 */}
      {mode !== 'idle' &&
        createPortal(
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
              <h2 className="mb-1 text-sm font-semibold text-gray-800">
                {mode === 'add' ? '주차장그룹 등록' : '주차장그룹 수정'}
              </h2>
              <p className="text-muted-foreground mb-4 text-xs">주차장 이름, 주소, 연결 차단기를 설정합니다.</p>

              <div className="flex flex-col gap-4">
                <div>
                  <label className="mb-1 block text-xs font-medium text-gray-600">주차장그룹 이름 *</label>
                  <input
                    type="text"
                    value={formName}
                    onChange={(e) => setFormName(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    placeholder="예: 우보주차장 A"
                  />
                </div>

                <div>
                  <label className="mb-1 block text-xs font-medium text-gray-600">주소</label>
                  <input
                    type="text"
                    value={formAddr}
                    onChange={(e) => setFormAddr(e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    placeholder="주소를 입력하세요"
                  />
                </div>

                {/* 차단기 선택 (parkingCareAdd.php 체크박스 목록 대응) */}
                <div className="rounded-md border border-gray-200 bg-gray-50/50 p-3">
                  <div className="mb-2 flex items-center justify-between">
                    <span className="text-xs font-semibold text-gray-500">차단기 선택</span>
                    <label className="flex items-center gap-1.5 text-xs text-gray-500">
                      <Checkbox
                        checked={formGates.size === gates.length && gates.length > 0}
                        onCheckedChange={(checked) => toggleAllGates(!!checked)}
                      />
                      전체선택
                    </label>
                  </div>
                  <Table>
                    <TableHeader>
                      <TableRow className="bg-muted/30 hover:bg-muted/30">
                        <TableHead className="w-10 px-2 py-1.5 text-center text-xs">선택</TableHead>
                        <TableHead className="px-2 py-1.5 text-xs">차단기명</TableHead>
                        <TableHead className="hidden px-2 py-1.5 text-xs md:table-cell">주소</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {gates.map((gate) => (
                        <TableRow key={gate.CD_DIST_OBSV}>
                          <TableCell className="px-2 py-1.5 text-center">
                            <Checkbox
                              checked={formGates.has(gate.CD_DIST_OBSV)}
                              onCheckedChange={() => toggleGate(gate.CD_DIST_OBSV)}
                            />
                          </TableCell>
                          <TableCell className="px-2 py-1.5 text-sm">{gate.NM_DIST_OBSV}</TableCell>
                          <TableCell className="text-muted-foreground hidden px-2 py-1.5 text-sm md:table-cell">
                            {gate.DTL_ADRES}
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </div>

              <div className="mt-5 flex justify-end gap-2">
                <Button variant="outline" size="sm" onClick={() => setMode('idle')}>
                  취소
                </Button>
                <Button size="sm" onClick={handleSave}>
                  {mode === 'add' ? '등록' : '수정'}
                </Button>
              </div>
            </div>
          </div>,
          document.body
        )}
    </div>
  );
}

export default ParkingGroupManage;
