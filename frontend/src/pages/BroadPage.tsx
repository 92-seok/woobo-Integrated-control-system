import { useState } from 'react';
import { Radio, Clock, FileText, Users, Phone, Info, ChevronDown } from 'lucide-react';
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
  const [helpOpen, setHelpOpen] = useState(false);

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
                onClick={() => { setActiveTab(tab.value); setHelpOpen(false); }}
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

          {/* 도움말 */}
          <div>
            <button
              type="button"
              onClick={() => setHelpOpen((prev) => !prev)}
              className="text-muted-foreground hover:bg-muted hover:text-foreground flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium transition-all"
            >
              <Info className="h-3.5 w-3.5" />
              도움말 {helpOpen ? '닫기' : '보기'}
              <ChevronDown className={cn('h-3.5 w-3.5 transition-transform duration-300', helpOpen && 'rotate-180')} />
            </button>
            <div
              className={cn(
                'grid transition-all duration-300 ease-in-out',
                helpOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'
              )}
            >
              <div className="overflow-hidden">
                <ul className="mt-1 flex flex-col gap-1 rounded-md border border-blue-100 bg-blue-50/60 px-4 py-3 text-xs leading-relaxed text-blue-700/80">
                  {activeTab === 'send' && (
                    <>
                      <li className="font-semibold text-blue-700">◈ 장비 선택</li>
                      <li>- 방송을 발송할 장비를 체크박스로 선택합니다.</li>
                      <li>- 그룹 필터를 사용하여 특정 그룹의 장비만 표시할 수 있습니다.</li>
                      <li className="mt-1.5 font-semibold text-blue-700">◈ 방송 설정</li>
                      <li>- 제목, 발송유형(즉시/예약), 반복횟수, 방송타입(TTS/경보)을 설정합니다.</li>
                      <li>- 멘트 선택 시 저장된 멘트가 자동으로 입력됩니다.</li>
                    </>
                  )}
                  {activeTab === 'history' && (
                    <>
                      <li className="font-semibold text-blue-700">◈ 방송 내역 조회</li>
                      <li>- 발송된 방송 목록을 확인할 수 있습니다.</li>
                      <li>- 행을 클릭하면 장비별 상세 상태를 확인할 수 있습니다.</li>
                      <li className="mt-1.5 font-semibold text-blue-700">◈ 재발송</li>
                      <li>- 상세보기에서 오류/실패 장비에 대해 재발송이 가능합니다.</li>
                    </>
                  )}
                  {activeTab === 'ment' && (
                    <>
                      <li className="font-semibold text-blue-700">◈ 멘트 관리</li>
                      <li>- TTS 멘트와 예경보 멘트를 탭으로 구분하여 관리합니다.</li>
                      <li>- 멘트 추가 버튼으로 새 멘트를 등록할 수 있습니다.</li>
                      <li className="mt-1.5 font-semibold text-blue-700">◈ 멘트 수정/삭제</li>
                      <li>- 행을 클릭하면 해당 멘트를 수정할 수 있습니다.</li>
                      <li>- 체크박스 선택 후 삭제 버튼으로 일괄 삭제할 수 있습니다.</li>
                    </>
                  )}
                  {activeTab === 'group' && (
                    <>
                      <li className="font-semibold text-blue-700">◈ 그룹 관리</li>
                      <li>- 좌측에서 그룹을 추가, 수정, 삭제할 수 있습니다.</li>
                      <li>- 그룹명 옆 연필 아이콘을 클릭하여 이름을 수정합니다.</li>
                      <li className="mt-1.5 font-semibold text-blue-700">◈ 장비 매핑</li>
                      <li>- 좌측에서 그룹을 선택하면 우측에 장비 목록이 표시됩니다.</li>
                      <li>- 체크박스로 장비를 선택한 후 저장 버튼을 클릭합니다.</li>
                    </>
                  )}
                  {activeTab === 'cid' && (
                    <>
                      <li className="font-semibold text-blue-700">◈ CID 관리</li>
                      <li>- 각 장비에 발신번호(CID)를 등록하여 관리합니다.</li>
                      <li>- CID 등록 버튼으로 새 발신번호를 등록합니다.</li>
                      <li className="mt-1.5 font-semibold text-blue-700">◈ CID 삭제</li>
                      <li>- 체크박스 선택 후 삭제 버튼으로 일괄 삭제할 수 있습니다.</li>
                    </>
                  )}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default BroadPage;
