import { useState, useMemo } from 'react';
import { Search, ChevronLeft, ChevronRight, Trash2, MessageSquare } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { CarRecord, ParkingGroup } from '@/types/parking';
import { DEVICE_CODE_LABELS, CAR_TYPE_OPTIONS } from '@/types/parking';

const PER_PAGE = 25; // PHP와 동일

interface CarHistoryProps {
  records: CarRecord[];
  groups: ParkingGroup[];
}

export function CarHistory({ records, groups }: CarHistoryProps) {
  const today = new Date().toISOString().slice(0, 10);

  const [parkCode, setParkCode] = useState('');
  const [carType, setCarType] = useState('3');
  const [startDate, setStartDate] = useState(today);
  const [endDate, setEndDate] = useState(today);
  const [carNumber, setCarNumber] = useState('');
  const [page, setPage] = useState(1);
  const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
  const [searched, setSearched] = useState(false);

  // 필터링
  const filtered = useMemo(() => {
    if (!searched) return records;
    return records.filter((r) => {
      const date = r.EventDateTime.slice(0, 10);
      if (date < startDate || date > endDate) return false;
      if (parkCode && r.ParkGroupName !== parkCode) return false;
      if (carType === '0' && r.DeviceCode !== '01') return false;
      if (carType === '1' && r.DeviceCode !== '02') return false;
      // carType '3'= 입/출차 전체, '2'= 현재주차 (별도 로직 필요, 여기선 전체 표시)
      if (carNumber && !r.CarNumber.includes(carNumber)) return false;
      return true;
    });
  }, [records, searched, startDate, endDate, parkCode, carType, carNumber]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
  const safePage = Math.min(page, totalPages);
  const paged = filtered.slice((safePage - 1) * PER_PAGE, safePage * PER_PAGE);

  const handleSearch = () => {
    setSearched(true);
    setPage(1);
    setSelectedIds(new Set());
  };

  const toggleId = (id: number) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id);
      else next.add(id);
      return next;
    });
  };

  const toggleAll = (checked: boolean) => {
    setSelectedIds(checked ? new Set(paged.map((r) => r.idx)) : new Set());
  };

  const handleDelete = () => {
    if (!confirm(`선택한 ${selectedIds.size}건을 삭제하시겠습니까?`)) return;
    alert(`${selectedIds.size}건 삭제 요청 (TODO: API 연동)`);
    setSelectedIds(new Set());
  };

  const handleSms = () => {
    if (selectedIds.size === 0) {
      alert('차량을 선택해주세요.');
      return;
    }
    alert(`${selectedIds.size}건 안내문자 발송 요청 (TODO: API 연동)`);
  };

  return (
    <div className="space-y-4">
      {/* 검색 바 — PHP parkingCar.php 검색 폼 대응 */}
      <div className="border-border flex flex-wrap items-center gap-3 rounded-lg border bg-white p-4 shadow-sm">
        <select
          value={parkCode}
          onChange={(e) => setParkCode(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        >
          <option value="">전체 주차장</option>
          {groups.map((g) => (
            <option key={g.ParkGroupCode} value={g.ParkGroupName}>
              {g.ParkGroupName}
            </option>
          ))}
        </select>

        <select
          value={carType}
          onChange={(e) => setCarType(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        >
          {CAR_TYPE_OPTIONS.map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>

        <input
          type="date"
          value={startDate}
          onChange={(e) => setStartDate(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        />
        <span className="text-muted-foreground text-sm">~</span>
        <input
          type="date"
          value={endDate}
          onChange={(e) => setEndDate(e.target.value)}
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        />

        <input
          type="text"
          value={carNumber}
          onChange={(e) => setCarNumber(e.target.value)}
          placeholder="차량번호 검색"
          className="border-border rounded-md border px-3 py-1.5 text-sm"
        />

        <Button size="sm" onClick={handleSearch}>
          <Search className="mr-1.5 h-3.5 w-3.5" />
          검색
        </Button>
        <span className="text-muted-foreground ml-auto text-xs">총 {filtered.length}건</span>
      </div>

      {/* 테이블 */}
      <div className="border-border overflow-hidden rounded-lg border bg-white shadow-sm">
        <Table className="table-fixed">
          <colgroup>
            <col className="w-10" />
            <col className="w-12" />
            <col />
            <col className="w-20" />
            <col className="w-28" />
            <col className="w-44" />
          </colgroup>
          <TableHeader>
            <TableRow className="bg-muted/30 hover:bg-muted/30">
              <TableHead className="px-2 py-2.5 text-center">
                <Checkbox
                  checked={paged.length > 0 && selectedIds.size === paged.length}
                  onCheckedChange={(checked) => toggleAll(!!checked)}
                />
              </TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">No</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">게이트</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">구분</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">차량번호</TableHead>
              <TableHead className="px-3 py-2.5 text-center text-xs">시간</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {paged.length === 0 ? (
              <TableRow>
                <TableCell colSpan={6} className="text-muted-foreground px-3 py-10 text-center text-sm">
                  {searched ? '조회된 내역이 없습니다.' : '검색 조건을 설정하고 검색 버튼을 클릭하세요.'}
                </TableCell>
              </TableRow>
            ) : (
              paged.map((record, idx) => (
                <TableRow key={record.idx}>
                  <TableCell className="px-2 py-3 text-center">
                    <Checkbox checked={selectedIds.has(record.idx)} onCheckedChange={() => toggleId(record.idx)} />
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center text-sm">
                    {filtered.length - ((safePage - 1) * PER_PAGE + idx)}
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center text-sm">
                    {record.ParkGroupName}({record.CD_DIST_OBSV}) | {record.NM_DIST_OBSV}
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center">
                    <span
                      className={cn(
                        'text-sm font-semibold',
                        record.DeviceCode === '01' ? 'text-blue-600' : 'text-orange-500'
                      )}
                    >
                      {DEVICE_CODE_LABELS[record.DeviceCode]}
                    </span>
                  </TableCell>
                  <TableCell className="px-3 py-3 text-center font-medium">{record.CarNumber}</TableCell>
                  <TableCell className="text-muted-foreground px-3 py-3 text-center text-sm">
                    {record.EventDateTime}
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>

        {/* 페이지네이션 */}
        {totalPages > 1 && (
          <div className="border-border flex items-center justify-center gap-1 border-t px-4 py-3">
            <Button variant="ghost" size="sm" disabled={safePage === 1} onClick={() => setPage(safePage - 1)}>
              <ChevronLeft className="h-4 w-4" />
            </Button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
              <Button
                key={p}
                variant={p === safePage ? 'default' : 'ghost'}
                size="sm"
                onClick={() => setPage(p)}
                className="w-8"
              >
                {p}
              </Button>
            ))}
            <Button variant="ghost" size="sm" disabled={safePage === totalPages} onClick={() => setPage(safePage + 1)}>
              <ChevronRight className="h-4 w-4" />
            </Button>
          </div>
        )}
      </div>

      {/* 하단 버튼 — PHP parkingCar.php 하단 버튼 대응 */}
      <div className="flex justify-end gap-2">
        <Button variant="outline" size="sm" onClick={handleSms} disabled={selectedIds.size === 0}>
          <MessageSquare className="mr-1.5 h-3.5 w-3.5" />
          안내문자 발송
        </Button>
        <Button variant="destructive" size="sm" onClick={handleDelete} disabled={selectedIds.size === 0}>
          <Trash2 className="mr-1.5 h-3.5 w-3.5" />
          입출차내역 삭제
        </Button>
      </div>
    </div>
  );
}

export default CarHistory;
