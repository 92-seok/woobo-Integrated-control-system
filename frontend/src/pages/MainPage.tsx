import { useEffect, useRef } from 'react';

export function MainPage() {
  const mapRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const KAKAO_KEY = import.meta.env.VITE_KAKAO_MAP_KEY;

    const initMap = () => {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const kakao = (window as any).kakao;
      kakao.maps.load(() => {
        if (!mapRef.current) return;
        const options = {
          center: new kakao.maps.LatLng(37.4336, 127.1731),
          level: 3,
        };
        new kakao.maps.Map(mapRef.current, options);
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
      <header className="border-border border-b bg-white px-6 py-4 shadow-sm">
        <h1 className="font-jakarta text-foreground text-base font-bold">상황화면</h1>
        <p className="text-muted-foreground text-xs">실시간 지도 모니터링</p>
      </header>

      {/* 지도 영역 */}
      <div className="flex-1 p-4">
        <div ref={mapRef} className="h-full w-full rounded-xl border" style={{ minHeight: '500px' }} />
      </div>
    </div>
  );
}

export default MainPage;
