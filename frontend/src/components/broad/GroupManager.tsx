import { useState } from 'react';
import { Checkbox } from '@/components/ui/checkbox';
import { Plus, Trash2, Users, Save, Pencil, X, Check } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { Equipment, BroadGroup } from '../../types/broad';

interface GroupManagerProps {
  groups: BroadGroup[];
  equipments: Equipment[];
  onGroupsChange: (groups: BroadGroup[]) => void;
}

export function GroupManager({ groups, equipments, onGroupsChange }: GroupManagerProps) {
  const [selectedGroup, setSelectedGroup] = useState<BroadGroup | null>(null);
  const [newGroupName, setNewGroupName] = useState('');
  const [mappedEquipIds, setMappedEquipIds] = useState<Set<string>>(new Set());

  // 그룹명 인라인 수정 상태
  const [editingCode, setEditingCode] = useState<number | null>(null);
  const [editingName, setEditingName] = useState('');

  // 룹 선택 → 우측에 장비 매핑 표시
  const handleSelectGroup = (group: BroadGroup) => {
    setSelectedGroup(group);
    const equipIds = group.BEquip ? group.BEquip.split(',').filter(Boolean) : [];
    setMappedEquipIds(new Set(equipIds));
  };

  // 그룹 추가
  const handleAddGroup = () => {
    if (!newGroupName.trim()) {
      alert('그룹명을 입력하세요');
      return;
    }
    const maxCode = Math.max(0, ...groups.map((g) => g.GCode));
    const newGroup: BroadGroup = {
      GCode: maxCode + 1,
      GName: newGroupName.trim(),
      BEquip: '',
    };
    onGroupsChange([...groups, newGroup]);
    setNewGroupName('');
    // API 연동 시 POST /api/broad/group
  };

  // 그룹 삭제
  const handleDeleteGroup = (gCode: number) => {
    if (!confirm('이 그룹을 삭제하시겠습니까?')) return;
    onGroupsChange(groups.filter((g) => g.GCode !== gCode));
    if (selectedGroup?.GCode === gCode) {
      setSelectedGroup(null);
      setMappedEquipIds(new Set());
    }
    // API 연동 시 DELETE /api/broad/group/:id
  };

  // 그룹명 수정 시작
  const startEditing = (group: BroadGroup) => {
    setEditingCode(group.GCode);
    setEditingName(group.GName);
  };

  // 그룹명 수정 저장
  const saveEditing = () => {
    if (!editingName.trim()) return;
    onGroupsChange(groups.map((g) => (g.GCode === editingCode ? { ...g, GName: editingName.trim() } : g)));
    setEditingCode(null);
    // API 연동 시 PUT /api/broad/group/:id
  };

  // 장비 매핑 저장
  const handleSaveMapping = () => {
    if (!selectedGroup) return;
    const newBEquip = Array.from(mappedEquipIds).join(',');
    onGroupsChange(groups.map((g) => (g.GCode === selectedGroup.GCode ? { ...g, BEquip: newBEquip } : g)));
    setSelectedGroup({ ...selectedGroup, BEquip: newBEquip });
    alert('장비 매핑이 저장되었습니다.');
    // API 연동 시 PUT /api/broad/group/:id/equip
  };

  // 장비 체크 토글
  const toggleEquip = (id: string) => {
    const next = new Set(mappedEquipIds);
    next.has(id) ? next.delete(id) : next.add(id);
    setMappedEquipIds(next);
  };

  return (
    <div className="grid grid-cols-2 gap-5">
      {/* ====== 좌측: 그룹 목록 ====== */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <div className="border-border bg-muted/40 flex h-14 items-center gap-2.5 border-b px-5">
          <Users className="text-primary h-4 w-4" />
          <h3 className="font-jakarta text-foreground text-sm font-semibold">그룹 목록</h3>
          <span className="bg-muted text-muted-foreground ring-border rounded-full px-2.5 py-0.5 text-xs font-medium ring-1">
            {groups.length}개
          </span>
        </div>

        <div className="flex flex-col">
          {groups.map((g) => (
            <div
              key={g.GCode}
              onClick={() => handleSelectGroup(g)}
              className={cn(
                'border-border flex cursor-pointer items-center justify-between border-b px-5 py-3 transition-colors',
                selectedGroup?.GCode === g.GCode ? 'bg-primary/5' : 'hover:bg-muted/30'
              )}
            >
              {/* 그룹명 (수정 모드 / 보기 모드) */}
              {editingCode === g.GCode ? (
                <div className="flex flex-1 items-center gap-1.5" onClick={(e) => e.stopPropagation()}>
                  <input
                    type="text"
                    value={editingName}
                    onChange={(e) => setEditingName(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && saveEditing()}
                    className="h-7 flex-1 rounded border border-blue-300 px-2 text-sm outline-none focus:border-blue-500"
                    autoFocus
                  />
                  <button
                    type="button"
                    onClick={saveEditing}
                    className="rounded p-1 text-emerald-600 hover:bg-emerald-50"
                  >
                    <Check className="h-3.5 w-3.5" />
                  </button>
                  <button
                    type="button"
                    onClick={() => setEditingCode(null)}
                    className="rounded p-1 text-gray-400 hover:bg-gray-100"
                  >
                    <X className="h-3.5 w-3.5" />
                  </button>
                </div>
              ) : (
                <div className="flex flex-1 items-center gap-2">
                  <span
                    className={cn(
                      'text-sm font-medium',
                      selectedGroup?.GCode === g.GCode ? 'text-primary' : 'text-foreground'
                    )}
                  >
                    {g.GName}
                  </span>
                  <span className="text-muted-foreground text-[11px]">
                    ({g.BEquip ? g.BEquip.split(',').filter(Boolean).length : 0}대)
                  </span>
                </div>
              )}

              {/* 수정/삭제 버튼 */}
              {editingCode !== g.GCode && (
                <div className="flex items-center gap-0.5" onClick={(e) => e.stopPropagation()}>
                  <button
                    type="button"
                    onClick={() => startEditing(g)}
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-blue-50 hover:text-blue-600"
                  >
                    <Pencil className="h-3 w-3" />
                  </button>
                  <button
                    type="button"
                    onClick={() => handleDeleteGroup(g.GCode)}
                    className="text-muted-foreground/50 rounded-md p-1.5 transition-all hover:bg-red-50 hover:text-red-500"
                  >
                    <Trash2 className="h-3 w-3" />
                  </button>
                </div>
              )}
            </div>
          ))}

          {/* 새 그룹 추가 */}
          <div className="flex items-center gap-2 px-5 py-3">
            <input
              type="text"
              value={newGroupName}
              onChange={(e) => setNewGroupName(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleAddGroup()}
              placeholder="새 그룹명 입력"
              className="border-border flex-1 rounded-md border px-3 py-2 text-sm outline-none focus:border-blue-500"
            />
            <button
              type="button"
              onClick={handleAddGroup}
              className="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md p-2 transition-all"
            >
              <Plus className="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      {/* ====== 우측: 장비 매핑 ====== */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <div className="border-border bg-muted/40 flex h-14 items-center justify-between border-b px-5">
          <h3 className="font-jakarta text-foreground text-sm font-semibold">
            {selectedGroup ? `${selectedGroup.GName} — 장비 매핑` : '그룹을 선택하세요'}
          </h3>
          {selectedGroup && (
            <button
              type="button"
              onClick={handleSaveMapping}
              className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-semibold shadow-sm"
            >
              <Save className="h-3.5 w-3.5" />
              저장
            </button>
          )}
        </div>

        {selectedGroup ? (
          <div className="flex flex-col">
            {equipments.map((e) => (
              <label
                key={e.CD_DIST_OBSV}
                className="border-border hover:bg-muted/30 flex cursor-pointer items-center gap-3 border-b px-5 py-3 transition-colors"
              >
                <Checkbox
                  checked={mappedEquipIds.has(e.CD_DIST_OBSV)}
                  onCheckedChange={() => toggleEquip(e.CD_DIST_OBSV)}
                />
                <span className="text-foreground text-sm font-medium">{e.NM_DIST_OBSV}</span>
                <span className="text-muted-foreground text-xs">({e.CD_DIST_OBSV})</span>
              </label>
            ))}
          </div>
        ) : (
          <div className="text-muted-foreground flex items-center justify-center py-20 text-sm">
            좌측에서 그룹을 선택하면 장비를 매핑할 수 있습니다.
          </div>
        )}
      </div>
    </div>
  );
}
