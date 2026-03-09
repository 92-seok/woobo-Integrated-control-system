import type { DisplayEquipment } from '@/types/display';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface DisplayEquipListProps {
  equipments: DisplayEquipment[];
  onSelect: (cdDistObsv: string) => void;
  selectedId?: string;
}

export function DisplayEquipList({ equipments, onSelect, selectedId }: DisplayEquipListProps) {
  return (
    <div className="rounded-lg border bg-white shadow-sm">
      <div className="border-b px-4 py-3">
        <h3 className="text-sm font-semibold">전광판 목록</h3>
      </div>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead className="w-12 text-center">No</TableHead>
            <TableHead>장비명</TableHead>
            <TableHead>설치지역</TableHead>
            <TableHead className="text-center">사이즈</TableHead>
            <TableHead className="text-center">IP(Port)</TableHead>
            <TableHead className="text-center">전원상태</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {equipments.map((equip, idx) => (
            <TableRow
              key={equip.CD_DIST_OBSV}
              className={`cursor-pointer transition-colors hover:bg-blue-50/50 ${selectedId === equip.CD_DIST_OBSV ? 'bg-blude-50' : ''}`}
              onClick={() => onSelect(equip.CD_DIST_OBSV)}
            >
              <TableCell className="text-center text-xs">{idx + 1}</TableCell>
              <TableCell className="text-xs font-semibold">{equip.NM_DIST_OBSV}</TableCell>
              <TableCell className="text-xs">{equip.DTL_ADRES}</TableCell>
              <TableCell className="text-center text-xs">
                {equip.SizeX} x {equip.SizeY}
              </TableCell>
              <TableCell className="text-center text-xs">
                {equip.ConnIP} : {equip.ConnPort}
              </TableCell>
              <TableCell className="text-center text-xs">
                {equip.LastStatus.toLowerCase() === 'ok' ? (
                  <span className="font-medium text-blue-600">정상</span>
                ) : (
                  <span className="font-medium text-red-500">점검 요망</span>
                )}
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}

export default DisplayEquipList;
