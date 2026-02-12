import { useState, useCallback } from 'react';
import { Button } from '@/components/ui/button';

const MAX_CONTENT_LENGTH = 70;
const MAX_TITLE_LENGTH = 30;

type Recipient = {
  gCode: string;
  division: string;
  position: string;
  name: string;
  phone: string;
};

const MOCK_RECIPIENTS: Recipient[] = [
  { gCode: '1', division: '관제실', position: '담당', name: '홍길동', phone: '010-1234-5678' },
  { gCode: '2', division: '안전팀', position: '팀장', name: '김철수', phone: '010-2345-6789' },
  { gCode: '3', division: '운영팀', position: '대리', name: '이영희', phone: '010-3456-7890' },
];

export function SendMsg() {
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');

  const allChecked = selectedIds.size === MOCK_RECIPIENTS.length && MOCK_RECIPIENTS.length > 0;
  const someChecked = selectedIds.size > 0;

  const toggleAll = useCallback(() => {
    if (allChecked) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(MOCK_RECIPIENTS.map((r) => r.gCode)));
    }
  }, [allChecked]);

  const toggleOne = useCallback((gCode: string) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(gCode)) next.delete(gCode);
      else next.add(gCode);
      return next;
    });
  }, []);

  const handleContentChange = useCallback((e: React.ChangeEvent<HTMLTextAreaElement>) => {
    const v = e.target.value;
    if (v.length <= MAX_CONTENT_LENGTH) setContent(v);
  }, []);

  const handleSubmit = useCallback(() => {
    if (!title.trim()) {
      alert('제목을 입력하세요.');
      return;
    }
    if (!content.trim()) {
      alert('내용을 입력하세요.');
      return;
    }
    if (selectedIds.size === 0) {
      alert('수신자를 한 명 이상 선택하세요.');
      return;
    }
    if (content.length > MAX_CONTENT_LENGTH) {
      alert(`내용은 ${MAX_CONTENT_LENGTH}글자 이하여야 합니다.`);
      return;
    }
    const codes = Array.from(selectedIds).join(',');
    console.log('전송 예정:', { codes, title, content });
    alert(`${selectedIds.size}명에게 발송 등록되었습니다. (API 연동 후 실제 전송)`);
  }, [title, content, selectedIds]);

  return (
    <div className="w-full max-w-full py-6 px-0 box-border">
      {/* 수신자선택 */}
      <div className="mb-8">
        <div className="flex items-center gap-2 mb-3">
          <span className="text-[#2b3280] font-semibold text-[15px]">◈</span>
          <span className="text-slate-800 font-medium text-[15px]">수신자선택</span>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
          <table className="w-full border-collapse text-sm text-center">
            <thead>
              <tr className="bg-[#2b3280] text-white">
                <th className="w-[8%] h-11 font-medium text-[13px]">
                  <input
                    type="checkbox"
                    checked={allChecked}
                    ref={(el) => {
                      if (el) el.indeterminate = someChecked && !allChecked;
                    }}
                    onChange={toggleAll}
                    className="size-4 rounded border-slate-300 accent-white"
                  />
                </th>
                <th className="w-[23%] h-11 font-medium text-[13px]">부서명</th>
                <th className="h-11 font-medium text-[13px]">직책</th>
                <th className="h-11 font-medium text-[13px]">별칭</th>
                <th className="h-11 font-medium text-[13px]">연락처</th>
              </tr>
            </thead>
            <tbody className="bg-white">
              {MOCK_RECIPIENTS.map((r) => (
                <tr
                  key={r.gCode}
                  className="border-b border-slate-100 last:border-b-0 hover:bg-slate-50/80 transition-colors"
                >
                  <td className="py-3 px-2">
                    <input
                      type="checkbox"
                      checked={selectedIds.has(r.gCode)}
                      onChange={() => toggleOne(r.gCode)}
                      value={r.gCode}
                      className="size-4 rounded border-slate-300 accent-[#2b3280]"
                    />
                  </td>
                  <td className="py-3 px-2 text-slate-700">{r.division}</td>
                  <td className="py-3 px-2 text-slate-700">{r.position}</td>
                  <td className="py-3 px-2 text-slate-700">{r.name}</td>
                  <td className="py-3 px-2 text-slate-600 tabular-nums">{r.phone}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* 메세지 입력 */}
      <div>
        <div className="flex justify-between items-center flex-wrap gap-2 mb-3">
          <div className="flex items-center gap-2">
            <span className="text-[#2b3280] font-semibold text-[15px]">◈</span>
            <span className="text-slate-800 font-medium text-[15px]">메세지 입력</span>
          </div>
          <span className="text-sm text-slate-500">
            [<span className="font-semibold text-slate-700">{content.length}</span>/
            {MAX_CONTENT_LENGTH}(글자)]
          </span>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
          <table className="w-full border-collapse text-sm">
            <tbody>
              <tr className="border-b border-slate-100">
                <th className="w-[28%] min-w-[80px] h-12 pl-5 text-left text-slate-600 font-medium text-[13px] bg-slate-50/80">
                  제목
                </th>
                <td className="p-3">
                  <input
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value.slice(0, MAX_TITLE_LENGTH))}
                    maxLength={MAX_TITLE_LENGTH}
                    placeholder="제목 입력"
                    className="w-full h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#2b3280]/25 focus:border-[#2b3280] transition-shadow"
                  />
                </td>
              </tr>
              <tr>
                <td colSpan={2} className="p-3 align-top">
                  <textarea
                    value={content}
                    onChange={handleContentChange}
                    maxLength={MAX_CONTENT_LENGTH}
                    rows={10}
                    placeholder="문자 내용 (70자 이내)"
                    className="w-full min-h-[240px] px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#2b3280]/25 focus:border-[#2b3280] resize-none transition-shadow box-border"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div className="flex justify-end mt-4">
          <Button
            type="button"
            onClick={handleSubmit}
            className="bg-[#2b3280] hover:bg-[#101441] text-white h-10 px-6 rounded-lg font-medium shadow-sm hover:shadow transition-shadow"
          >
            전송
          </Button>
        </div>
      </div>

      {/* 도움말 */}
      <div className="mt-8 pt-4 border-t border-slate-100">
        <button
          type="button"
          className="text-sm text-slate-500 hover:text-slate-700 transition-colors"
        >
          도움말 보기
        </button>
      </div>
    </div>
  );
}
