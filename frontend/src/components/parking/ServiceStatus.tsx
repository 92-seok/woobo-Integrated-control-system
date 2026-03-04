import { useState } from 'react';
import { RefreshCw, Play, Square, RotateCw, FileText } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import type { ServiceProcess } from '@/types/parking';
import { SERVICE_STATUS_CONFIG } from '@/types/parking';

interface ServiceStatusProps {
  process: ServiceProcess;
}

function formatUptime(ms: number): string {
  const seconds = Math.floor(ms / 1000);
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);
  if (days > 0) return `${days}일 ${hours % 24}시간`;
  if (hours > 0) return `${hours}시간 ${minutes % 60}분`;
  if (minutes > 0) return `${minutes}분 ${seconds % 60}초`;
  return `${seconds}초`;
}

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

export function ServiceStatus({ process }: ServiceStatusProps) {
  const [logsOpen, setLogsOpen] = useState(false);
  const statusInfo = SERVICE_STATUS_CONFIG[process.status];
  const isOnline = process.status === 'online';

  const handleControl = (action: string) => {
    const labels: Record<string, string> = { start: '시작', stop: '중지', restart: '재시작' };
    if (!confirm(`HnsLpr 서비스를 ${labels[action]} 하시겠습니까?`)) return;
    alert(`${labels[action]} 요청 (TODO: API 연동 POST /parking/server/pm2Control.php)`);
  };

  return (
    <div className="space-y-4">
      {/* 컨트롤 패널 */}
      <div className="border-border rounded-lg border bg-white p-4 shadow-sm">
        <div className="flex flex-wrap gap-2">
          <Button size="sm" onClick={() => alert('새로고침 (TODO: API 연동)')}>
            <RefreshCw className="mr-1.5 h-3.5 w-3.5" />
            새로고침
          </Button>
          {!isOnline && (
            <Button
              size="sm"
              variant="outline"
              onClick={() => handleControl('start')}
              className="text-emerald-600 hover:text-emerald-700"
            >
              <Play className="mr-1.5 h-3.5 w-3.5" />
              시작
            </Button>
          )}
          {isOnline && (
            <Button size="sm" variant="destructive" onClick={() => handleControl('stop')}>
              <Square className="mr-1.5 h-3.5 w-3.5" />
              중지
            </Button>
          )}
          <Button size="sm" variant="outline" onClick={() => handleControl('restart')}>
            <RotateCw className="mr-1.5 h-3.5 w-3.5" />
            재시작
          </Button>
          {isOnline && (
            <Button size="sm" variant="outline" onClick={() => setLogsOpen((prev) => !prev)}>
              <FileText className="mr-1.5 h-3.5 w-3.5" />
              {logsOpen ? '로그 닫기' : '로그 보기'}
            </Button>
          )}
        </div>
      </div>

      {/* 서비스 상태 카드 */}
      <div className="border-border rounded-lg border bg-white p-8 text-center shadow-sm">
        <div
          className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full shadow-lg"
          style={{
            background: isOnline
              ? 'linear-gradient(135deg, #4CAF50, #45a049)'
              : 'linear-gradient(135deg, #f44336, #d32f2f)',
          }}
        >
          <span className="text-2xl font-bold text-white">{isOnline ? 'O' : 'X'}</span>
        </div>
        <h3 className="mb-4 text-lg font-semibold">HnsLpr 서비스</h3>
        <span
          className={cn(
            'rounded-md px-4 py-1.5 text-sm font-semibold',
            statusInfo.color,
            isOnline ? 'bg-emerald-50' : 'bg-red-50'
          )}
        >
          {statusInfo.label}
        </span>

        <div className="text-muted-foreground mt-6 space-y-1.5 text-sm">
          <p>
            <strong>프로세스 이름:</strong> {process.name}
          </p>
          <p>
            <strong>CPU 사용률:</strong> {process.cpu}%
          </p>
          <p>
            <strong>메모리 사용량:</strong> {formatBytes(process.memory)}
          </p>
          <p>
            <strong>재시작 횟수:</strong> {process.restarts}회
          </p>
          <p>
            <strong>가동 시간:</strong> {formatUptime(process.uptime)}
          </p>
        </div>
      </div>

      {/* 로그 패널 */}
      {logsOpen && (
        <div className="border-border overflow-hidden rounded-lg border shadow-sm">
          <div className="flex items-center justify-between border-b bg-gray-100 px-4 py-2.5">
            <span className="text-xs font-semibold">HnsLpr 실시간 로그 (최근 10줄)</span>
            <Button variant="ghost" size="sm" onClick={() => setLogsOpen(false)} className="h-6 px-2 text-xs">
              닫기
            </Button>
          </div>
          <pre className="max-h-[300px] overflow-y-auto bg-slate-800 p-4 font-mono text-xs leading-relaxed text-slate-200">
            {`[2026-03-03 10:01:11] hnsLpr connected to LPR device LP01 (192.168.10.1:5000)
              [2026-03-03 10:01:11] hnsLpr connected to LPR device LP02 (192.168.10.2:5000)
              [2026-03-03 10:01:12] hnsLpr connected to LPR device LP03 (192.168.10.3:5000)
              [2026-03-03 10:01:12] Watching for car events...
              [2026-03-03 10:05:30] LP01 detected: 12가3456 (DeviceCode: 01)
              [2026-03-03 10:05:30] Record saved to hns_lprdata
              [2026-03-03 10:12:45] LP02 detected: 34나7890 (DeviceCode: 02)
              [2026-03-03 10:12:45] Record saved to hns_lprdata
              [2026-03-03 10:20:00] Heartbeat: all devices online
              [2026-03-03 10:30:00] Heartbeat: all devices online`}
          </pre>
        </div>
      )}
    </div>
  );
}
