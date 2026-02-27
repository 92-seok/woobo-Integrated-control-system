import { useState } from 'react';
import { ChevronDown, X } from 'lucide-react';
import { cn } from '@/lib/utils';

interface AsItem {
  equip: string;
  content: string;
}

const LARGE_OPTIONS = ['대분류 선택', '계측센서', '제어장비'];
const MIDDLE_MAP: Record<string, string[]> = {
  계측센서: ['중분류 선택', '강우', '수위', '변위', '침수', '적설'],
  제어장비: ['중분류 선택', '방송', '전광판', '차단기'],
};
const CONTENT_OPTIONS = ['접수내용 선택', '장비상태 오류', '장비제어 오류', '데이터값 오류', '직접입력'];

export function AsTab() {
  const [large, setLarge] = useState('');
  const [middle, setMiddle] = useState('');
  const [equip, setEquip] = useState('');
  const [content, setContent] = useState('');
  const [list, setList] = useState<AsItem[]>([]);
  const [phone, setPhone] = useState('010-0000-0000');
  const [phoneChk, setPhoneChk] = useState(true);
  const [sender, setSender] = useState('유지보수업체');
  const [openSections, setOpenSections] = useState({ content: true, list: true, way: true });

  const toggle = (key: 'content' | 'list' | 'way') => {
    setOpenSections((p) => ({ ...p, [key]: !p[key] }));
  };

  const handleAdd = () => {
    if (!middle || middle === '중분류 선택') return;
    const equipName = equip || middle;
    const contentText = content && content !== '접수내용 선택' ? content : '장비상태 오류';
    setList((p) => [...p, { equip: equipName, content: contentText }]);
  };

  const handleRemove = (idx: number) => {
    setList((p) => p.filter((_, i) => i !== idx));
  };

  const handleSubmit = () => {
    if (list.length === 0) return alert('접수할 장비를 추가해주세요.');
    alert(`${list.length}건 접수 완료`);
    setList([]);
  };

  const selectClass =
    'w-full rounded border border-gray-200 bg-white px-3 py-2 text-[12px] text-slate-700 outline-none transition-colors focus:border-violet-400';

  return (
    <div className="flex flex-col gap-3 text-[12px]">
      {/* ── 접수내용 ── */}
      <div>
        <div className="flex w-full items-center justify-between border-b-2 border-violet-500">
          <button
            type="button"
            onClick={() => toggle('content')}
            className="flex items-center gap-1 py-2 text-left text-[13px] font-bold text-violet-600"
          >
            <ChevronDown
              className={cn('h-3.5 w-3.5 transition-transform duration-200', !openSections.content && '-rotate-90')}
            />
            접수내용
          </button>
        </div>
        <div
          className={cn(
            'grid transition-[grid-template-rows] duration-200 ease-out',
            openSections.content ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
          )}
        >
          <div className="overflow-hidden">
            <div className="flex flex-col gap-2 py-2">
              <p className="text-[11px] font-medium text-slate-500">장비선택</p>
              <select
                className={selectClass}
                value={large}
                onChange={(e) => {
                  setLarge(e.target.value);
                  setMiddle('');
                  setEquip('');
                }}
              >
                {LARGE_OPTIONS.map((o) => (
                  <option key={o} value={o === '대분류 선택' ? '' : o}>
                    {o}
                  </option>
                ))}
              </select>
              <select
                className={selectClass}
                value={middle}
                onChange={(e) => {
                  setMiddle(e.target.value);
                  setEquip('');
                }}
                disabled={!large}
              >
                {(large ? MIDDLE_MAP[large] || ['중분류 선택'] : ['중분류 선택']).map((o) => (
                  <option key={o} value={o === '중분류 선택' ? '' : o}>
                    {o}
                  </option>
                ))}
              </select>
              <select className={selectClass} value={equip} onChange={(e) => setEquip(e.target.value)} disabled={!middle}>
                <option value="">소분류 선택</option>
              </select>

              <p className="mt-2 text-[11px] font-medium text-slate-500">접수내용</p>
              <select className={selectClass} value={content} onChange={(e) => setContent(e.target.value)}>
                {CONTENT_OPTIONS.map((o) => (
                  <option key={o} value={o === '접수내용 선택' ? '' : o}>
                    {o}
                  </option>
                ))}
              </select>

              <button
                type="button"
                onClick={handleAdd}
                className="mx-auto mt-1 rounded bg-violet-600 px-6 py-1.5 text-[12px] font-medium text-white transition-colors hover:bg-violet-700"
              >
                추가하기
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* ── 접수장비 ── */}
      <div>
        <div className="flex w-full items-center justify-between border-b-2 border-violet-500">
          <button
            type="button"
            onClick={() => toggle('list')}
            className="flex items-center gap-1 py-2 text-left text-[13px] font-bold text-violet-600"
          >
            <ChevronDown
              className={cn('h-3.5 w-3.5 transition-transform duration-200', !openSections.list && '-rotate-90')}
            />
            접수장비
          </button>
        </div>
        <div
          className={cn(
            'grid transition-[grid-template-rows] duration-200 ease-out',
            openSections.list ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
          )}
        >
          <div className="overflow-hidden">
            <table className="mt-1 w-full">
              <thead>
                <tr className="border-b border-gray-100">
                  <th className="w-[15%] py-2 text-center text-[11px] font-medium text-violet-500">NO.</th>
                  <th className="py-2 text-left text-[11px] font-medium text-violet-500">접수장비</th>
                  <th className="py-2 text-left text-[11px] font-medium text-violet-500">접수내용</th>
                  <th className="w-[15%] py-2 text-center text-[11px] font-medium text-violet-500">제거</th>
                </tr>
              </thead>
              <tbody>
                {list.length === 0 ? (
                  <tr>
                    <td colSpan={4} className="py-4 text-center text-[11px] text-gray-300">
                      추가된 장비 없음
                    </td>
                  </tr>
                ) : (
                  list.map((item, i) => (
                    <tr key={i} className="border-b border-gray-50 transition-colors hover:bg-gray-50/50">
                      <td className="py-2 text-center">{i + 1}</td>
                      <td className="text-left">{item.equip}</td>
                      <td className="text-left">{item.content}</td>
                      <td className="text-center">
                        <button type="button" onClick={() => handleRemove(i)}>
                          <X className="mx-auto h-3.5 w-3.5 text-red-400 transition-colors hover:text-red-600" />
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* ── 접수방법 ── */}
      <div>
        <div className="flex w-full items-center justify-between border-b-2 border-violet-500">
          <button
            type="button"
            onClick={() => toggle('way')}
            className="flex items-center gap-1 py-2 text-left text-[13px] font-bold text-violet-600"
          >
            <ChevronDown
              className={cn('h-3.5 w-3.5 transition-transform duration-200', !openSections.way && '-rotate-90')}
            />
            접수방법
          </button>
        </div>
        <div
          className={cn(
            'grid transition-[grid-template-rows] duration-200 ease-out',
            openSections.way ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
          )}
        >
          <div className="overflow-hidden">
            <div className="flex flex-col gap-2 py-2">
              <p className="text-[11px] font-medium text-slate-500">접수방법</p>
              <label className="flex items-center gap-3 px-2">
                <input
                  type="checkbox"
                  checked={phoneChk}
                  onChange={(e) => setPhoneChk(e.target.checked)}
                  className="accent-violet-600"
                />
                <span className="w-14 text-[12px] text-slate-600">핸드폰</span>
                <input
                  type="text"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  className="flex-1 rounded border px-2 py-1.5 text-[12px] transition-colors outline-none focus:border-violet-400"
                />
              </label>
              <label className="flex items-center gap-3 px-2">
                <span className="w-[62px] text-[12px] text-slate-600">발신인</span>
                <input
                  type="text"
                  value={sender}
                  onChange={(e) => setSender(e.target.value)}
                  maxLength={10}
                  className="flex-1 rounded border px-2 py-1.5 text-[12px] transition-colors outline-none focus:border-violet-400"
                />
              </label>
              <button
                type="button"
                onClick={handleSubmit}
                className="mx-auto mt-1 rounded bg-violet-600 px-6 py-1.5 text-[12px] font-medium text-white transition-colors hover:bg-violet-700"
              >
                접수하기
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default AsTab;
