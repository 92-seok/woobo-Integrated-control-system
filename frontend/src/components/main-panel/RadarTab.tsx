import { useState, useEffect, useCallback } from 'react';
import { cn } from '@/lib/utils';

const SERVICE_KEY = decodeURIComponent(import.meta.env.VITE_DATA_GO_KR_KEY);

// 위성 영상 종류
const SAT_TYPES = [
  { id: 'ir105', label: '적외' },
  { id: 'vi006', label: '가시' },
  { id: 'wv069', label: '수증기' },
  { id: 'rgbt', label: 'RGB' },
];

// 오늘 날짜 YYYYMMDD
function getToday(): string {
  const d = new Date();
  return `${d.getFullYear()}${String(d.getMonth() + 1).padStart(2, '0')}${String(d.getDate()).padStart(2, '0')}`;
}

// 이미지 뷰어 (공통 컴포넌트)
function ImageViewer({ images, loading, error }: { images: string[]; loading: boolean; error: string }) {
  const [idx, setIdx] = useState(0);

  // 이미지 목록 변경 시 최신으로 이동
  useEffect(() => {
    if (images.length > 0) setIdx(images.length - 1);
  }, [images]);

  return (
    <>
      <div className="relative min-h-0 w-full flex-1 overflow-hidden rounded-lg border bg-gray-50">
        {loading ? (
          <div className="flex h-full items-center justify-center">
            <div className="h-5 w-5 animate-spin rounded-full border-2 border-violet-300 border-t-violet-600" />
          </div>
        ) : error ? (
          <div className="flex h-full items-center justify-center">
            <span className="text-[11px] text-red-400">{error}</span>
          </div>
        ) : images.length > 0 ? (
          <img src={images[idx]} alt="영상" className="h-full w-full object-contain" />
        ) : (
          <div className="flex h-full flex-col items-center justify-center">
            <span className="block text-[12px] text-gray-400">영상 데이터 없음</span>
            <span className="block text-[10px] text-gray-400">(기상청 데이터가 없습니다)</span>
          </div>
        )}
      </div>
      {images.length > 1 && (
        <div className="flex items-center justify-between">
          <button
            type="button"
            onClick={() => setIdx((p) => Math.max(0, p - 1))}
            disabled={idx === 0}
            className="rounded px-2 py-1 text-[10px] text-gray-500 hover:bg-gray-100 disabled:opacity-30"
          >
            ◀ 이전
          </button>
          <span className="text-[10px] text-gray-400">
            {idx + 1} / {images.length}
          </span>
          <button
            type="button"
            onClick={() => setIdx((p) => Math.min(images.length - 1, p + 1))}
            disabled={idx === images.length - 1}
            className="rounded px-2 py-1 text-[10px] text-gray-500 hover:bg-gray-100 disabled:opacity-30"
          >
            다음 ▶
          </button>
        </div>
      )}
    </>
  );
}

// 메인 컴포넌트
export function RadarTab() {
  const [satType, setSatType] = useState('ir105');
  const [radarImages, setRadarImages] = useState<string[]>([]);
  const [satImages, setSatImages] = useState<string[]>([]);
  const [radarLoading, setRadarLoading] = useState(false);
  const [satLoading, setSatLoading] = useState(false);
  const [radarError, setRadarError] = useState('');
  const [satError, setSatError] = useState('');

  // 레이더 영상 조회
  const fetchRadar = useCallback(async () => {
    setRadarLoading(true);
    setRadarError('');
    try {
      const params = new URLSearchParams({
        ServiceKey: SERVICE_KEY,
        pageNo: '1',
        numOfRows: '10',
        dataType: 'JSON',
        data: 'CMP_WRC',
        time: getToday(),
      });
      const res = await fetch(`/data-api/1360000/RadarImgInfoService/getCmpImg?${params}`);
      const json = await res.json();
      const items = json?.response?.body?.items?.item;
      if (items) {
        const list = Array.isArray(items) ? items : [items];
        const urls = list
          .flatMap((i: Record<string, unknown>) => i['rdr-img-file'])
          .filter((u): u is string => typeof u === 'string')
          .map((u) => u.replace('http://www.kma.go.kr', '/kma-img'));
        setRadarImages(urls);
      } else {
        setRadarImages([]);
      }
    } catch {
      setRadarError('레이더 영상 조회 실패');
    } finally {
      setRadarLoading(false);
    }
  }, []);

  // 위성 영상 조회
  const fetchSatellite = useCallback(async () => {
    setSatLoading(true);
    setSatError('');
    try {
      const params = new URLSearchParams({
        ServiceKey: SERVICE_KEY,
        pageNo: '1',
        numOfRows: '10',
        dataType: 'JSON',
        sat: 'G2',
        data: satType,
        area: 'ko',
        time: getToday(),
      });
      const res = await fetch(`/data-api/1360000/SatlitImgInfoService/getInsightSatlit?${params}`);
      const json = await res.json();
      // console.log(`위성 (${satType}) API 응답: `, JSON.stringify(json, null, 2));
      const items = json?.response?.body?.items?.item;
      if (items) {
        const list = Array.isArray(items) ? items : [items];
        const urls = list
          .flatMap((i: Record<string, unknown>) => i['satImgC-file'])
          .filter((u): u is string => typeof u === 'string')
          .map((u) => u.replace('http://www.kma.go.kr', '/kma-img'));
        setSatImages(urls);
      } else {
        setSatImages([]);
      }
    } catch {
      setSatError('위성 영상 조회 실패');
    } finally {
      setSatLoading(false);
    }
  }, [satType]);

  // 마운트 시 레이더 조회
  useEffect(() => {
    fetchRadar();
  }, [fetchRadar]);

  // satType 변경 시 위성 조회
  useEffect(() => {
    fetchSatellite();
  }, [fetchSatellite]);

  return (
    <div className="flex h-full flex-col gap-2">
      {/* ── 레이더 영상 (위) ── */}
      <div className="flex min-h-0 flex-1 flex-col gap-1">
        <h3 className="text-[16px] font-bold text-slate-600">레이더영상</h3>
        <ImageViewer images={radarImages} loading={radarLoading} error={radarError} />
      </div>

      {/* 구분선 */}
      <div className="h-px bg-gray-200" />

      {/* ── 위성 영상 (아래) ── */}
      <div className="flex min-h-0 flex-1 flex-col gap-1">
        <div className="flex items-center justify-center">
          <h3 className="text-[16px] font-bold text-slate-600">위성영상</h3>
        </div>
        {/* 위성 종류 선택 */}
        <div className="flex gap-1">
          {SAT_TYPES.map((t) => (
            <button
              key={t.id}
              type="button"
              onClick={() => setSatType(t.id)}
              className={cn(
                'flex-1 rounded py-1 text-[10px] transition-colors',
                satType === t.id
                  ? 'bg-gray-200 font-medium text-slate-700 shadow-[inset_0_1px_3px_rgba(0,0,0,0.1)]'
                  : 'text-gray-400 hover:bg-gray-50'
              )}
            >
              {t.label}
            </button>
          ))}
        </div>
        <ImageViewer images={satImages} loading={satLoading} error={satError} />
      </div>
    </div>
  );
}

export default RadarTab;
