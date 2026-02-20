import { useState, useCallback } from 'react';
import { Send, Info, FileText, ChevronDown, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { Recipient } from './RecipientTable';

const MAX_CHARS = 70;
const MAX_TITLE = 30;
const SMS_API_URL = '/api/sms';

interface MessageComposerProps {
  selectedRecipients: Recipient[];
}

export function MessageComposer({ selectedRecipients }: MessageComposerProps) {
  const [title, setTitle] = useState('');
  const [body, setBody] = useState('');
  // const [auth, setAuth] = useState('');
  const [helpOpen, setHelpOpen] = useState(false);
  const [isSending, setIsSending] = useState(false);

  const bodyCount = body.length;
  const bodyPercent = Math.min((bodyCount / MAX_CHARS) * 100, 100);
  const isNearLimit = bodyCount > MAX_CHARS * 0.8;
  const isAtLimit = bodyCount >= MAX_CHARS;

  const handleBodyChange = useCallback((e: React.ChangeEvent<HTMLTextAreaElement>) => {
    const v = e.target.value;
    if (v.length <= MAX_CHARS) setBody(v);
  }, []);

  const handleSend = useCallback(async () => {
    // 유효성 검사
    if (selectedRecipients.length === 0) {
      alert('수신자를 1명 이상 선택하세요.');
      return;
    }
    if (!title.trim()) {
      alert('제목을 입력하세요.');
      return;
    }
    if (!body.trim()) {
      alert('내용을 입력하세요.');
      return;
    }

    // API 요청 Payload 구성
    const payload = {
      title: title.trim(),
      PhoneNumber: selectedRecipients.map((r) => r.PhoneNumber),
      Message: body.trim(),
      Auth: '관리자',
    };

    setIsSending(true);

    try {
      const response = await fetch(SMS_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`서버 오류 (${response.status}): ${errorText}`);
      }

      alert(`발송 완료. ${selectedRecipients.length}명에게 문자를 발송했습니다.`);

      // 전송 성공 후 입력 초기화
      setTitle('');
      setBody('');
    } catch (err) {
      const message = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
      alert(`발송 실패: ${message}`);
    } finally {
      setIsSending(false);
    }
  }, [title, body, selectedRecipients]);

  return (
    <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* 카드 헤더 */}
      <div className="border-border bg-muted/40 flex items-center gap-2.5 border-b px-5 py-3">
        <FileText className="text-primary h-4 w-4" />
        <h3 className="font-jakarta text-foreground text-sm font-semibold">메세지 입력</h3>
        {/* 선택된 수신자 미리보기 */}
        {selectedRecipients.length > 0 && (
          <span className="text-muted-foreground text-xs">
            {selectedRecipients.map((r) => r.Name).join(', ')} ({selectedRecipients.length}명)
          </span>
        )}
      </div>

      <div className="flex flex-col gap-4 p-5">
        {/* 제목 */}
        <div className="flex flex-col gap-1.5">
          <div className="flex items-center justify-between">
            <label htmlFor="msg-title" className="text-muted-foreground text-xs font-semibold">
              제목
            </label>
            <span className="font-mono-data text-muted-foreground text-xs">
              {title.length} / {MAX_TITLE}
            </span>
          </div>
          <input
            id="msg-title"
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value.slice(0, MAX_TITLE))}
            placeholder="메세지 제목을 입력하세요"
            className="border-border bg-muted/30 text-foreground placeholder:text-muted-foreground/50 focus:border-primary/60 focus:ring-primary/10 h-10 w-full rounded-md border px-4 text-sm transition-all outline-none focus:bg-white focus:ring-2"
          />
        </div>

        {/* 내용 */}
        <div className="flex flex-col gap-1.5">
          <div className="flex items-center justify-between">
            <label htmlFor="msg-body" className="text-muted-foreground text-xs font-semibold">
              내용
            </label>
            <span
              className={cn(
                'font-mono-data text-xs tabular-nums transition-colors',
                isAtLimit
                  ? 'font-semibold text-red-500'
                  : isNearLimit
                    ? 'font-semibold text-amber-500'
                    : 'text-muted-foreground'
              )}
            >
              {bodyCount} / {MAX_CHARS}
            </span>
          </div>
          <textarea
            id="msg-body"
            value={body}
            onChange={handleBodyChange}
            placeholder="메세지 내용을 입력하세요 (70자 이내)"
            rows={5}
            className="border-border bg-muted/30 text-foreground placeholder:text-muted-foreground/50 focus:border-primary/60 focus:ring-primary/10 w-full resize-none rounded-md border px-4 py-3 text-sm leading-relaxed transition-all outline-none focus:bg-white focus:ring-2"
          />
          <div className="bg-muted h-1 w-full overflow-hidden rounded-full">
            <div
              className={cn(
                'h-full rounded-full transition-all duration-300',
                isAtLimit ? 'bg-red-500' : isNearLimit ? 'bg-amber-400' : 'bg-primary'
              )}
              style={{ width: `${bodyPercent}%` }}
            />
          </div>
        </div>

        {/* 도움말 + 전송 */}
        <div className="flex flex-col gap-2">
          <div className="flex items-center justify-between">
            <button
              type="button"
              onClick={() => setHelpOpen((prev) => !prev)}
              className="text-muted-foreground hover:bg-muted hover:text-foreground flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium transition-all"
            >
              <Info className="h-3.5 w-3.5" />
              도움말 {helpOpen ? '닫기' : '보기'}
              <ChevronDown className={cn('h-3.5 w-3.5 transition-transform duration-300', helpOpen && 'rotate-180')} />
            </button>

            {/* 전송 버튼 - 로딩 상태 포함 */}
            <button
              type="button"
              onClick={handleSend}
              disabled={isSending}
              className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 rounded-md px-5 py-2 text-sm font-semibold shadow-sm transition-all hover:shadow-md active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
            >
              {isSending ? (
                <>
                  <Loader2 className="h-4 w-4 animate-spin" />
                  전송 중...
                </>
              ) : (
                <>
                  <Send className="h-4 w-4" />
                  전송
                </>
              )}
            </button>
          </div>

          {/* 도움말 패널 */}
          <div
            className={cn(
              'grid transition-all duration-300 ease-in-out',
              helpOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'
            )}
          >
            <div className="overflow-hidden">
              <ul className="mt-1 flex flex-col gap-1 rounded-md border border-blue-100 bg-blue-50/60 px-4 py-3 text-xs leading-relaxed text-blue-700/80">
                <li className="font-semibold text-blue-700">◈ 수신자 선택</li>
                <li>- SMS를 전송받을 수신자를 선택합니다.</li>
                <li>- 수신자는 상단의 연락처관리에서 추가 또는 수정할 수 있습니다.</li>
                <li className="mt-1.5 font-semibold text-blue-700">◈ 메세지 입력</li>
                <li>- 보낼 문자의 제목과 내용을 입력합니다.</li>
                <li>- 제목은 상단의 발송내역에 기록됩니다.</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
