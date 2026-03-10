import { useState, useEffect } from 'react';
import { Monitor, Users } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { DisplayEquipment, DisplayScenario, DisplayMent, SaveScenarioPayload } from '@/types/display';
import { DisplayEquipList } from '@/components/display/DisplayEquipList';
import { ScenarioList } from '@/components/display/ScenarioList';
import { ScenarioForm } from '@/components/display/ScenarioForm';
import { GroupScenarioForm } from '@/components/display/GroupScenarioForm';

type TabValue = 'each' | 'group';

const tabs: { value: TabValue; label: string; icon: typeof Monitor }[] = [
  { value: 'each', label: '개별관리', icon: Monitor },
  { value: 'group', label: '그룹관리', icon: Users },
];

// 개별관리 뷰 상태
type EachView = 'list' | 'form';

export function DisplayPage() {
  const [activeTab, setActiveTab] = useState<TabValue>('each');

  // 데이터
  const [equipments, setEquipments] = useState<DisplayEquipment[]>([]);
  const [scenarios, setScenarios] = useState<DisplayScenario[]>([]);
  const [ments, setMents] = useState<DisplayMent[]>([]);

  // 개별관리 상태
  const [selectedEquipId, setSelectedEquipId] = useState<string>();
  const [eachView, setEachView] = useState<EachView>('list');
  const [editScenario, setEditScenario] = useState<DisplayScenario>();

  const selectedEquip = equipments.find((e) => e.CD_DIST_OBSV === selectedEquipId);

  // 장비 목록 조회
  useEffect(() => {
    const fetchEquipments = async () => {
      try {
        const res = await fetch('/api/display/status');
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data: DisplayEquipment[] = await res.json();
        setEquipments(data);
      } catch (err) {
        console.error('장비 조회 실패:', err);
      }
    };
    fetchEquipments();
  }, []);

  // 선택된 장비의 시나리오 조회
  useEffect(() => {
    if (!selectedEquipId) return;
    const fetchScenarios = async () => {
      try {
        const res = await fetch(`/api/display/scenario?cdDistObsv=${selectedEquipId}`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data: DisplayScenario[] = await res.json();
        setScenarios(data);
      } catch (err) {
        console.error('시나리오 조회 실패:', err);
      }
    };
    fetchScenarios();
  }, [selectedEquipId]);

  // 지정문구 조회
  useEffect(() => {
    const fetchMents = async () => {
      try {
        const res = await fetch('/api/display/ment');
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data: DisplayMent[] = await res.json();
        setMents(data);
      } catch (err) {
        console.error('지정문구 조회 실패:', err);
      }
    };
    fetchMents();
  }, []);

  // 장비 선택
  const handleSelectEquip = (cdDistObsv: string) => {
    setSelectedEquipId(cdDistObsv);
    setEachView('list');
    setEditScenario(undefined);
  };

  // 시나리오 저장 (개별/그룹 공통)
  const handleSave = async (payload: SaveScenarioPayload) => {
    try {
      const url = payload.cdDistObsv.includes(',') ? '/api/display/scenario/group' : '/api/display/scenario';
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      alert('저장되었습니다.');
      setEachView('list');
      setEditScenario(undefined);
      // 시나리오 목록 갱신
      if (selectedEquipId) {
        const r = await fetch(`/api/display/scenario?cdDistObsv=${selectedEquipId}`);
        if (r.ok) setScenarios(await r.json());
      }
    } catch (err) {
      const msg = err instanceof Error ? err.message : '알 수 없는 오류';
      alert(`저장 실패: ${msg}`);
    }
  };

  // 시나리오 삭제
  const handleDelete = async (disCodes: number[]) => {
    if (!confirm(`${disCodes.length}개의 시나리오를 삭제하시겠습니까?`)) return;
    try {
      const res = await fetch('/api/display/scenario', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'delete', disCodes }),
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      setScenarios((prev) => prev.filter((s) => !disCodes.includes(s.DisCode)));
    } catch (err) {
      console.error('삭제 실패:', err);
    }
  };

  // 시나리오 종료
  const handleEnd = async (disCode: number) => {
    if (!confirm('시나리오를 종료하시겠습니까?')) return;
    try {
      const res = await fetch('/api/display/scenario', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'end', disCode }),
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      setScenarios((prev) => prev.map((s) => (s.DisCode === disCode ? { ...s, Exp_YN: 'N' } : s)));
    } catch (err) {
      console.error('종료 실패:', err);
    }
  };

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">전광판 관리</h1>
          <p className="text-muted-foreground text-xs">전광판 시나리오 관리 및 제어</p>
        </div>
      </header>

      {/* 탭 */}
      <div className="border-border border-b bg-white px-6">
        <nav className="flex">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            const isActive = activeTab === tab.value;
            return (
              <button
                key={tab.value}
                type="button"
                onClick={() => setActiveTab(tab.value)}
                className={cn(
                  'relative flex items-center gap-2 px-4 py-3.5 text-sm font-medium transition-colors duration-150',
                  isActive ? 'text-primary' : 'text-muted-foreground hover:text-foreground'
                )}
              >
                <Icon className="h-4 w-4" />
                {tab.label}
                {isActive && <span className="bg-primary absolute right-0 bottom-0 left-0 h-0.5 rounded-t-full" />}
              </button>
            );
          })}
        </nav>
      </div>

      {/* 콘텐츠 */}
      <div className="bg-grid flex-1 overflow-y-auto p-6">
        <div className="animate-fade-in-up">
          {activeTab === 'each' && (
            <div className="flex gap-5">
              {/* 좌측: 장비 목록 */}
              <div className="w-1/3 flex-shrink-0">
                <DisplayEquipList equipments={equipments} onSelect={handleSelectEquip} selectedId={selectedEquipId} />
              </div>

              {/* 우측: 시나리오 리스트 or 폼 */}
              <div className="flex-1">
                {!selectedEquip ? (
                  <div className="flex h-40 items-center justify-center rounded-lg border bg-white text-sm text-gray-400">
                    좌측에서 전광판을 선택하세요
                  </div>
                ) : eachView === 'list' ? (
                  <ScenarioList
                    equipment={selectedEquip}
                    scenarios={scenarios}
                    onAddScenario={() => {
                      setEditScenario(undefined);
                      setEachView('form');
                    }}
                    onDeleteScenarios={handleDelete}
                    onEndScenario={handleEnd}
                    onEditScenario={(s) => {
                      setEditScenario(s);
                      setEachView('form');
                    }}
                  />
                ) : (
                  <ScenarioForm
                    equipment={selectedEquip}
                    scenario={editScenario}
                    ments={ments}
                    onSave={handleSave}
                    onCancel={() => {
                      setEachView('list');
                      setEditScenario(undefined);
                    }}
                  />
                )}
              </div>
            </div>
          )}

          {activeTab === 'group' && <GroupScenarioForm equipments={equipments} ments={ments} onSave={handleSave} />}
        </div>
      </div>
    </div>
  );
}
