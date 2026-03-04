import { useState } from 'react';
import { Warehouse, Car, BarChart3, Server } from 'lucide-react';
import { cn } from '@/lib/utils';
import {
  MOCK_PARKING_GROUPS,
  MOCK_PARKING_GATES,
  MOCK_CAR_RECORDS,
  MOCK_CAR_STATS_DAY,
  MOCK_CAR_STATS_MONTH,
  MOCK_CAR_STATS_YEAR,
  // MOCK_SERVICE_PROCESS,
} from '@/mocks/mock';
import type { ParkingGroup } from '@/types/parking';
import { ParkingGroupManage } from '@/components/parking/ParkingGroupManage';
import { CarHistory } from '@/components/parking/CarHistory';
import { CarStats } from '@/components/parking/CarStats';
// import { ServiceStatus } from '@/components/parking/ServiceStatus';

type TabValue = 'group' | 'car' | 'stats';
// type TabValue = 'group' | 'car' | 'stats' | 'service';

const tabs: { value: TabValue; label: string; icon: typeof Warehouse }[] = [
  { value: 'group', label: '주차장관리', icon: Warehouse },
  { value: 'car', label: '입출차내역', icon: Car },
  { value: 'stats', label: '입출차통계', icon: BarChart3 },
  // { value: 'service', label: '서비스관리', icon: Server },
];

export function ParkingPage() {
  const [activeTab, setActiveTab] = useState<TabValue>('group');
  const [groups, setGroups] = useState<ParkingGroup[]>(MOCK_PARKING_GROUPS);

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">주차장 관리</h1>
          <p className="text-muted-foreground text-xs">주차장그룹, 입출차내역 및 통계 관리</p>
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
          {activeTab === 'group' && (
            <ParkingGroupManage groups={groups} gates={MOCK_PARKING_GATES} onGroupsChange={setGroups} />
          )}
          {activeTab === 'car' && <CarHistory records={MOCK_CAR_RECORDS} groups={groups} />}
          {activeTab === 'stats' && (
            <CarStats dayStats={MOCK_CAR_STATS_DAY} monthStats={MOCK_CAR_STATS_MONTH} yearStats={MOCK_CAR_STATS_YEAR} />
          )}
          {/* {activeTab === 'service' && <ServiceStatus process={MOCK_SERVICE_PROCESS} />} */}
        </div>
      </div>
    </div>
  );
}

export default ParkingPage;
