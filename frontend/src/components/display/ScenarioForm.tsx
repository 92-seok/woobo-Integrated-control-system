import { useState } from 'react';
import { Button } from '@/components/ui/button';
import type { DisplayEquipment, DisplayScenario, DisplayMent, SaveScenarioPayload } from '@/types/display';
import { DIS_EFFECT_OPTIONS, END_EFFECT_OPTIONS } from '@/types/display';

interface ScenarioFormProps {
  equipment: DisplayEquipment;
  scenario?: DisplayScenario; // 수정 시 전달, 추가 시 undefined
  ments: DisplayMent[]; // 지정문구 목록
  onSave: (payload: SaveScenarioPayload) => void;
  onCancel: () => void;
}

// 전광판 크기에 따른 미리보기 배율
function getPreviewSize(sizeX: number, sizeY: number) {
  if (sizeX < sizeY) return { width: sizeX * 4, height: sizeY * 4 };
  if (sizeX < 300) return { width: sizeX * 3, height: sizeY * 3 };
  return { width: sizeX * 2, height: sizeY * 2 };
}

// 릴레이 숫자 -> 4비트 배열
function relayToBits(relay: number): boolean[] {
  return [((relay >> 3) & 1) === 1, ((relay >> 2) & 1) === 1, ((relay >> 1) & 1) === 1, ((relay >> 0) & 1) === 1];
}

// 4비트 배열 -> 릴레이 숫자
function bitsToRelay(bits: boolean[]): number {
  return (bits[0] ? 8 : 0) + (bits[1] ? 4 : 0) + (bits[2] ? 2 : 0) + (bits[3] ? 1 : 0);
}

// 기본 날짜값: 오늘 00:00 ~ 오늘 23:59
function defaultStrDate() {
  return new Date().toISOString().slice(0, 10) + ' 00:00:00';
}
function defaultEndDate() {
  return new Date().toISOString().slice(0, 10) + ' 23:59:59';
}

export function ScenarioForm({ equipment, scenario, ments, onSave, onCancel }: ScenarioFormProps) {
  // 폼 상태 (수정 시 기본값, 추가 시 기본값)
  const [disEffect, setDisEffect] = useState(scenario?.DisEffect ?? '1');
  const [disSpeed, setDisSpeed] = useState(scenario?.DisSpeed ?? '1');
  const [disTime, setDisTime] = useState(scenario?.DisTime ?? 5);
  const [endEffect, setEndEffect] = useState(scenario?.EndEffect ?? '5');
  const [endSpeed, setEndSpeed] = useState(scenario?.EndSpeed ?? '1');
  const [strDate, setStrDate] = useState(scenario?.StrTime ?? defaultStrDate());
  const [endDate, setEndDate] = useState(scenario?.EndTime ?? defaultEndDate());
  const [relayBits, setRelayBits] = useState<boolean[]>(relayToBits(Number(scenario?.Relay ?? '0')));
  const [htmlData, setHtmlData] = useState(scenario?.HtmlData ?? '');

  const preview = getPreviewSize(equipment.SizeX, equipment.SizeY);
  const isEdit = !!scenario;

  // 지정문구 선택 시 textarea에 적용
  const handleMentSelect = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const ment = ments.find((m) => m.disCode === Number(e.target.value));
    if (ment) setHtmlData(ment.HtmlData);
  };

  // 릴레이 비트 토글
  const toggleRelay = (idx: number) => {
    setRelayBits((prev) => prev.map((v, i) => (i === idx ? !v : v)));
  };

  // 저장
  const handleSubmit = () => {
    const payload: SaveScenarioPayload = {
      type: isEdit ? 'update' : 'insert',
      cdDistObsv: equipment.CD_DIST_OBSV,
      disCode: scenario?.DisCode,
      viewType: 'text',
      disEffect,
      disSpeed,
      disTime,
      endEffect,
      endSpeed,
      strDate,
      endDate,
      relay: String(bitsToRelay(relayBits)),
      viewImg: '',
      sendImg: '',
      htmlData,
    };
    onSave(payload);
  };

  return (
    <div className="rounded-lg border bg-white shadow-sm">
      <div className="border-b px-4 py-3">
        <h3 className="text-sm font-semibold">
          ◈ 시나리오 {isEdit ? '수정' : '추가'} — {equipment.NM_DIST_OBSV}
        </h3>
      </div>

      <div className="flex flex-col gap-5 p-5">
        {/* 표시효과 / 완료효과 */}
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

        {/* 표시 시작/종료 시간 */}
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

        {/* 릴레이 (4비트 체크박스) */}
        <div>
          <span className="text-xs font-medium">릴레이 제어</span>
          <div className="mt-1 flex gap-3">
            {relayBits.map((checked, idx) => (
              <label key={idx} className="flex items-center gap-1 text-xs">
                <input type="checkbox" checked={checked} onChange={() => toggleRelay(idx)} />
                릴레이 {idx + 1}
              </label>
            ))}
          </div>
        </div>

        {/* 지정문구 선택 */}
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

        {/* 내용 입력 (textarea — 추후 리치에디터로 교체 가능) */}
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
            {/* 미리보기 */}
            <div
              className="flex-shrink-0 overflow-hidden rounded border bg-black"
              style={{ width: preview.width, height: preview.height }}
            >
              <div className="h-full w-full p-1 text-sm text-white" dangerouslySetInnerHTML={{ __html: htmlData }} />
            </div>
          </div>
        </div>

        {/* 버튼 */}
        <div className="flex justify-end gap-2 border-t pt-4">
          <Button variant="outline" size="sm" onClick={onCancel}>
            취소
          </Button>
          <Button size="sm" onClick={handleSubmit}>
            {isEdit ? '수정' : '저장'}
          </Button>
        </div>
      </div>
    </div>
  );
}
