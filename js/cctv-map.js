/**
 * cctv 전용 지도
 */

// region Marker Tracker 부분

/**
 * AbstractOverlay를 상속받을 객체를 선언합니다.
 */
function TooltipMarker(position, tooltipText) {
    this.position = position;
    let node = (this.node = document.createElement('div'));
    node.className = 'node';

    // 마커의 z-index 설정
    node.style.zIndex = '10';
    node.style.position = 'absolute';

    let tooltip = document.createElement('div');
    (tooltip.className = 'tooltip'), tooltip.appendChild(document.createTextNode(tooltipText));
    node.appendChild(tooltip);

    // 툴팁이 항상 보이도록 설정
    // 툴팁 위치, 폰트 사이즈 조정
    tooltip.style.display = 'block';
    tooltip.style.position = 'absolute';
    tooltip.style.zIndex = '20';
    tooltip.style.left = '50%';
    tooltip.style.transform = 'translateX(-50%)';
    tooltip.style.top = '-28px';
    tooltip.style.fontSize = '18px';

    // 기존 코드 (일단 삭제하지 마봐요)
    // // 툴팁 엘리먼트에 마우스 인터렉션에 따라 보임/숨김 기능을 하도록 이벤트를 등록합니다.
    // node.onmouseover = function () {
    //     tooltip.style.display = 'block';
    // };
    // node.onmouseout = function () {
    //     tooltip.style.display = 'none';
    // };

    // 외부에서 node에 접근할 수 있는 매소드 추가.
    this.getMarkerNode = function () {
        return node;
    };
}

// AbstractOverlay 상속. 프로토타입 체인을 연결합니다.
TooltipMarker.prototype = new kakao.maps.AbstractOverlay();

// AbstractOverlay의 필수 구현 메소드.
// setMap(map)을 호출했을 경우에 수행됩니다.
// AbstractOverlay의 getPanels() 메소드로 MapPanel 객체를 가져오고
// 거기에서 오버레이 레이어를 얻어 생성자에서 만든 엘리먼트를 자식 노드로 넣어줍니다.
TooltipMarker.prototype.onAdd = function () {
    let panel = this.getPanels().overlayLayer;
    panel.appendChild(this.node);
};

// AbstractOverlay의 필수 구현 메소드.
// setMap(null)을 호출했을 경우에 수행됩니다.
// 생성자에서 만든 엘리먼트를 오버레이 레이어에서 제거합니다.
TooltipMarker.prototype.onRemove = function () {
    this.node.parentNode.removeChild(this.node);
};

// AbstractOverlay의 필수 구현 메소드.
// 지도의 속성 값들이 변화할 때마다 호출됩니다. (zoom, center, mapType)
// 엘리먼트의 위치를 재조정 해 주어야 합니다.
TooltipMarker.prototype.draw = function () {
    // 화면 좌표와 지도의 좌표를 매핑시켜주는 projection객체
    let projection = this.getProjection();

    // overlayLayer는 지도와 함께 움직이는 layer이므로
    // 지도 내부의 위치를 반영해주는 pointFromCoords를 사용합니다.
    let point = projection.pointFromCoords(this.position);

    // 내부 엘리먼트의 크기를 얻어서
    let width = this.node.offsetWidth;
    let height = this.node.offsetHeight;

    // 해당 위치의 정중앙에 위치하도록 top, left를 지정합니다.
    this.node.style.left = point.x - width / 2 + 'px';
    this.node.style.top = point.y - height / 2 + 'px';
};

// 좌표를 반환하는 메소드
TooltipMarker.prototype.getPosition = function () {
    return this.position;
};

/**
 * 지도 영역 외부에 존재하는 마커를 추적하는 기능을 가진 객체입니다.
 * 클리핑 알고리즘을 사용하여 tracker의 좌표를 구하고 있습니다.
 */
