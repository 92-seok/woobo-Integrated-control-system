import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { DisplayEquipment, DisplayMent, SaveScenarioPayload } from '@/types/display';
import { DIS_EFFECT_OPTIONS, END_EFFECT_OPTIONS } from '@/types/display';

interface GroupScenarioFormProps {
  equipments: DisplayEquipment[];
  ments: DisplayMent[];
  onSave: (payload: SaveScenarioPayload) => void;
}

function defaultStrDate() {
  return new Date().toISOString().slice(0, 10) + ' 00:00:00';
}
function defaultEndDate() {
  return new Date().toISOString().slice(0, 10) + ' 23:59:59';
}

export function GroupScenarioForm({ equipments, ments, onSave }: GroupScenarioFormProps) {
  // 선택된 장비 코드
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());

  // 시나리오 설정
  const [disEffect, setDisEffect] = useState('1');
  const [disSpeed, setDisSpeed] = useState('1');
  const [disTime, setDisTime] = useState(5);
  const [endEffect, setEndEffect] = useState('5');
  const [endSpeed, setEndSpeed] = useState('1');
  const [strDate, setStrDate] = useState(defaultStrDate());
  const [endDate, setEndDate] = useState(defaultEndDate());
  const [htmlData, setHtmlData] = useState('');

  // 장비 체크 토글
  const toggleEquip = (code: string) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(code)) next.delete(code);
      else next.add(code);
      return next;
    });
  };

  // 전체 선택
  const toggleAll = () => {
    if (selectedIds.size === equipments.length) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(equipments.map((e) => e.CD_DIST_OBSV)));
    }
  };

  // 지정문구 선택
  const handleMentSelect = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const ment = ments.find((m) => m.disCode === Number(e.target.value));
    if (ment) setHtmlData(ment.HtmlData);
  };

  // 일괄 저장 (PHP: cdDistObsv를 쉼표로 연결)
  const handleSubmit = () => {
    if (selectedIds.size === 0) {
      alert('전광판을 1개 이상 선택하세요.');
      return;
    }
    const payload: SaveScenarioPayload = {
      type: 'insert',
      cdDistObsv: [...selectedIds].join(','),
      viewType: 'text',
      disEffect,
      disSpeed,
      disTime,
      endEffect,
      endSpeed,
      strDate,
      endDate,
      relay: '0',
      viewImg: '',
      sendImg: '',
      htmlData,
    };
    onSave(payload);
  };

  return (
    <div className="flex flex-col gap-5">
      {/* 장비 선택 테이블 */}
      <div className="rounded-lg border bg-white shadow-sm">
        <div className="border-b px-4 py-3">
          <h3 className="text-sm font-semibold">◈ 전광판 선택</h3>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10 text-center">
                <Checkbox
                  checked={selectedIds.size === equipments.length && equipments.length > 0}
                  onCheckedChange={toggleAll}
                />
              </TableHead>
              <TableHead className="w-12 text-center">No</TableHead>
              <TableHead>장비명</TableHead>
              <TableHead>설치지역</TableHead>
              <TableHead className="text-center">사이즈</TableHead>
              <TableHead className="text-center">전원상태</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {equipments.map((eq, idx) => (
              <TableRow key={eq.CD_DIST_OBSV}>
                <TableCell className="text-center">
                  <Checkbox
                    checked={selectedIds.has(eq.CD_DIST_OBSV)}
                    onCheckedChange={() => toggleEquip(eq.CD_DIST_OBSV)}
                  />
                </TableCell>
                <TableCell className="text-center text-xs">{idx + 1}</TableCell>
                <TableCell className="text-xs font-medium">{eq.NM_DIST_OBSV}</TableCell>
                <TableCell className="text-xs">{eq.DTL_ADRES}</TableCell>
                <TableCell className="text-center text-xs">
                  {eq.SizeX} x {eq.SizeY}
                </TableCell>
                <TableCell className="text-center text-xs">
                  {eq.LastStatus.toLowerCase() === 'ok' ? (
                    <span className="font-medium text-blue-600">정상</span>
                  ) : (
                    <span className="font-medium text-red-500">점검요망</span>
                  )}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

      {/* 시나리오 설정 */}
      <div className="rounded-lg border bg-white shadow-sm">
        <div className="border-b px-4 py-3">
          <h3 className="text-sm font-semibold">◈ 시나리오 설정</h3>
        </div>
        <div className="flex flex-col gap-5 p-5">
          {/* 효과 설정 */}
          <div className="grid grid-cols-2 gap-4 md:grid-cols-3">
            <label className="flex flex-col gap-1 text-xs font-medium">
              표시효과
              <select
                value={disEffect}
                onChange={(e) => setDisEffect(e.target.value)}
                className="rounded border px-2 py-1.5 text-xs"
              >
                {DIS_EFFECT_OPTIONS.map((o) => (
                  <option key={o.value} value={o.value}>
                    {o.label}
                  </option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              표시속도
              <select
                value={disSpeed}
                onChange={(e) => setDisSpeed(e.target.value)}
                className="rounded border px-2 py-1.5 text-xs"
              >
                {Array.from({ length: 8 }, (_, i) => (
                  <option key={i + 1} value={String(i + 1)}>
                    {i + 1}
                  </option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              표시유지(초)
              <select
                value={disTime}
                onChange={(e) => setDisTime(Number(e.target.value))}
                className="rounded border px-2 py-1.5 text-xs"
              >
                {Array.from({ length: 20 }, (_, i) => (
                  <option key={i + 1} value={i + 1}>
                    {i + 1}초
                  </option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              완료효과
              <select
                value={endEffect}
                onChange={(e) => setEndEffect(e.target.value)}
                className="rounded border px-2 py-1.5 text-xs"
              >
                {END_EFFECT_OPTIONS.map((o) => (
                  <option key={o.value} value={o.value}>
                    {o.label}
                  </option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              완료속도
              <select
                value={endSpeed}
                onChange={(e) => setEndSpeed(e.target.value)}
                className="rounded border px-2 py-1.5 text-xs"
              >
                {Array.from({ length: 8 }, (_, i) => (
                  <option key={i + 1} value={String(i + 1)}>
                    {i + 1}
                  </option>
                ))}
              </select>
            </label>
          </div>

          {/* 시간 설정 */}
          <div className="grid grid-cols-2 gap-4">
            <label className="flex flex-col gap-1 text-xs font-medium">
              표시시작
              <input
                type="datetime-local"
                value={strDate.replace(' ', 'T').slice(0, 16)}
                onChange={(e) => setStrDate(e.target.value.replace('T', ' ') + ':00')}
                className="rounded border px-2 py-1.5 text-xs"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              표시종료
              <input
                type="datetime-local"
                value={endDate.replace(' ', 'T').slice(0, 16)}
                onChange={(e) => setEndDate(e.target.value.replace('T', ' ') + ':00')}
                className="rounded border px-2 py-1.5 text-xs"
              />
            </label>
          </div>

          {/* 지정문구 */}
          {ments.length > 0 && (
            <label className="flex flex-col gap-1 text-xs font-medium">
              지정문구
              <select onChange={handleMentSelect} defaultValue="" className="rounded border px-2 py-1.5 text-xs">
                <option value="" disabled>
                  문구를 선택하세요
                </option>
                {ments.map((m) => (
                  <option key={m.disCode} value={m.disCode}>
                    {m.Title}
                  </option>
                ))}
              </select>
            </label>
          )}

          {/* 내용 + 미리보기 */}
          <div>
            <span className="text-xs font-medium">내용</span>
            <div className="mt-1 flex gap-4">
              <textarea
                value={htmlData}
                onChange={(e) => setHtmlData(e.target.value)}
                className="flex-1 rounded border p-2 text-sm"
                rows={5}
                placeholder="표시할 내용을 입력하세요"
              />
              <div
                className="flex-shrink-0 overflow-hidden rounded border bg-black"
                style={{ width: 426, height: 240 }}
              >
                <div className="h-full w-full p-1 text-sm text-white" dangerouslySetInnerHTML={{ __html: htmlData }} />
              </div>
            </div>
          </div>

          {/* 저장 버튼 */}
          <div className="flex justify-end border-t pt-4">
            <Button size="sm" onClick={handleSubmit}>
              일괄 전송 ({selectedIds.size}대)
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
