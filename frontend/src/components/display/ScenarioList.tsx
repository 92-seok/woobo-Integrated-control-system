import { useState } from 'react';
import type { DisplayScenario, DisplayEquipment } from '@/types/display';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';

interface ScenarioListProps {
  equipment: DisplayEquipment;
  scenarios: DisplayScenario[];
  onAddScenario: () => void;
  onDeleteScenarios: (disCodes: number[]) => void;
  onEndScenario: (disCode: number) => void;
  onEditScenario: (scenario: DisplayScenario) => void;
}

export function ScenarioList({
  equipment,
  scenarios,
  onAddScenario,
  onDeleteScenarios,
  onEndScenario,
  onEditScenario,
}: ScenarioListProps) {
  const [checkedIds, setCheckedIds] = useState<Set<number>>(new Set());
  const now = new Date().toISOString().replace('T', ' ').slice(0, 19);

  // 표출중: Exp_YN === 'Y' && 현재 시간이 StrTime ~ EndTime 사이
  const activeScenarios = scenarios.filter((s) => s.Exp_YN === 'Y' && s.StrTime <= now && s.EndTime >= now);

  // 등록된 전체 리스트 (최신순)
  const allScenarios = [...scenarios].sort((a, b) => b.DisCode - a.DisCode);

  // 시나리오 상태 계산 (sendEachScen.php:173~181 로직)
  function getStatus(s: DisplayScenario) {
    if (s.Exp_YN === 'Y' && s.StrTime <= now && s.EndTime >= now) {
      return { label: '표시중', color: 'text-blue-600' };
    }
    if (s.Exp_YN === 'N') {
      return { label: '수동 종료', color: 'text-gray-400' };
    }
    if (s.StrTime > now) {
      return { label: '표시 대기', color: 'text-red-500' };
    }
    return { label: '표시 종료', color: 'text-gray-400' };
  }

  // 체크박스 토글
  function toggleCheck(disCode: number) {
    setCheckedIds((prev) => {
      const next = new Set(prev);
      if (next.has(disCode)) next.delete(disCode);
      else next.add(disCode);
      return next;
    });
  }

  // 전체 선택/해제
  function toggleAll() {
    if (checkedIds.size === allScenarios.length) {
      setCheckedIds(new Set());
    } else {
      setCheckedIds(new Set(allScenarios.map((s) => s.DisCode)));
    }
  }

  return (
    <div className="flex flex-col gap-5">
      {/* 장비 기본정보 */}
      <div className="rounded-lg border bg-white shadow-sm">
        <div className="border-b px-4 py-3">
          <h3 className="text-sm font-semibold">◈ 전광판 기본정보</h3>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>장비명</TableHead>
              <TableHead className="text-center">사이즈</TableHead>
              <TableHead>설치지역</TableHead>
              <TableHead className="text-center">IP(Port)</TableHead>
              <TableHead className="text-center">전원상태</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow>
              <TableCell className="text-xs font-medium">{equipment.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-center text-xs">
                {equipment.SizeX} x {equipment.SizeY}
              </TableCell>
              <TableCell className="text-xs">{equipment.DTL_ADRES}</TableCell>
              <TableCell className="text-center text-xs">
                {equipment.ConnIP} : {equipment.ConnPort}
              </TableCell>
              <TableCell className="text-center text-xs">
                {equipment.LastStatus.toLowerCase() === 'ok' ? (
                  <span className="font-medium text-blue-600">정상</span>
                ) : (
                  <span className="font-medium text-red-500">점검요망</span>
                )}
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      {/* 표출중 시나리오 리스트 */}
      <div className="rounded-lg border bg-white shadow-sm">
        <div className="border-b px-4 py-3">
          <h3 className="text-sm font-semibold">◈ 표출중 시나리오 리스트</h3>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-12 text-center">No</TableHead>
              <TableHead>표시일자</TableHead>
              <TableHead>내용</TableHead>
              <TableHead className="w-32 text-center">시나리오 종료</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {activeScenarios.length === 0 ? (
              <TableRow>
                <TableCell colSpan={4} className="text-muted-foreground py-6 text-center text-xs">
                  표출중인 시나리오가 없습니다
                </TableCell>
              </TableRow>
            ) : (
              activeScenarios.map((s, idx) => (
                <TableRow key={s.DisCode}>
                  <TableCell className="text-center text-xs">{idx + 1}</TableCell>
                  <TableCell className="text-xs">
                    {s.StrTime > now && <span className="mr-1 text-red-500">[표시대기]</span>}
                    {s.StrTime.slice(0, 13)} ~ {s.EndTime.slice(0, 13)}
                  </TableCell>
                  <TableCell>
                    <button
                      type="button"
                      onClick={() => onEditScenario(s)}
                      className="cursor-pointer rounded bg-black px-3 py-1 text-xs text-white"
                      dangerouslySetInnerHTML={{ __html: s.HtmlData }}
                    />
                  </TableCell>
                  <TableCell className="text-center">
                    <button
                      type="button"
                      onClick={() => onEndScenario(s.DisCode)}
                      className="rounded bg-blue-600 px-3 py-1.5 text-xs text-white hover:bg-blue-700"
                    >
                      시나리오 종료
                    </button>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>

      {/* 등록된 리스트 */}
      <div className="rounded-lg border bg-white shadow-sm">
        <div className="border-b px-4 py-3">
          <h3 className="text-sm font-semibold">◈ 등록된 리스트</h3>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10 text-center">
                <Checkbox
                  checked={checkedIds.size === allScenarios.length && allScenarios.length > 0}
                  onCheckedChange={toggleAll}
                />
              </TableHead>
              <TableHead className="w-12 text-center">No</TableHead>
              <TableHead>표시일자</TableHead>
              <TableHead>내용</TableHead>
              <TableHead className="w-24 text-center">표시상태</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {allScenarios.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground py-6 text-center text-xs">
                  등록된 시나리오가 없습니다
                </TableCell>
              </TableRow>
            ) : (
              allScenarios.map((s, idx) => {
                const status = getStatus(s);
                return (
                  <TableRow key={s.DisCode}>
                    <TableCell className="text-center">
                      <Checkbox checked={checkedIds.has(s.DisCode)} onCheckedChange={() => toggleCheck(s.DisCode)} />
                    </TableCell>
                    <TableCell className="text-center text-xs">{idx + 1}</TableCell>
                    <TableCell className="text-xs">
                      {s.StrTime > now && <span className="mr-1 text-red-500">[표시대기]</span>}
                      {s.StrTime.slice(0, 13)} ~ {s.EndTime.slice(0, 13)}
                    </TableCell>
                    <TableCell>
                      <button
                        type="button"
                        onClick={() => onEditScenario(s)}
                        className="cursor-pointer rounded bg-black px-3 py-1 text-xs text-white"
                        dangerouslySetInnerHTML={{ __html: s.HtmlData }}
                      />
                    </TableCell>
                    <TableCell className={`text-center text-xs font-medium ${status.color}`}>{status.label}</TableCell>
                  </TableRow>
                );
              })
            )}
          </TableBody>
        </Table>

        {/* 하단 버튼 */}
        <div className="flex items-center justify-end gap-2 border-t px-4 py-3">
          <button
            type="button"
            onClick={onAddScenario}
            className="rounded bg-blue-600 px-4 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
          >
            시나리오 추가
          </button>
          <button
            type="button"
            onClick={() => {
              if (checkedIds.size === 0) return;
              onDeleteScenarios([...checkedIds]);
              setCheckedIds(new Set());
            }}
            className="rounded bg-red-500 px-4 py-1.5 text-xs font-medium text-white hover:bg-red-600"
          >
            시나리오 삭제
          </button>
        </div>
      </div>
    </div>
  );
}
