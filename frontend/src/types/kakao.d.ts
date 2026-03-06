declare namespace kakao.maps {
  class LatLng {
    constructor(lat: number, lng: number);
  }
  class Map {
    constructor(container: HTMLElement, options: { center: LatLng; level: number });
    setMapTypeId(typeId: number): void;
    getLevel(): number;
    setLevel(level: number): void;
  }
  class Marker {
    constructor(options: { map: Map; position: LatLng; title?: string; image?: MarkerImage });
  }
  class MarkerImage {
    constructor(src: string, size: size);
  }
  class Size {
    constructor(width: number, height: number);
  }
  class CustomOverlay {
    constructor(options: { content: string; position: LatLng; yAnchor?: number });
    setMap(map: Map | null): void;
  }
  namespace event {
    function addListener(target: Marker, type: string, handler: () => void): void;
  }
  const MapTypeId: {
    ROADMAP: number;
    HYBRID: number;
  };
  function load(callback: () => void): void;
}

// Window 인터페이서 확장
interface Window {
  kakao: typeof kakao;
  __navigateTo?: (path: string) => void;
  __closeInfoWindow?: () => void;
}