function MarkerTracker(map, target) {
    // 클리핑을 위한 outcode
    let OUTCODE = {
        INSIDE: 0, // 0b0000
        TOP: 8, //0b1000
        RIGHT: 2, // 0b0010
        BOTTOM: 4, // 0b0100
        LEFT: 1, // 0b0001
    };

    // viewport 영역을 구하기 위한 buffer값
    // target의 크기가 60x60 이므로
    // 여기서는 지도 bounds에서 상하좌우 30px의 여분을 가진 bounds를 구하기 위해 사용합니다.
    let BOUNDS_BUFFER = 30;

    // 클리핑 알고리즘으로 tracker의 좌표를 구하기 위한 buffer값
    // 지도 bounds를 기준으로 상하좌우 buffer값 만큼 축소한 내부 사각형을 구하게 됩니다.
    // 그리고 그 사각형으로 target위치와 지도 중심 사이의 선을 클리핑 합니다.
    // 여기서는 tracker의 크기를 고려하여 40px로 잡습니다.
    let CLIP_BUFFER = 40;

    // trakcer 엘리먼트
    let tracker = document.createElement('div');
    tracker.className = 'tracker';

    // 내부 아이콘
    let icon = document.createElement('div');
    icon.className = 'icon';

    // 외부에 있는 target의 위치에 따라 회전하는 말풍선 모양의 엘리먼트
    let balloon = document.createElement('div');
    balloon.className = 'balloon';

    tracker.appendChild(balloon);
    tracker.appendChild(icon);

    map.getNode().appendChild(tracker);

    // traker를 클릭하면 target의 위치를 지도 중심으로 지정합니다.
    tracker.onclick = function () {
        map.setCenter(target.getPosition());
        setVisible(false);
    };

    // target의 위치를 추적하는 함수
    function tracking() {
        let proj = map.getProjection();

        // 지도의 영역을 구합니다.
        let bounds = map.getBounds();

        // 지도의 영역을 기준으로 확장된 영역을 구합니다.
        let extBounds = extendBounds(bounds, proj);

        // target이 확장된 영역에 속하는지 판단하고
        if (extBounds.contain(target.getPosition())) {
            // 속하면 tracker를 숨깁니다.
            setVisible(false);
        } else {
            // target이 영역 밖에 있으면 계산을 시작합니다.

            // 지도 bounds를 기준으로 클리핑할 top, right, bottom, left를 재계산합니다.
            //
            //  +-------------------------+
            //  | Map Bounds              |
            //  |   +-----------------+   |
            //  |   | Clipping Rect   |   |
            //  |   |                 |   |
            //  |   |        *       (A)  |     A
            //  |   |                 |   |
            //  |   |                 |   |
            //  |   +----(B)---------(C)  |
            //  |                         |
            //  +-------------------------+
            //
            //        B
            //
            //                                       C
            // * 은 지도의 중심,
            // A, B, C가 TooltipMarker의 위치,
            // (A), (B), (C)는 각 TooltipMarker에 대응하는 tracker입니다.
            // 지도 중심과 각 TooltipMarker를 연결하는 선분이 있다고 가정할 때,
            // 그 선분과 Clipping Rect와 만나는 지점의 좌표를 구해서
            // tracker의 위치(top, left)값을 지정해주려고 합니다.
            // tracker 자체의 크기가 있기 때문에 원래 지도 영역보다 안쪽의 가상 영역을 그려
            // 클리핑된 지점을 tracker의 위치로 사용합니다.
            // 실제 tracker의 position은 화면 좌표가 될 것이므로
            // 계산을 위해 좌표 변환 메소드를 사용하여 모두 화면 좌표로 변환시킵니다.

            // TooltipMarker의 위치
            let pos = proj.containerPointFromCoords(target.getPosition());

            // 지도 중심의 위치
            let center = proj.containerPointFromCoords(map.getCenter());

            // 현재 보이는 지도의 영역의 남서쪽 화면 좌표
            let sw = proj.containerPointFromCoords(bounds.getSouthWest());

            // 현재 보이는 지도의 영역의 북동쪽 화면 좌표
            let ne = proj.containerPointFromCoords(bounds.getNorthEast());

            // 클리핑할 가상의 내부 영역을 만듭니다.
            let top = ne.y + CLIP_BUFFER;
            let right = ne.x - CLIP_BUFFER;
            let bottom = sw.y - CLIP_BUFFER;
            let left = sw.x + CLIP_BUFFER;

            // 계산된 모든 좌표를 클리핑 로직에 넣어 좌표를 얻습니다.
            let clipPosition = getClipPosition(top, right, bottom, left, center, pos);

            // 클리핑된 좌표를 tracker의 위치로 사용합니다.
            tracker.style.top = clipPosition.y + 'px';
            tracker.style.left = clipPosition.x + 'px';

            // 말풍선의 회전각을 얻습니다.
            let angle = getAngle(center, pos);

            // 회전각을 CSS transform을 사용하여 지정합니다.
            // 브라우저 종류에따라 표현되지 않을 수도 있습니다.
            // https://caniuse.com/#feat=transforms2d
            balloon.style.cssText += '-ms-transform: rotate(' + angle + 'deg);' + '-webkit-transform: rotate(' + angle + 'deg);' + 'transform: rotate(' + angle + 'deg);';

            // target이 영역 밖에 있을 경우 tracker를 노출합니다.
            setVisible(true);
        }
    }

    // 상하좌우로 BOUNDS_BUFFER(30px)만큼 bounds를 확장 하는 함수
    //
    //  +-----------------------------+
    //  |              ^              |
    //  |              |              |
    //  |     +-----------------+     |
    //  |     |                 |     |
    //  |     |                 |     |
    //  |  <- |    Map Bounds   | ->  |
    //  |     |                 |     |
    //  |     |                 |     |
    //  |     +-----------------+     |
    //  |              |              |
    //  |              v              |
    //  +-----------------------------+
    //
    // 여기서는 TooltipMaker가 완전히 안보이게 되는 시점의 영역을 구하기 위해서 사용됩니다.
    // TooltipMarker는 60x60 의 크기를 가지고 있기 때문에
    // 지도에서 완전히 사라지려면 지도 영역을 상하좌우 30px만큼 더 드래그해야 합니다.
    // 이 함수는 현재 보이는 지도 bounds에서 상하좌우 30px만큼 확장한 bounds를 리턴합니다.
    // 이 확장된 영역은 TooltipMarker가 화면에서 보이는지를 판단하는 영역으로 사용됩니다.
    function extendBounds(bounds, proj) {
        // 주어진 bounds는 지도 좌표 정보로 표현되어 있습니다.
        // 이것을 BOUNDS_BUFFER 픽셀 만큼 확장하기 위해서는
        // 픽셀 단위인 화면 좌표로 변환해야 합니다.
        let sw = proj.pointFromCoords(bounds.getSouthWest());
        let ne = proj.pointFromCoords(bounds.getNorthEast());

        // 확장을 위해 각 좌표에 BOUNDS_BUFFER가 가진 수치만큼 더하거나 빼줍니다.
        sw.x -= BOUNDS_BUFFER;
        sw.y += BOUNDS_BUFFER;

        ne.x += BOUNDS_BUFFER;
        ne.y -= BOUNDS_BUFFER;

        // 그리고나서 다시 지도 좌표로 변환한 extBounds를 리턴합니다.
        // extBounds는 기존의 bounds에서 상하좌우 30px만큼 확장된 영역 객체입니다.
        return new kakao.maps.LatLngBounds(proj.coordsFromPoint(sw), proj.coordsFromPoint(ne));
    }

    // Cohen–Sutherland clipping algorithm
    // 자세한 내용은 아래 위키에서...
    // https://en.wikipedia.org/wiki/Cohen%E2%80%93Sutherland_algorithm
    function getClipPosition(top, right, bottom, left, inner, outer) {
        function calcOutcode(x, y) {
            let outcode = OUTCODE.INSIDE;

            if (x < left) {
                outcode |= OUTCODE.LEFT;
            } else if (x > right) {
                outcode |= OUTCODE.RIGHT;
            }

            if (y < top) {
                outcode |= OUTCODE.TOP;
            } else if (y > bottom) {
                outcode |= OUTCODE.BOTTOM;
            }

            return outcode;
        }

        let ix = inner.x;
        let iy = inner.y;
        let ox = outer.x;
        let oy = outer.y;

        let code = calcOutcode(ox, oy);

        while (true) {
            if (!code) {
                break;
            }

            if (code & OUTCODE.TOP) {
                ox = ox + ((ix - ox) / (iy - oy)) * (top - oy);
                oy = top;
            } else if (code & OUTCODE.RIGHT) {
                oy = oy + ((iy - oy) / (ix - ox)) * (right - ox);
                ox = right;
            } else if (code & OUTCODE.BOTTOM) {
                ox = ox + ((ix - ox) / (iy - oy)) * (bottom - oy);
                oy = bottom;
            } else if (code & OUTCODE.LEFT) {
                oy = oy + ((iy - oy) / (ix - ox)) * (left - ox);
                ox = left;
            }

            code = calcOutcode(ox, oy);
        }

        return { x: ox, y: oy };
    }

    // 말풍선의 회전각을 구하기 위한 함수
    // 말풍선의 anchor가 TooltipMarker가 있는 방향을 바라보도록 회전시킬 각을 구합니다.
    function getAngle(center, target) {
        let dx = target.x - center.x;
        let dy = center.y - target.y;
        let deg = (Math.atan2(dy, dx) * 180) / Math.PI;

        return ((-deg + 360) % 360 | 0) + 90;
    }

    // tracker의 보임/숨김을 지정하는 함수
    function setVisible(visible) {
        tracker.style.display = visible ? 'block' : 'none';
    }

    // Map 객체의 'zoom_start' 이벤트 핸들러
    function hideTracker() {
        setVisible(false);
    }

    // target의 추적을 실행합니다.
    this.run = function () {
        kakao.maps.event.addListener(map, 'zoom_start', hideTracker);
        kakao.maps.event.addListener(map, 'zoom_changed', tracking);
        kakao.maps.event.addListener(map, 'center_changed', tracking);
        tracking();
    };

    // target의 추적을 중지합니다.
    this.stop = function () {
        kakao.maps.event.removeListener(map, 'zoom_start', hideTracker);
        kakao.maps.event.removeListener(map, 'zoom_changed', tracking);
        kakao.maps.event.removeListener(map, 'center_changed', tracking);
        setVisible(false);
    };
}

