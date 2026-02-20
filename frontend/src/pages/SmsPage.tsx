import { useState, useEffect } from 'react';
import { RecipientTable, type Recipient } from '@/components/sms/RecipientTable';
import { MessageComposer } from '@/components/sms/MessageComposer';
import { SendHistory } from '@/components/sms/SendHistory';
import { ContactsManager } from '@/components/sms/ContactsManager';
import { MessageSquare, Clock, BookUser } from 'lucide-react';
import { cn } from '@/lib/utils';

type TabValue = 'send' | 'history' | 'contacts';

const tabs: { value: TabValue; label: string; icon: typeof MessageSquare }[] = [
  { value: 'send', label: '문자발송', icon: MessageSquare },
  { value: 'history', label: '발송내역', icon: Clock },
  { value: 'contacts', label: '연락처관리', icon: BookUser },
];

export function SmsPage() {
  const [activeTab, setActiveTab] = useState<TabValue>('send');
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [recipients, setRecipients] = useState<Recipient[]>([]);

  const fetchRecipients = () => {
    fetch('/api/smsuser')
      .then((res) => res.json())
      .then((data) => setRecipients(data));
  };

  useEffect(() => {
    fetchRecipients();
  }, []);

  const selectedRecipients = recipients.filter((r) => selectedIds.has(String(r.Idx)));

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="border-border flex items-center justify-between border-b bg-white px-6 py-4 shadow-sm">
        <div>
          <h1 className="font-jakarta text-foreground text-base font-bold">SMS 관리</h1>
          <p className="text-muted-foreground text-xs">문자 발송 및 연락처 관리</p>
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
          {activeTab === 'send' && (
            <>
              <RecipientTable recipients={recipients} selectedIds={selectedIds} onSelectedChange={setSelectedIds} />
              <MessageComposer selectedRecipients={selectedRecipients} />
            </>
          )}
          {activeTab === 'history' && <SendHistory />}
          {activeTab === 'contacts' && <ContactsManager onRefresh={fetchRecipients} />}
        </div>
      </div>
    </div>
  );
}
