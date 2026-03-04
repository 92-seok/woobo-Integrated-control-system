import { useState } from 'react';
import { FileText, AlertTriangle, Download, RefreshCw } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { DailyReport } from '@/components/report/DailyReport';
import { AlertHistory } from '@/components/report/AlertHistory';
import {
  MOCK_ACTIVE_OBSERVATIONS,
  MOCK_WATER_LEVEL,
  MOCK_RAINFALL,
  MOCK_DISPLACEMENT,
  MOCK_SOIL_MOISTURE,
  MOCK_TILT,
  MOCK_SNOW,
  MOCK_FLOOD,
  MOCK_ALERT_STATUS,
} from '@/mocks/mock';

type TabValue = 'daily' | 'alert';

const tabs: { value: TabValue; label: string; icon: typeof FileText }[] = [
  { value: 'daily', label: '일일현황', icon: FileText },
  { value: 'alert', label: '경보현황', icon: AlertTriangle },
];

export function ReportPage() {
  const [activeTab, setActiveTab] = useState<TabValue>('daily');

  const handleExcelDownload = () => {
    // TODO: API 연동 시 reportExcel.php 호출 또는 프론트 엑셀 생성
    alert('엑셀 다운로드 기능은 API 연동 후 사용 가능합니다.');
  };

  const handleRefresh = () => {
    window.location.reload();
  };

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">보고서</h1>
          <p className="text-muted-foreground text-xs">일일현황 및 경보이력 보고서 관리</p>
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
          {activeTab === 'daily' && (
            <>
              <DailyReport
                activeObservations={MOCK_ACTIVE_OBSERVATIONS}
                waterLevel={MOCK_WATER_LEVEL}
                rainfall={MOCK_RAINFALL}
                displacement={MOCK_DISPLACEMENT}
                soilMoisture={MOCK_SOIL_MOISTURE}
                tilt={MOCK_TILT}
                snow={MOCK_SNOW}
                flood={MOCK_FLOOD}
              />
              <div className="mx-auto flex max-w-3xl items-center justify-center gap-3">
                <Button size="sm" className="bg-sky-500 text-white hover:bg-sky-600" onClick={handleExcelDownload}>
                  <Download className="h-3.5 w-3.5" />
                  엑셀다운
                </Button>
                <Button size="sm" className="bg-slate-500 text-white hover:bg-slate-600" onClick={handleRefresh}>
                  <RefreshCw className="h-3.5 w-3.5" />
                  새로고침
                </Button>
              </div>
            </>
          )}
          {activeTab === 'alert' && <AlertHistory alerts={MOCK_ALERT_STATUS} />}
        </div>
      </div>
    </div>
  );
}

export default ReportPage;