// endregion

// 지도 초기화 함수
function initializeMap() {
    let mapContainer = document.getElementById('cctv_map');

    if (!mapContainer) {
        console.error('지도 컨테이너를 찾을 수 없습니다.');
        return;
    }

    // 카카오맵 API가 로드되었는지 확인
    if (typeof kakao === 'undefined' || typeof kakao.maps === 'undefined') {
        console.error('카카오맵 API가 로드되지 않았습니다.');
        return;
    }

    try {
        let mapOption = {
            center: new kakao.maps.LatLng(37.433656425335826, 127.17337702179964), // 지도의 중심좌표 (우보)
            level: 3, // 지도의 확대 레벨
            draggable: true, // 지도 이동 가능
            scrollwheel: true, // 마우스 휠로 확대/축소 가능
            disableDoubleClick: false, // 더블클릭 확대/축소 가능
            disableDoubleTap: false, // 더블탭 확대/축소 가능
            disableTwoFingerTap: false, // 두 손가락 탭 확대/축소 가능
        };

                // 지도를 표시할 div와 지도 옵션으로 지도를 생성합니다
        let map = new kakao.maps.Map(mapContainer, mapOption);

        // 지도 로드 완료 이벤트 추가
        kakao.maps.event.addListener(map, 'tilesloaded', function() {
            console.log('지도 로드 완료');
        });

        // 일반지도, 스카이뷰지도 컨트롤
        let mapTypeControl = new kakao.maps.MapTypeControl();
        map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPLEFT);

        // 지도 확대 축소 컨트롤 생성
        let zoomControl = new kakao.maps.ZoomControl();
        map.addControl(zoomControl, kakao.maps.ControlPosition.LEFT);

        return map;
    } catch (error) {
        console.error('지도 초기화 중 오류 발생:', error);
        return null;
    }
}

