import { useState } from 'react';
import { createPortal } from 'react-dom';
import { Plus, Pencil, Trash2, Server } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { GateEquipment } from '@/types/gate';

interface GateEquipManageProps {
  equipments: GateEquipment[];
  onEquipmentsChange: (equipments: GateEquipment[]) => void;
  onRefresh: () => void;
}

type FormMode = 'idle' | 'add' | 'edit';

const STATUS_FORM: GateEquipment = {
  CD_DIST_OBSV: '',
  NM_DIST_OBSV: '',
  DTL_ADRES: '',
  LastDate: new Date(),
  ConnIP: '',
  ConnPort: '',
  Gate: '',
  Light: '',
  Sound: '',
  Status: '',
};

export function GateEquipManage({ equipments, onEquipmentsChange, onRefresh }: GateEquipManageProps) {
  const [mode, setMode] = useState<FormMode>('idle');
  const [form, setForm] = useState<GateEquipment>(STATUS_FORM);
  const [editCode, setEditCode] = useState<string | null>(null);

  // 입력 핸들러
  const updateField = (key: keyof GateEquipment, value: string) => {
    setForm((prev) => ({ ...prev, [key]: value }));
  };

  // 등록 시작
  const handleAddStart = () => {
    setMode('add');
    setForm(STATUS_FORM);
    setEditCode(null);
  };

  // 수정 시작
  const handleEditStart = (equip: GateEquipment) => {
    setMode('edit');
    setForm({ ...equip });
    setEditCode(equip.CD_DIST_OBSV);
  };

  // 저장 (등록/수정)
  const handleSave = () => {
    if (!form.NM_DIST_OBSV.trim()) {
      alert('차단기명과 IP는 필수 입력입니다.');
      return;
    }

    if (mode === 'add') {
      onEquipmentsChange([...equipments, form]);
      // TODO: API 연동 시 POST /api/gate { saveType: 'insert', ... }
    } else {
      onEquipmentsChange(equipments.map((e) => (e.CD_DIST_OBSV === editCode ? { ...form } : e)));
      // TODO: API 연동 시 POST /api/gate { saveType: 'update', ... }
    }

    setMode('idle');
    setForm(STATUS_FORM);
    setEditCode(null);
    alert('저장되었습니다.');
    onRefresh();
  };

  // 삭제 (비활성화)
  const handleDelete = (code: string, name: string) => {
    if (!confirm(`"${name}" 차단기를 삭제하시겠습니까?`)) return;
    onEquipmentsChange(equipments.filter((e) => e.CD_DIST_OBSV !== code));
    // TODO: API 연동 시 POST /api/gate { saveType: 'delete', num: code }
    alert('삭제되었습니다.');
    onRefresh();
  };

  // 취소
  const handleCancel = () => {
    setMode('idle');
    setForm(STATUS_FORM);
    setEditCode(null);
  };

  return (
    <div className="space-y-4">
      {/* 장비 목록 테이블 */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <div className="border-border bg-muted/40 flex items-center justify-between border-b px-5 py-3">
          <div className="flex items-center gap-2.5">
            <Server className="text-primary h-4 w-4" />
            <h3 className="text-sm font-semibold">등록된 차단기</h3>
            <span className="bg-primary text-primary-foreground rounded-full px-2.5 py-0.5 text-xs">
              {equipments.length}대
            </span>
          </div>
          <Button size="sm" onClick={handleAddStart}>
            <Plus className="mr-1.5 h-3.5 w-3.5" />
            차단기 등록
          </Button>
        </div>

        <Table className="table-fixed">
          <colgroup>
            <col className="w-12" />
            <col className="w-20" />
            <col />
            <col className="w-28" />
            <col />
            <col className="w-24" />
          </colgroup>
          <TableHeader>
            <TableRow className="bg-muted/30 hover:bg-muted/30">
              <TableHead className="px-3 py-2.5 text-center text-xs">No</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">코드</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">차단기명</TableHead>
              <TableHead className="hidden px-3 py-2.5 text-center text-xs lg:table-cell">IP</TableHead>
              <TableHead className="hidden px-3 py-2.5 text-center text-xs md:table-cell">주소</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">관리</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {equipments.map((equip, idx) => (
              <TableRow key={equip.CD_DIST_OBSV}>
                <TableCell className="px-3 py-3 text-center text-sm">{idx + 1}</TableCell>
                <TableCell className="px-3 py-3 text-center font-mono text-xs">{equip.CD_DIST_OBSV}</TableCell>
                <TableCell className="px-3 py-3 text-center font-medium">{equip.NM_DIST_OBSV}</TableCell>
                <TableCell className="px-3 py-3 text-center font-medium">{equip.ConnIP ?? '-'}</TableCell>
                <TableCell className="text-muted-foreground hidden px-3 py-3 text-center text-sm md:table-cell">
                  {equip.DTL_ADRES}
                </TableCell>
                <TableCell className="px-3 py-3 text-center">
                  <div className="flex items-center justify-center gap-1">
                    <Button variant="ghost" size="sm" onClick={() => handleEditStart(equip)} className="h-7 w-7 p-0">
                      <Pencil className="h-3.5 w-3.5" />
                    </Button>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => handleDelete(equip.CD_DIST_OBSV, equip.NM_DIST_OBSV)}
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
      </div>

      {/* 등록/수정 모달 */}
      {mode !== 'idle' &&
        createPortal(
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div className="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
              <h2 className="mb-1 text-sm font-semibold text-gray-800">
                {mode === 'add' ? '차단기 등록' : '차단기 수정'}
              </h2>
              <p className="text-muted-foreground mb-4 text-xs">
                {mode === 'add' ? '새로운 차단기 장비를 등록합니다.' : '차단기 장비 정보를 수정합니다.'}
              </p>

              <div className="flex flex-col gap-4">
                <div>
                  <label className="mb-1 block text-xs font-medium text-gray-600">차단기명 *</label>
                  <input
                    type="text"
                    value={form.NM_DIST_OBSV}
                    onChange={(e) => updateField('NM_DIST_OBSV', e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    placeholder="예: 강남1차단기"
                  />
                </div>
                <div>
                  <label className="mb-1 block text-xs font-medium text-gray-600">주소</label>
                  <input
                    type="text"
                    value={form.DTL_ADRES}
                    onChange={(e) => updateField('DTL_ADRES', e.target.value)}
                    className="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                    placeholder="서울시 강남구 역삼동 123-4"
                  />
                </div>
              </div>

              {/* 버튼 */}
              <div className="mt-5 flex justify-end gap-2">
                <Button variant="outline" size="sm" onClick={handleCancel}>
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
