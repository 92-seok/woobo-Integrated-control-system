import { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';

// 마커 이미지 import
import rainMarkerImg from '@/assets/rain_marker.png';
import rainMarkerErrImg from '@/assets/rain_marker_error.png';
import waterMarkerImg from '@/assets/water_marker.png';
import waterMarkerErrImg from '@/assets/water_marker_error.png';
import floodMarkerImg from '@/assets/floodMarker.png';
import floodMarkerErrImg from '@/assets/floodMarker_error.png';
import gateMarkerImg from '@/assets/gate_marker.png';
import gateMarkerErrImg from '@/assets/gate_marker_error.png';
import broadMarkerImg from '@/assets/broadMarker.png';
import broadMarkerErrImg from '@/assets/broadMarker_error.png';
import displayMarkerImg from '@/assets/display_marker.png';
import displayMarkerErrImg from '@/assets/display_marker_error.png';
import dPlaceMarkerImg from '@/assets/dPlace_marker.png';
import dPlaceMarkerErrImg from '@/assets/dPlace_marker_error.png';
import snowMarkerImg from '@/assets/snow_marker.png';
import snowMarkerErrImg from '@/assets/snow_marker_error.png';
import cctvMarkerImg from '@/assets/cctvMarker.png';
import AssistantPanel from '@/components/layout/AssistantPanel';

// 마커 종류 타입
type MarkerType = 'rain' | 'water' | 'flood' | 'gate' | 'broad' | 'display' | 'dPlace' | 'snow' | 'cctv';

// 마커 데이터 구조
interface MarkerData {
  type: MarkerType;
  lat: number;
  lng: number;
  name: string;
  status: 'normal' | 'error';
}

// 타입별 이미지 매핑
const MARKER_IMAGES: Record<MarkerType, { normal: string; error: string }> = {
  rain: { normal: rainMarkerImg, error: rainMarkerErrImg },
  water: { normal: waterMarkerImg, error: waterMarkerErrImg },
  flood: { normal: floodMarkerImg, error: floodMarkerErrImg },
  gate: { normal: gateMarkerImg, error: gateMarkerErrImg },
  broad: { normal: broadMarkerImg, error: broadMarkerErrImg },
  display: { normal: displayMarkerImg, error: displayMarkerErrImg },
  dPlace: { normal: dPlaceMarkerImg, error: dPlaceMarkerErrImg },
  snow: { normal: snowMarkerImg, error: snowMarkerErrImg },
  cctv: { normal: cctvMarkerImg, error: cctvMarkerImg },
};

// 마커 타입별 색상, 라벨, 버튼
const MARKER_CONFIG: Record<MarkerType, { color: string; label: string; btn: string; route: string }> = {
  rain: { color: '#42569d', label: '강우량', btn: '데이터검색', route: '/data' },
  water: { color: '#329fe0', label: '수위', btn: '데이터검색', route: '/data' },
  flood: { color: '#f94045', label: '침수', btn: '데이터검색', route: '/data' },
  gate: { color: '#e66ba1', label: '차단기', btn: '차단기관리', route: '/gate' },
  broad: { color: '#f3732c', label: '예경보', btn: '방송관리', route: '/broad' },
  display: { color: '#ffb200', label: '전광판', btn: '전광판관리', route: '/display' },
  dPlace: { color: '#a5614a', label: '변위', btn: '데이터검색', route: '/data' },
  snow: { color: '#8643ae', label: '적설', btn: '데이터검색', route: '/data' },
  cctv: { color: '#2b7a78', label: 'CCTV', btn: 'CCTV보기', route: '/cctv' },
};

// 인포윈도우 HTML 생성 함수
function createInfoContent(data: MarkerData): string {
  const cfg = MARKER_CONFIG[data.type];
  const isError = data.status === 'error';

  return `
    <div style="
      min-width:220px;
      border-radius:12px;
      overflow:hidden;
      box-shadow:0 4px 20px rgba(0,0,0,.15);
      font-family:'Pretendard','Apple SD Gothic Neo',sans-serif;
    ">
      <!-- 헤더 -->
      <div style="
        background:${cfg.color};
        color:#fff;
        padding:10px 14px;
        display:flex;
        align-items:center;
        gap:8px;
        position:relative;
      ">
        <span style="
          display:inline-block;
          width:8px;height:8px;
          border-radius:50%;
          background:${isError ? '#ff6b6b' : '#69db7c'};
          box-shadow:0 0 6px ${isError ? '#ff6b6b' : '#69db7c'};
        "></span>
        <strong style="font-size:13px;letter-spacing:-0.3px;">${data.name}</strong>
        <span
          onclick="window.__closeInfoWindow()"
          style="
            position:absolute;
            right:10px;
            top:50%;
            transform:translateY(-50%);
            width:22px;height:22px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            background:rgba(255,255,255,.15);
            backdrop-filter:blur(4px);
            cursor:pointer;
            font-size:14px;
            line-height:1;
            transition:background .2s;
          "
          onmouseover="this.style.background='rgba(255,255,255,.35)'"
          onmouseout="this.style.background='rgba(255,255,255,.15)'"
        >&times;</span>
      </div>

      <!-- 데이터 영역 -->
      <div style="background:#fff;">
        <table style="width:100%;border-collapse:collapse;">
          <tr>
            <td style="
              padding:9px 14px;
              font-size:12px;
              color:#6b7280;
              width:45%;
              border-bottom:1px solid #f3f4f6;
            ">${cfg.label}상태</td>
            <td style="
              padding:9px 14px;
              font-size:12px;
              font-weight:600;
              color:${isError ? '#ef4444' : '#10b981'};
              text-align:right;
              border-bottom:1px solid #f3f4f6;
            ">${isError ? '이상' : '정상'}</td>
          </tr>
        </table>
      </div>

      <!-- 하단 버튼 -->
      <div style="padding:8px 12px;background:#fafafa;border-top:1px solid #f0f0f0;">
        <div
          onclick="window.__navigateTo('${cfg.route}')"
          style="
            display:block;
            width:100%;
            padding:7px 0;
            background:${cfg.color};
            color:#fff;
            font-size:12px;
            font-weight:500;
            border-radius:6px;
            cursor:pointer;
            text-align:center;
            letter-spacing:-0.2px;
            transition:opacity .2s;
          "
          onmouseover="this.style.opacity='0.85'"
          onmouseout="this.style.opacity='1'"
        >${cfg.btn}</div>
      </div>
    </div>
    <div style="
      width:0;height:0;
      border-left:10px solid transparent;
      border-right:10px solid transparent;
      border-top:10px solid #fafafa;
      margin:0 auto;
    "></div>
  `;
}

// 샘플이미지 -> 추후 실 데이터 연동
const SAMPLE_MARKERS: MarkerData[] = [
  { type: 'rain', lat: 37.4346, lng: 127.1741, name: '강우량계', status: 'normal' },
  { type: 'water', lat: 37.4326, lng: 127.1721, name: '수위계', status: 'error' },
  { type: 'flood', lat: 37.4356, lng: 127.1751, name: '침수센서', status: 'normal' },
  { type: 'gate', lat: 37.4316, lng: 127.1761, name: '차단기', status: 'normal' },
  { type: 'broad', lat: 37.4366, lng: 127.1711, name: '예경보', status: 'normal' },
  { type: 'display', lat: 37.4336, lng: 127.1771, name: '전광판', status: 'error' },
  { type: 'snow', lat: 37.4376, lng: 127.1731, name: '적설계', status: 'normal' },
  { type: 'cctv', lat: 37.4306, lng: 127.1701, name: 'CCTV', status: 'normal' },
];

export function MainPage() {
  const mapRef = useRef<HTMLDivElement>(null);
  const mapInstanceRef = useRef<any>(null);
  const [panelOpen, setPanelOpen] = useState(true);
  const [mapType, setMapType] = useState<'roadmap' | 'hybrid'>('hybrid');
  const navigate = useNavigate();

  useEffect(() => {
    (window as any).__navigateTo = (path: string) => navigate(path);
    return () => {
      delete (window as any).__navigateTo;
    };
  }, [navigate]);

  // 지도 타입 전환
  const toggleMapType = () => {
    const map = mapInstanceRef.current;
    if (!map) return;
    const kakao = (window as any).kakao;
    if (mapType === 'hybrid') {
      map.setMapTypeId(kakao.maps.MapTypeId.ROADMAP);
      setMapType('roadmap');
    } else {
      map.setMapTypeId(kakao.maps.MapTypeId.HYBRID);
      setMapType('hybrid');
    }
  };

  // 줌 인/아웃
  const zoomIn = () => {
    const map = mapInstanceRef.current;
    if (map) map.setLevel(map.getLevel() - 1);
  };
  const zoomOut = () => {
    const map = mapInstanceRef.current;
    if (map) map.setLevel(map.getLevel() + 1);
  };

  useEffect(() => {
    const KAKAO_KEY = import.meta.env.VITE_KAKAO_MAP_KEY;

    const initMap = () => {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const kakao = (window as any).kakao;

      kakao.maps.load(() => {
        if (!mapRef.current) return;

        const map = new kakao.maps.Map(mapRef.current, {
          center: new kakao.maps.LatLng(37.4336, 127.1731),
          level: 3,
        });

        // 하이브리드 기본 설정 + 인스턴스 저장
        map.setMapTypeId(kakao.maps.MapTypeId.HYBRID);
        mapInstanceRef.current = map;

        // 현재 열린 인포윈도우 추적(한개만 열리기)
        let openInfoWindow: any = null;

        // 글로벌 닫기 함수
        (window as any).__closeInfoWindow = () => {
          if (openInfoWindow) {
            openInfoWindow.setMap(null);
            openInfoWindow = null;
          }
        };

        // 마커생성로직
        SAMPLE_MARKERS.forEach((data) => {
          // 이미지 설정
          const imgSrc = MARKER_IMAGES[data.type][data.status];
          const markerImage = new kakao.maps.MarkerImage(imgSrc, new kakao.maps.Size(35, 51));

          // 마커 생성
          const marker = new kakao.maps.Marker({
            map,
            position: new kakao.maps.LatLng(data.lat, data.lng),
            title: data.name,
            image: markerImage,
          });

          // 인포 윈도우 내용
          // const content = `
          // <div style="padding:10px 14px;min-width:150px;font-size:13px;line-height:1.6;">
          //     <strong>${data.name}</strong><br/>
          //     <span style="color:${data.status === 'error' ? '#e53e3e' : '#38a169'}">
          //       ${data.status === 'error' ? '⚠ 이상' : '✓ 정상'}
          //     </span>
          //   </div>
          // `;

          // 커스텀오버레이 생성
          const overlay = new kakao.maps.CustomOverlay({
            content: createInfoContent(data),
            position: new kakao.maps.LatLng(data.lat, data.lng),
            yAnchor: 1.3,
          });

          // 클릭 -> 커스텀오버레이 열기/닫기
          kakao.maps.event.addListener(marker, 'click', () => {
            if (openInfoWindow) openInfoWindow.setMap(null);
            if (openInfoWindow === overlay) {
              openInfoWindow = null;
            } else {
              overlay.setMap(map);
              openInfoWindow = overlay;
            }
          });
        });
      });
    };

    // 이미 kakao SDK가 로드된 상태 (StrictMode 2번째 실행 시)
    if ((window as any).kakao) {
      initMap();
      return;
    }

    // 이미 스크립트 태그가 존재하는 경우 (중복 방지)
    const existing = document.querySelector('script[src*="dapi.kakao.com"]');
    if (existing) {
      existing.addEventListener('load', initMap);
      return;
    }

    // 스크립트 신규 추가
    const script = document.createElement('script');
    script.src = `//dapi.kakao.com/v2/maps/sdk.js?appkey=${KAKAO_KEY}&autoload=false`;
    script.onload = initMap;
    document.head.appendChild(script);
  }, []);

  return (
    <div className="flex flex-1 flex-col overflow-hidden">
      {/* 헤더 */}
      <header className="flex items-center justify-center border-b bg-white px-5 py-3.5 shadow-sm">
        <div>
          <p className="text-xl font-bold tracking-wide text-slate-800">Intelligent Integrated Control System</p>
          <p className="mt-0.5 text-[14px] text-slate-400">실시간 통합 관제 시스템</p>
        </div>
      </header>

      {/* 지도 영역  + 우측 패널 */}
      <div className="relative flex flex-1 overflow-hidden">
        {/* 지도 영역 */}
        <div className="relative flex-1 p-3">
          <div ref={mapRef} className="h-full w-full rounded-xl border shadow-sm" />

          {/* 지도 타입 변환 */}
          <div className="absolute top-5 left-5 z-10">
            <button
              type="button"
              onClick={toggleMapType}
              className="rounded-lg bg-white px-3 py-2 text-[12px] font-medium text-slate-700 shadow-md transition-colors hover:bg-gray-50"
            >
              {mapType === 'hybrid' ? '일반지도' : '하이브리드'}
            </button>
          </div>

          {/* 줌 컨트롤 */}
          <div className="absolute top-15 left-5 z-10 flex flex-col">
            <button
              type="button"
              onClick={zoomIn}
              className="rounded-t-lg bg-white px-3 py-2 text-[16px] font-bold text-slate-600 shadow-md transition-colors hover:bg-gray-50"
            >
              +
            </button>
            <button
              type="button"
              onClick={zoomOut}
              className="rounded-b-lg border-t bg-white px-3 py-2 text-[16px] font-bold text-slate-600 shadow-md transition-colors hover:bg-gray-50"
            >
              −
            </button>
          </div>
        </div>

        <AssistantPanel open={panelOpen} onToggle={() => setPanelOpen(!panelOpen)} />
      </div>
    </div>
  );
}

export default MainPage;