// 전역 변수로 지도 객체 선언
let map = null;

// region 마커부분

// CCTV 데이터를 동적으로 가져오는 함수
function loadCCTVMarkers() {
    fetch('/include/server/cctvData.php')
        .then(response => response.json())
        .then(data => {
            // 기존 마커들 제거 (있다면)
            if (window.cctvMarkers) {
                window.cctvMarkers.forEach(marker => {
                    marker.setMap(null);
                });
            }

            window.cctvMarkers = [];
            window.cctvTrackers = [];

            // 모든 마커의 좌표를 저장할 배열
            let bounds = new kakao.maps.LatLngBounds();

            data.forEach((cctv, index) => {
                // LAT, LON 값 검증
                if (!cctv.lat || !cctv.lon ||
                    isNaN(parseFloat(cctv.lat)) || isNaN(parseFloat(cctv.lon))) {
                    console.warn(`CCTV ${cctv.cd_dist_obsv} (${cctv.nm_dist_obsv})의 좌표가 유효하지 않습니다: lat=${cctv.lat}, lon=${cctv.lon}`);
                    return; // 이 항목 건너뛰기
                }

                try {
                    // 마커 위치 생성
                    let lat = parseFloat(cctv.lat);
                    let lon = parseFloat(cctv.lon);

                    // 좌표 유효성 검사 추가
                    if (lat < 30 || lat > 40 || lon < 120 || lon > 140) {
                        console.warn(`CCTV ${cctv.nm_dist_obsv}의 좌표가 한반도 범위를 벗어남: lat=${lat}, lon=${lon}`);
                        return;
                    }

                    let markerPosition = new kakao.maps.LatLng(lat, lon);
                    // console.log(`마커 ${index + 1} 생성: ${cctv.nm_dist_obsv} at (${lat}, ${lon})`);

                    // 마커 생성
                    let marker = new TooltipMarker(markerPosition, cctv.nm_dist_obsv);

                    // 마커를 지도에 표시
                    marker.setMap(map);
                    // console.log(`마커 ${cctv.nm_dist_obsv}를 지도에 추가함`);

                    // 마커가 실제로 지도에 추가되었는지 확인
                    if (marker.getMap() === map) {
                        // console.log(`마커 ${cctv.nm_dist_obsv}가 지도에 성공적으로 추가됨`);
                    } else {
                        // console.warn(`마커 ${cctv.nm_dist_obsv}가 지도에 추가되지 않음`);
                    }

                    window.cctvMarkers.push(marker);

                    // bounds에 마커 위치 추가
                    bounds.extend(markerPosition);

                    // MarkerTracker 생성
                    let tracker = new MarkerTracker(map, marker);
                    tracker.run();
                    window.cctvTrackers.push(tracker);

                    // 마커 클릭 이벤트 추가
                    marker.getMarkerNode().addEventListener('click', function () {
                        console.log(`CCTV 마커 클릭됨: ${cctv.nm_dist_obsv}`);
                        // 기존 모달 닫기 (jQuery 사용)
                        $('#cctvModal').fadeOut(300);

                        // 포트 번호 계산 (CCTV 키에 따라 9001, 9002, 9003... 순서로 할당)
                        let port
                        if (index <= 2) {
                            port = window.cctvPort;
                        } else {
                            port = Number(window.cctvPort) + 1;
                        }

                        // CCTV 키는 CD_DIST_OBSV를 사용하고 포트 번호도 함께 전달
                        openCCTVModal(cctv.cd_dist_obsv, cctv.nm_dist_obsv, port);
                    });

                    // console.log(`마커 ${cctv.nm_dist_obsv} 생성 완료`);
                } catch (error) {
                    // console.error(`마커 ${cctv.nm_dist_obsv} 생성 중 오류:`, error);
                }
            });

            // 모든 마커가 보이도록 지도 영역 설정
            if (data.length > 0) {
                try {
                    // console.log('지도 영역 설정 시도 중...');
                    // console.log('생성된 마커 개수:', window.cctvMarkers.length);

                    // setBounds 대신 더 안전한 방법 사용
                    // 1. 유효한 마커들의 중심점 계산
                    let centerLat = 0;
                    let centerLng = 0;
                    let validCount = 0;

                    window.cctvMarkers.forEach((marker, index) => {
                        let pos = marker.getPosition();
                        let lat = pos.getLat();
                        let lng = pos.getLng();

                        // 좌표 유효성 재검사
                        if (lat >= 30 && lat <= 40 && lng >= 120 && lng <= 140) {
                            centerLat += lat;
                            centerLng += lng;
                            validCount++;
                            // console.log(`유효한 마커 ${index}: (${lat}, ${lng})`);
                        } else {
                            // console.warn(`무효한 마커 ${index}: (${lat}, ${lng})`);
                        }
                    });

                    // console.log(`유효한 마커 개수: ${validCount}`);

                    if (validCount > 0) {
                        centerLat /= validCount;
                        centerLng /= validCount;

                        // 2. 중심점으로 이동
                        let centerPosition = new kakao.maps.LatLng(centerLat, centerLng);
                        map.setCenter(centerPosition);

                        // 3. 적절한 줌 레벨 설정 (마커들이 모두 보이도록)
                        let maxDistance = 0;
                        window.cctvMarkers.forEach(marker => {
                            let pos = marker.getPosition();
                            let lat = pos.getLat();
                            let lng = pos.getLng();

                            if (lat >= 30 && lat <= 40 && lng >= 120 && lng <= 140) {
                                let distance = Math.sqrt(
                                    Math.pow(lat - centerLat, 2) +
                                    Math.pow(lng - centerLng, 2)
                                );
                                if (distance > maxDistance) {
                                    maxDistance = distance;
                                }
                            }
                        });

                        // 거리에 따른 줌 레벨 계산
                        let zoomLevel = 3; // 기본값
                        if (maxDistance > 0.01) zoomLevel = 2;
                        if (maxDistance > 0.05) zoomLevel = 1;
                        if (maxDistance < 0.001) zoomLevel = 4;

                        map.setLevel(zoomLevel);

                        // console.log('CCTV 마커 로드 완료:', data.length, '개');
                        // console.log('지도 중심점 설정됨:', centerLat, centerLng);
                        // console.log('줌 레벨 설정됨:', zoomLevel);
                    } else {
                        // console.warn('유효한 마커가 없습니다. 기본 중심점 사용');
                        // 기본 중심점 (우보)
                        map.setCenter(new kakao.maps.LatLng(37.433656425335826, 127.17337702179964));
                        map.setLevel(3);
                    }

                } catch (error) {
                    console.error('지도 영역 설정 중 오류:', error);

                    // 오류 발생 시 기본 중심점으로 이동
                    map.setCenter(new kakao.maps.LatLng(37.433656425335826, 127.17337702179964));
                    map.setLevel(3);
                    console.log('오류 발생 후 기본 중심점 이동 완료');
                }
            } else {
                console.warn('유효한 CCTV 데이터가 없습니다. LAT, LON 값이 모두 null이거나 유효하지 않을 수 있습니다.');
            }
        })
        .catch(error => {
            console.error('CCTV 데이터 로드 실패:', error);
            console.error('데이터베이스 연결이나 쿼리에 문제가 있을 수 있습니다.');
        });
}

// 페이지 로드 시 지도 초기화 및 CCTV 마커 로드
$(document).ready(function() {
    // 카카오맵 API가 로드될 때까지 대기
    function waitForKakaoMap() {
        if (typeof kakao !== 'undefined' && typeof kakao.maps !== 'undefined') {
            // 지도 초기화
            map = initializeMap();

            if (map) {
                let markersLoaded = false;

                // 지도 로드 완료 이벤트를 기다린 후 마커 로드 (한 번만)
                kakao.maps.event.addListener(map, 'tilesloaded', function() {
                    if (!markersLoaded) {
                        markersLoaded = true;
                        loadCCTVMarkers();
                    }
                });

                // 백업: 3초 후에도 마커 로드
                setTimeout(function() {
                    if (!markersLoaded && (!window.cctvMarkers || window.cctvMarkers.length === 0)) {
                        markersLoaded = true;
                        loadCCTVMarkers();
                    }
                }, 5000);
            } else {
                console.error('지도 초기화 실패');
            }
        } else {
            // 카카오맵 API가 아직 로드되지 않았으면 100ms 후 다시 시도
            setTimeout(waitForKakaoMap, 100);
        }
    }

    // 카카오맵 API 로드 대기 시작
    waitForKakaoMap();
});

// endregion
