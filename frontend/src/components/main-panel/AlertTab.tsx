import { useState, useEffect, useCallback } from 'react';
import { ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';

const SAMPLE_ALERTS = [
  { time: '2026.02.24 10:00', msg: '장비 점검 결과 - 점검 필요', color: '#d97706' },
  { time: '2026.02.24 10:00', msg: '장비 점검 결과 - 오류', color: '#ef4444' },
  { time: '2026.02.24 10:00', msg: '경보 발령 확인 결과 - 정상', color: '#111' },
];

const SAMPLE_RESULTS = `------------------------------------------------
------------------------------------------------
[2026.02.24 10:23] 점검 필요 항목

- 데모룸
- 데모룸

점검결과 점검 필요 2건

------------------------------------------------
------------------------------------------------

[2026.02.24 10:23] 자동 A/S 접수

- 데모룸
- 데모룸 K수위
- 데모룸 C수위
- 데모룸 침수
- 데모룸
- 데모룸
- 데모룸
- 데모룸
- 데모룸
- 데모룸

점검결과 오류 10건`;

export function AlertTab() {
  const [autoCheck, setAutoCheck] = useState(true);
  const [count, setCount] = useState(45);
  const [openSections, setOpenSections] = useState({ chk: true, result: true });

  useEffect(() => {
    if (!autoCheck) return;
    const timer = setInterval(() => {
      setCount((p) => (p <= 1 ? 59 : p - 1));
    }, 1000);
    return () => clearInterval(timer);
  }, [autoCheck]);

  const toggle = useCallback((key: 'chk' | 'result') => {
    setOpenSections((p) => ({ ...p, [key]: !p[key] }));
  }, []);

  return (
    <div className="flex flex-col gap-3 text-[12px]">
      {/* 자동 점검 체크박스 */}
      <label className="flex items-center justify-start gap-2 rounded bg-gray-50 px-3 py-2">
        <input
          type="checkbox"
          checked={autoCheck}
          onChange={(e) => setAutoCheck(e.target.checked)}
          className="accent-violet-600"
        />
        <span className="text-slate-700">
          1분 간격 자동 점검 <span className="text-slate-400">({count})</span>
        </span>
      </label>

      {/* 자동점검현황(실시간) */}
      <div>
        <div className="flex w-full items-center justify-between border-b-2 border-violet-500">
          <button
            type="button"
            onClick={() => toggle('chk')}
            className="flex items-center gap-1 py-2 text-left text-[13px] font-bold text-violet-600"
          >
            <ChevronDown
              className={cn('h-3.5 w-3.5 transition-transform duration-200', !openSections.chk && '-rotate-90')}
            />
            자동점검현황(실시간)
          </button>
        </div>
        <div
          className={cn(
            'grid transition-[grid-template-rows] duration-200 ease-out',
            openSections.chk ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
          )}
        >
          <div className="overflow-hidden">
            <div className="flex flex-col gap-1 px-2 py-2">
              {SAMPLE_ALERTS.map((a, i) => (
                <p key={i} style={{ color: a.color }}>
                  [{a.time}] {a.msg}
                </p>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* 자동점검 특이사항 */}
      <div>
        <div className="flex w-full items-center justify-between border-b-2 border-violet-500">
          <button
            type="button"
            onClick={() => toggle('result')}
            className="flex items-center gap-1 py-2 text-left text-[13px] font-bold text-violet-600"
          >
            <ChevronDown
              className={cn('h-3.5 w-3.5 transition-transform duration-200', !openSections.result && '-rotate-90')}
            />
            자동점검 특이사항
          </button>
        </div>
        <div
          className={cn(
            'grid transition-[grid-template-rows] duration-200 ease-out',
            openSections.result ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
          )}
        >
          <div className="overflow-hidden">
            <pre className="px-2 py-2 text-left text-[12px] leading-relaxed whitespace-pre-wrap text-slate-600">
              {SAMPLE_RESULTS}
            </pre>
          </div>
        </div>
      </div>
    </div>
  );
}

export default AlertTab;
