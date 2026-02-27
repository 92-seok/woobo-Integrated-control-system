import { useState, useCallback } from 'react';
import { Send, Loader2, FileText, Clock, Repeat, Volume2, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';
import { MOCK_TTS_MENTS, MOCK_ALERT_MENTS } from '../../mocks/mock';
import type { Equipment, MessageMent, SendBroadcastPayload } from '../../types/broad';

const MAX_CONTENT = 500;

interface BroadcastFormProps {
  selectedEquipments: Equipment[];
}

export function BroadcastForm({ selectedEquipments }: BroadcastFormProps) {
  const [title, setTitle] = useState('');
  const [tType, setTType] = useState<'general' | 'reserve'>('general');
  const [sDate, setSDate] = useState('');
  const [sTime, setSTime] = useState('00');
  const [sMin, setSMin] = useState('00');
  const [repeat, setRepeat] = useState(1);
  const [brdType, setBrdType] = useState<'tts' | 'alert'>('tts');
  const [selectedMent, setSelectedMent] = useState('');
  const [content, setContent] = useState('');
  const [isSending, setIsSending] = useState(false);

  // TODO: API 연동 시 fetch로 교체
  const mentList = brdType === 'tts' ? MOCK_TTS_MENTS : MOCK_ALERT_MENTS;

  const today = new Date().toISOString().split('T')[0];

  const handleMentChange = (mentCode: string) => {
    setSelectedMent(mentCode);
    if (!mentCode) {
      setContent('');
      return;
    }
    const found = mentList.find((m) => String(m.AltCode) === mentCode);
    setContent(found?.Content ?? '');
  };

  const handleSend = useCallback(async () => {
    if (selectedEquipments.length === 0) {
      alert('장비를 1대 이상 선택하세요.');
      return;
    }
    if (!title.trim()) {
      alert('방송 제목을 입력하세요.');
      return;
    }
    if (tType === 'reserve' && !sDate) {
      alert('예약 날짜를 선택하세요.');
      return;
    }
    if (!content.trim()) {
      alert('방송 내용을 입력하세요.');
      return;
    }

    const payload: SendBroadcastPayload = {
      equip: selectedEquipments.map((e) => e.CD_DIST_OBSV),
      title: title.trim(),
      tType,
      repeat,
      type: brdType,
      ment: selectedMent || undefined,
      content: content.trim(),
      ...(tType === 'reserve' && { sDate, sTime, sMin }),
    };

    setIsSending(true);
    console.log('방송 발송 payload:', payload);
    await new Promise((r) => setTimeout(r, 1000));

    alert(`방송 발송 완료! ${selectedEquipments.length}대 장비에 전송`);
    setTitle('');
    setContent('');
    setSelectedMent('');
    setIsSending(false);
  }, [title, content, tType, sDate, sTime, sMin, repeat, brdType, selectedMent, selectedEquipments]);

  const contentCount = content.length;
  const contentPercent = Math.min((contentCount / MAX_CONTENT) * 100, 100);
  const isNearLimit = contentCount > MAX_CONTENT * 0.8;
  const isAtLimit = contentCount >= MAX_CONTENT;

  return (
    <div className="border-border mt-5 overflow-hidden rounded-lg border bg-white shadow-sm">
      {/* ── 카드 헤더 ── */}
      <div className="border-border bg-muted/40 flex items-center gap-2.5 border-b px-5 py-3">
        <FileText className="text-primary h-4 w-4" />
        <h3 className="font-jakarta text-foreground text-sm font-semibold">방송 설정</h3>
        {selectedEquipments.length > 0 && (
          <span className="text-muted-foreground text-xs">
            {selectedEquipments.map((e) => e.NM_DIST_OBSV).join(', ')} ({selectedEquipments.length}대)
          </span>
        )}
      </div>

      {/* ── 제목 (전체 너비, 가장 먼저 눈에 들어옴) ── */}
      <div className="border-border border-b px-5 py-4">
        <div className="flex items-center gap-3">
          <label htmlFor="brd-title" className="text-muted-foreground shrink-0 text-xs font-semibold">
            제목
          </label>
          <input
            id="brd-title"
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value.slice(0, 50))}
            placeholder="방송 제목을 입력하세요"
            className="border-border bg-muted/30 text-foreground placeholder:text-muted-foreground/50 focus:border-primary/60 focus:ring-primary/10 h-9 flex-1 rounded-md border px-3 text-sm outline-none focus:bg-white focus:ring-2"
          />
          <span className="font-mono-data text-muted-foreground shrink-0 text-[11px]">{title.length}/50</span>
        </div>
      </div>

      {/* ── 본문: 좌측 옵션 + 우측 내용 ── */}
      <div className="grid grid-cols-[280px_1fr]">
        {/* ====== 좌측: 옵션 패널 ====== */}
        <div className="border-border flex flex-col gap-0 border-r">
          {/* 발송 유형 */}
          <div className="border-border border-b px-4 py-3">
            <div className="mb-2 flex items-center gap-1.5">
              <Clock className="text-muted-foreground h-3 w-3" />
              <span className="text-muted-foreground text-[11px] font-semibold tracking-wider uppercase">
                발송 유형
              </span>
            </div>
            <div className="flex gap-1.5">
              {(['general', 'reserve'] as const).map((t) => (
                <button
                  key={t}
                  type="button"
                  onClick={() => setTType(t)}
                  className={cn(
                    'flex-1 rounded-md py-2 text-xs font-semibold transition-all',
                    tType === t
                      ? t === 'reserve'
                        ? 'bg-amber-500 text-white shadow-sm'
                        : 'bg-primary text-white shadow-sm'
                      : 'bg-muted text-muted-foreground hover:bg-muted/80'
                  )}
                >
                  {t === 'general' ? '즉시' : '예약'}
                </button>
              ))}
            </div>
          </div>

          {/* 예약 설정 (예약일 때만) */}
          {tType === 'reserve' && (
            <div className="border-border border-b bg-amber-50/60 px-4 py-3">
              <input
                type="date"
                value={sDate}
                min={today}
                onChange={(e) => setSDate(e.target.value)}
                className="mb-2 h-8 w-full rounded border border-amber-200 bg-white px-2 text-xs outline-none focus:border-amber-400"
              />
              <div className="flex gap-1.5">
                <select
                  value={sTime}
                  onChange={(e) => setSTime(e.target.value)}
                  className="h-8 flex-1 rounded border border-amber-200 bg-white px-2 text-xs outline-none focus:border-amber-400"
                >
                  {Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0')).map((h) => (
                    <option key={h} value={h}>
                      {h}시
                    </option>
                  ))}
                </select>
                <select
                  value={sMin}
                  onChange={(e) => setSMin(e.target.value)}
                  className="h-8 flex-1 rounded border border-amber-200 bg-white px-2 text-xs outline-none focus:border-amber-400"
                >
                  {Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0')).map((m) => (
                    <option key={m} value={m}>
                      {m}분
                    </option>
                  ))}
                </select>
              </div>
            </div>
          )}

          {/* 반복횟수 */}
          <div className="border-border border-b px-4 py-3">
            <div className="mb-2 flex items-center gap-1.5">
              <Repeat className="text-muted-foreground h-3 w-3" />
              <span className="text-muted-foreground text-[11px] font-semibold tracking-wider uppercase">반복</span>
            </div>
            <div className="flex gap-1">
              {[1, 2, 3, 5, 9].map((n) => (
                <button
                  key={n}
                  type="button"
                  onClick={() => setRepeat(n)}
                  className={cn(
                    'flex-1 rounded py-1.5 text-xs font-semibold transition-all',
                    repeat === n
                      ? 'bg-primary text-white shadow-sm'
                      : 'bg-muted text-muted-foreground hover:bg-muted/80'
                  )}
                >
                  {n}회
                </button>
              ))}
            </div>
          </div>

          {/* 방송 타입 */}
          <div className="border-border border-b px-4 py-3">
            <div className="mb-2 flex items-center gap-1.5">
              <Volume2 className="text-muted-foreground h-3 w-3" />
              <span className="text-muted-foreground text-[11px] font-semibold tracking-wider uppercase">
                방송 타입
              </span>
            </div>
            <div className="flex gap-1.5">
              <button
                type="button"
                onClick={() => setBrdType('tts')}
                className={cn(
                  'flex flex-1 items-center justify-center gap-1.5 rounded-md py-2 text-xs font-semibold transition-all',
                  brdType === 'tts'
                    ? 'bg-primary text-white shadow-sm'
                    : 'bg-muted text-muted-foreground hover:bg-muted/80'
                )}
              >
                <Volume2 className="h-3 w-3" />
                TTS
              </button>
              <button
                type="button"
                onClick={() => setBrdType('alert')}
                className={cn(
                  'flex flex-1 items-center justify-center gap-1.5 rounded-md py-2 text-xs font-semibold transition-all',
                  brdType === 'alert'
                    ? 'bg-orange-500 text-white shadow-sm'
                    : 'bg-muted text-muted-foreground hover:bg-muted/80'
                )}
              >
                <AlertTriangle className="h-3 w-3" />
                경보
              </button>
            </div>
          </div>

          {/* 멘트 선택 */}
          <div className="px-4 py-3">
            <div className="mb-2 flex items-center gap-1.5">
              <FileText className="text-muted-foreground h-3 w-3" />
              <span className="text-muted-foreground text-[11px] font-semibold tracking-wider uppercase">
                멘트 선택
              </span>
            </div>
            <select
              value={selectedMent}
              onChange={(e) => handleMentChange(e.target.value)}
              className="border-border bg-muted/30 focus:border-primary/60 focus:ring-primary/10 h-8 w-full rounded-md border px-2 text-xs outline-none focus:bg-white focus:ring-2"
            >
              <option value="">직접 입력</option>
              {mentList.map((m) => (
                <option key={m.AltCode} value={m.AltCode}>
                  {m.Title}
                </option>
              ))}
            </select>
          </div>
        </div>

        {/* ====== 우측: 내용 작성 (메인 영역) ====== */}
        <div className="flex flex-col">
          <div className="flex flex-1 flex-col p-4">
            <div className="mb-2 flex items-center justify-between">
              <span className="text-muted-foreground text-[11px] font-semibold tracking-wider uppercase">
                방송 내용
              </span>
              <span
                className={cn(
                  'font-mono-data text-[11px] tabular-nums',
                  isAtLimit
                    ? 'font-bold text-red-500'
                    : isNearLimit
                      ? 'font-bold text-amber-500'
                      : 'text-muted-foreground'
                )}
              >
                {contentCount} / {MAX_CONTENT}
              </span>
            </div>
            <textarea
              value={content}
              onChange={(e) => {
                if (e.target.value.length <= MAX_CONTENT) setContent(e.target.value);
              }}
              placeholder="방송할 내용을 입력하세요..."
              className="border-border bg-muted/20 text-foreground placeholder:text-muted-foreground/40 focus:border-primary/40 focus:ring-primary/10 min-h-[200px] flex-1 resize-none rounded-lg border p-4 text-sm leading-relaxed transition-all outline-none focus:bg-white focus:ring-2"
            />
            {/* 프로그레스 바 */}
            <div className="bg-muted mt-2 h-1 w-full overflow-hidden rounded-full">
              <div
                className={cn(
                  'h-full rounded-full transition-all duration-300',
                  isAtLimit ? 'bg-red-500' : isNearLimit ? 'bg-amber-400' : 'bg-primary'
                )}
                style={{ width: `${contentPercent}%` }}
              />
            </div>
          </div>

          {/* 발송 버튼 */}
          <div className="border-border flex items-center justify-end border-t bg-gray-50/80 px-4 py-3">
            <button
              type="button"
              onClick={handleSend}
              disabled={isSending}
              className="bg-primary text-primary-foreground hover:bg-primary/90 flex items-center gap-2 rounded-lg px-6 py-2.5 text-sm font-bold shadow-sm transition-all hover:shadow-md active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-60"
            >
              {isSending ? (
                <>
                  <Loader2 className="h-4 w-4 animate-spin" />
                  발송 중...
                </>
              ) : (
                <>
                  <Send className="h-4 w-4" />
                  방송 발송
                </>
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default BroadcastForm;
