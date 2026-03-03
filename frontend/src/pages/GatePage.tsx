import { useState } from 'react';
import { ShieldCheck, Clock, Server } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_GATE_EQUIPMENTS, MOCK_GATE_STATUSES, MOCK_GATE_HISTORY } from '@/mocks/mock';
import type { GateEquipment, GateStatus } from '@/types/gate';
import { GateControl } from '@/components/gate/GateControl';
import { GateHistory } from '@/components/gate/GateHistory';
import { GateEquipManage } from '@/components/gate/GateEquipManage';

type TabValue = 'control' | 'history' | 'equip';

const tabs: { value: TabValue; label: string; icon: typeof ShieldCheck }[] = [
  { value: 'control', label: '차단기제어', icon: ShieldCheck },
  { value: 'history', label: '제어이력', icon: Clock },
  { value: 'equip', label: '장비관리', icon: Server },
];
export function GatePage() {
  const [activeTab, setActiveTab] = useState<TabValue>('control');

  // 상태 중앙관리
  const [equipments, setEquipments] = useState<GateEquipment[]>(MOCK_GATE_EQUIPMENTS);
  const [statuses, setStatuses] = useState<GateStatus[]>(MOCK_GATE_STATUSES);
  const [history] = useState(MOCK_GATE_HISTORY);

  // 차단기 상태 변경 핸들러
  const handleStatusChange = (code: string, gate: 'open' | 'close') => {
    setStatuses((prev) =>
      prev.map((s) =>
        s.CD_DIST_OBSV === code
          ? { ...s, Gate: gate, RegDate: new Date().toISOString().slice(0, 19).replace('T', ' ') }
          : s
      )
    );
    // TODO: API 연동 시 POST /api/gate { saveType: 'save', num: code, gate }
  };

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">차단기 관리</h1>
          <p className="text-muted-foreground text-xs">차단기 제어 및 장비 관리</p>
        </div>
        <div className="flex items-center gap-4">
          <div className="hidden items-center gap-2 sm:flex">
            <span
              className="h-2 w-2 rounded-full bg-emerald-500"
              style={{ animation: 'pulse-dot 2s ease-in-out infinite' }}
            />
            <span className="text-muted-foreground text-xs">시스템 정상</span>
          </div>
          <div className="bg-primary text-primary-foreground flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold shadow-sm">
            관
          </div>
        </div>
      </header>

      {/* 탭 네비게이션 */}
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
                {isActive && <span className="bg-primary absolute right-0 bottom-0 left-0 h-[2px] rounded-t-full" />}
              </button>
            );
          })}
        </nav>
      </div>

      {/* 콘텐츠 영역 */}
      <div className="bg-grid flex-1 overflow-y-auto p-6">
        <div className="animate-fade-in-up flex flex-col gap-5">
          {activeTab === 'control' && (
            <GateControl equipments={equipments} statuses={statuses} onStatusChange={handleStatusChange} />
          )}
          {activeTab === 'history' && <GateHistory history={history} />}
          {activeTab === 'equip' && <GateEquipManage equipments={equipments} onEquipmentsChange={setEquipments} />}
        </div>
      </div>
    </div>
  );
}

export default GatePage;
