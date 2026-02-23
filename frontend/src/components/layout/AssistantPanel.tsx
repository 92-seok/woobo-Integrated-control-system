import { useState } from 'react';
import { BrainCircuit, ChevronLeft, X } from 'lucide-react';
import { cn } from '@/lib/utils';
import { RadarTab } from '../panel/RadarTab';

const TABS = [
  { id: 'radar', label: '위성영상' },
  { id: 'alert', label: '실시간현황' },
  { id: 'data', label: '계측현황' },
  { id: 'equip', label: '장비현황' },
  { id: 'as', label: 'A/S접수' },
];

interface AssistantPanelProps {
  open: boolean;
  onToggle: () => void;
}

export function AssistantPanel({ open, onToggle }: AssistantPanelProps) {
  const [activeTab, setActiveTab] = useState('radar');

  return (
    <>
      <div className="relative flex">
        {/* 패널 닫혔을 때: 열기 버튼 */}
        {!open && (
          <button
            type="button"
            onClick={onToggle}
            className="absolute top-4 -left-8 z-10 flex h-9 w-9 items-center justify-center rounded-l-lg border border-r-0 bg-white shadow-md hover:bg-gray-50"
          >
            <ChevronLeft className="h-4 w-4" />
          </button>
        )}

        {/* 패널 본체 */}
        <div
          className={cn(
            'flex flex-col border-l bg-white transition-all duration-300',
            open ? 'w-[360px]' : 'w-0 overflow-hidden'
          )}
        >
          {/* 헤더: 제목 + 닫기버튼 */}
          <div className="flex items-center justify-between border-b px-4 py-3">
            <div className="flex items-center gap-2">
              <BrainCircuit className="h-5 w-5 text-violet-500" />
              <span className="text-sm font-bold text-slate-700">지능형 어시스턴트</span>
            </div>
            <button
              type="button"
              onClick={onToggle}
              className="flex h-6 w-6 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600"
            >
              <X className="h-3 w-3" />
            </button>
          </div>

          {/* 탭 네비게이션 */}
          <div className="flex border-b">
            {TABS.map((tab) => (
              <button
                key={tab.id}
                type="button"
                onClick={() => setActiveTab(tab.id)}
                className={cn(
                  'flex-1 py-3 text-[12px] font-medium transition-all',
                  activeTab === tab.id
                    ? 'bg-gray-100 text-slate-800 shadow-[inset_0_2px_4px_rgba(0,0,0,0.12)]'
                    : 'text-gray-400 hover:bg-gray-50'
                )}
              >
                {tab.label}
              </button>
            ))}
          </div>

          {/* 탭 콘텐츠 */}
          <div className="flex-1 overflow-y-auto p-3">
            {activeTab === 'radar' && <RadarTab />}
            {activeTab === 'alert' && <p className="pt-8 text-center text-[11px] text-gray-400">실시간현황 영역</p>}
            {activeTab === 'data' && <p className="pt-8 text-center text-[11px] text-gray-400">계측현황 영역</p>}
            {activeTab === 'equip' && <p className="pt-8 text-center text-[11px] text-gray-400">장비현황 영역</p>}
            {activeTab === 'as' && <p className="pt-8 text-center text-[11px] text-gray-400">A/S접수 영역</p>}
          </div>
        </div>
      </div>
    </>
  );
}

export default AssistantPanel;
