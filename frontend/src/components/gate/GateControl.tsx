import { useState } from 'react';
import { ShieldCheck, Info, ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { GateEquipment, GateSendDto, GateStatus } from '@/types/gate';
import { GATE_STATUS_CONFIG } from '@/types/gate';

interface GateControlProps {
  equipments: GateEquipment[];
  statuses: GateStatus[];
  onStatusChange: (code: string, gate: 'open' | 'close') => void;
}

export function GateControl({ equipments, statuses, onStatusChange }: GateControlProps) {
  const [helpOpen, setHelpOpen] = useState(false);
  const [isSending, setIsSending] = useState(false);

  // 각 차단기의 현재 상태를 Map으로 관리
  const statusMap = new Map(statuses.map((s) => [s.CD_DIST_OBSV, s.Gate]));

  const handleToggle = async (code: string, gate: 'open' | 'close') => {
    if (!confirm(`차단기 상태를 "${gate === 'open' ? '열림' : '닫힘'}"으로 변경하시겠습니까?`)) return;

    const equip = equipments.find((e) => e.CD_DIST_OBSV === code);
    if (!equip) return;

    const payload: GateSendDto = {
      Devices: [code],
      Gate: gate,
      Status: 'start',
    };

    setIsSending(true);

    try {
      const response = await fetch('/api/gate/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`서버 오류 (${response.status}: ${errorText})`);
      }
      onStatusChange(code, gate);
      alert('차단기 상태가 변경되었습니다.');
    } catch (err) {
      const message = err instanceof Error ? err.message : '알 수 없는 오류가 발생했습니다.';
      alert(`제어 실패: ${message}`);
    } finally {
      setIsSending(false);
    }
  };

  return (
    <div className="space-y-4">
      {/* 테이블 */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <div className="border-border bg-muted/40 flex items-center gap-2.5 border-b px-5 py-3">
          <ShieldCheck className="text-primary h-4 w-4" />
          <h3 className="text-sm font-semibold">차단기 현황</h3>
          <span className="bg-primary text-primary-foreground rounded-full px-2.5 py-0.5 text-xs">
            {equipments.length}대
          </span>
        </div>

        <Table className="table-fixed">
          <colgroup>
            <col className="w-20" />
            <col />
            <col />
            <col className="w-80" />
            <col className="w-80" />
            <col className="w-40" />
          </colgroup>
          <TableHeader>
            <TableRow className="bg-muted/30 hover:bg-muted/30">
              <TableHead className="px-3 py-2.5 text-center text-xs">No</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">차단기명</TableHead>
              <TableHead className="hidden px-3 py-2.5 text-center text-xs md:table-cell">주소</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">통신시간</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">상태변경</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">현재상태</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {equipments.map((equip, idx) => {
              const currentGate = statusMap.get(equip.CD_DIST_OBSV) ?? 'open';
              const statusInfo = GATE_STATUS_CONFIG[currentGate];

              return (
                <TableRow key={equip.CD_DIST_OBSV}>
                  <TableCell className="px-3 py-3 text-center text-sm">{idx + 1}</TableCell>
                  <TableCell className="px-3 py-3 text-center font-medium">{equip.NM_DIST_OBSV}</TableCell>
                  <TableCell className="text-muted-foreground hidden px-3 py-3 text-center text-sm md:table-cell">
                    {equip.DTL_ADRES}
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center font-medium">
                    {statuses.find((s) => s.CD_DIST_OBSV === equip.CD_DIST_OBSV)?.RegDate ?? '-'}
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center">
                    <div className="flex items-center justify-center gap-2">
                      <Button
                        size="sm"
                        variant={currentGate === 'open' ? 'default' : 'outline'}
                        onClick={() => handleToggle(equip.CD_DIST_OBSV, 'open')}
                        className="w-16"
                        disabled={isSending}
                      >
                        열림
                      </Button>
                      <Button
                        size="sm"
                        variant={currentGate === 'close' ? 'destructive' : 'outline'}
                        onClick={() => handleToggle(equip.CD_DIST_OBSV, 'close')}
                        className="w-16"
                      >
                        닫힘
                      </Button>
                    </div>
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center">
                    <span className={cn('text-sm font-semibold', statusInfo.color)}>{statusInfo.label}</span>
                  </TableCell>
                </TableRow>
              );
            })}
          </TableBody>
        </Table>
      </div>

      {/* 도움말 */}
      <div>
        <button
          type="button"
          onClick={() => setHelpOpen((prev) => !prev)}
          className="text-muted-foreground hover:bg-muted hover:text-foreground flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium transition-all"
        >
          <Info className="h-3.5 w-3.5" />
          도움말 {helpOpen ? '닫기' : '보기'}
          <ChevronDown className={cn('h-3.5 w-3.5 transition-transform duration-300', helpOpen && 'rotate-180')} />
        </button>
        <div
          className={cn(
            'grid transition-all duration-300 ease-in-out',
            helpOpen ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'
          )}
        >
          <div className="overflow-hidden">
            <ul className="mt-1 flex flex-col gap-1 rounded-md border border-blue-100 bg-blue-50/60 px-4 py-3 text-xs leading-relaxed text-blue-700/80">
              <li className="font-semibold text-blue-700">◈ 차단기 수동 제어</li>
              <li>- 제어하려는 차단기의 상태 버튼(열림/닫힘)을 클릭합니다.</li>
              <li>- 파란색 버튼이 현재 상태를 나타냅니다.</li>
              <li className="mt-1.5 font-semibold text-blue-700">◈ 상태 확인</li>
              <li>- 현재 상태 컬럼에서 각 차단기의 실시간 상태를 확인할 수 있습니다.</li>
              <li>- 상태 변경 시 확인 팝업이 표시됩니다.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
