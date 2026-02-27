import { useState } from 'react';
import { Radio, Clock, FileText, Users, Phone } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_EQUIPMENTS, MOCK_GROUPS } from '@/mocks/mock';
import type { Equipment, BroadGroup } from '@/types/broad';
import { EquipmentSelector } from '@/components/broad/EquipmentSelector';
import { BroadcastForm } from '@/components/broad/BroadcastForm';
import { BroadcastHistory } from '@/components/broad/BroadcastHistory';
import { MessageTemplate } from '@/components/broad/MessageTemplate';
import { GroupManager } from '@/components/broad/GroupManager';
import CidManager from '@/components/broad/CidManager';

// 탭 정의
type TabValue = 'send' | 'history' | 'ment' | 'group' | 'cid';

const tabs: { value: TabValue; label: string; icon: typeof Radio }[] = [
  { value: 'send', label: '방송발송', icon: Radio },
  { value: 'history', label: '방송내역', icon: Clock },
  { value: 'ment', label: '멘트관리', icon: FileText },
  { value: 'group', label: '그룹관리', icon: Users },
  { value: 'cid', label: 'CID관리', icon: Phone },
];

export function BroadPage() {
  const [activeTab, setActiveTab] = useState<TabValue>('send');

  // 상태 중앙관리
  const [equipments] = useState<Equipment[]>(MOCK_EQUIPMENTS);
  const [groups, setGroups] = useState<BroadGroup[]>(MOCK_GROUPS);
  const [selectedEquipIds, setSelectedEquipIds] = useState<Set<string>>(new Set());

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">예경보 관리</h1>
          <p className="text-muted-foreground text-xs">예경보 발송 및 그룹/멘트 관리</p>
        </div>
        <div className="flex items-center gap-4">
          <div className="hidden items-center gap-2 sm:flex">
            <span
              className="h-2 w-2 rounded-full bg-emerald-500"
              style={{ animation: 'pulse-dot 2s ease-in-out infinite' }}
            />
            <span className="text-muted-foreground text-xs">시스템 정상</span>
          </div>
          <div className="bg-primary text-primary-foreground font-blod flex h-8 w-8 items-center justify-center rounded-full text-xs shadow-sm">
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
        <div className="animate-fade-in-up flex-flex-col gap-5">
          {activeTab === 'send' && (
            <>
              <EquipmentSelector
                equipments={equipments}
                groups={groups}
                selectedIds={selectedEquipIds}
                onSelectedChange={setSelectedEquipIds}
              />
              <BroadcastForm selectedEquipments={equipments.filter((e) => selectedEquipIds.has(e.CD_DIST_OBSV))} />
            </>
          )}
          {activeTab === 'history' && <BroadcastHistory />}
          {activeTab === 'ment' && <MessageTemplate />}
          {activeTab === 'group' && <GroupManager groups={groups} equipments={equipments} onGroupsChange={setGroups} />}
          {activeTab === 'cid' && <CidManager equipments={equipments} />}
        </div>
      </div>
    </div>
  );
}

export default BroadPage;
