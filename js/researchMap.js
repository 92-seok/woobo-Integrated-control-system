// JavaScript Document

////////////////////////////////////////////////////////////////////////////////
// USER DEFINED VARIABLE
////////////////////////////////////////////////////////////////////////////////
var lat = '36.2';
var lon = '127.8';
var zoom_level = 12;
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
// KAKAO MAP
////////////////////////////////////////////////////////////////////////////////
// 지도를 표시할 div
var mapContainer = document.getElementById('id_frame_box');
var mapOption = {
    center: new kakao.maps.LatLng(lat, lon), // 지도의 중심좌표
    level: zoom_level, // 지도의 확대 레벨
    maxLevel: 13, // 최대 확대 레벨
};

/* 지도를 생성합니다. */
var map = new kakao.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

/* 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다*/
var mapTypeControl = new kakao.maps.MapTypeControl();
map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);

/* 지도 확대 축소를 제어할 수 있는 줌 컨트롤을 생성합니다. */
var zoomControl = new kakao.maps.ZoomControl();
map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);

/* 지도를 클릭한 위치에 표출할 마커입니다 */
// 마커 이미지를 생성합니다
var marker = new kakao.maps.Marker({
    // 지도 중심좌표에 마커를 생성합니다
    position: map.getCenter(),
});
marker.setMap(map);
marker.setVisible(false);

/* 인포윈도우를 생성하고 지도에 표시합니다*/
var infowindow = new kakao.maps.InfoWindow({
    zIndex: 1,
    removable: false,
    disableAutoPan: false
});

/* 장소 검색 객체를 생성합니다. */
var ps = new kakao.maps.services.Places();

/* 로드뷰 객체를 생성합니다 */
var roadviewContainer = document.getElementById('roadview');
var roadview = new kakao.maps.Roadview(roadviewContainer);
var roadviewClient = new kakao.maps.RoadviewClient();

/* 좌표를 주소로 변환하는 지오코더 객체를 생성합니다 */
var geocoder = new kakao.maps.services.Geocoder();

////////////////////////////////////////////////////////////////////////////////
// PUBLIC STATIC VARIABLE
////////////////////////////////////////////////////////////////////////////////
var markers = [];
var infos = [];
var roadviewVisible = false;

////////////////////////////////////////////////////////////////////////////////
// PUBLIC STATIC EVENT
////////////////////////////////////////////////////////////////////////////////
window.onload = function () {
    //alert("onload");

    //document.getElementById("id_frame_box").style.width = window.innerWidth - 100 + 'px';
    //document.getElementById("id_frame_box").style.height = window.innerHeight - 100 + 'px';


    // 검색 타입 변경 시 UI 업데이트
    $(document).on('change', '#type', function() {
        var type = $(this).val();
        var keywordInput = $('#keyword');
        var categorySelect = $('#categorySelect');

        if (type === 'category') {
            categorySelect.show();
            keywordInput.attr('placeholder', '위도,경도 입력 (예: 37.5665,126.9780)');
        } else {
            categorySelect.hide();

            switch(type) {
                case 'adr':
                    keywordInput.attr('placeholder', '주소를 입력하세요 (예: 서울특별시 강남구 역삼동 또는 역삼동)');
                    break;
                case 'coord':
                    keywordInput.attr('placeholder', '위도,경도 순서로 입력하세요 (예: 37.5665, 126.9780)');
                    break;
                case 'key':
                    keywordInput.attr('placeholder', '장소명이나 업체명을 입력하세요 (예: 시청, 병원, 카페 등)');
                    break;
                case 'keycoord':
                    keywordInput.attr('placeholder', '키워드,위도,경도,반경(m) 순서로 입력하세요 (예: 카페,37.5665,126.9780,1000)');
                    break;
            }
        }
    });

    // 카테고리 선택 시 placeholder 업데이트
    $(document).on('change', '#categorySelect', function() {
        var categoryCode = $(this).val();
        var categoryName = $(this).find('option:selected').text();
        var keywordInput = $('#keyword');

        if (categoryCode) {
            keywordInput.attr('placeholder', `${categoryName} 검색: 위도,경도 (예: 37.5665,126.9780)`);
        } else {
            keywordInput.attr('placeholder', '위도,경도 입력 (예: 37.5665,126.9780)');
        }
    });

    // 초기 placeholder 설정
    $('#type').trigger('change');

    // 검색 결과 닫기 버튼 이벤트
    $(document).on('click', '#clearResults', function() {
        clearSearchResults();
    });


    // 로드뷰 닫기 버튼 이벤트
    $(document).on('click', '#roadviewClose', function() {
        if (roadviewVisible) {
            // 버튼 클릭 효과를 위한 약간의 지연
            $(this).css('transform', 'scale(0.95)');
            setTimeout(function() {
                toggleRoadview();
            }, 50);
        }
    });
};

window.onresize = function () {
    // 지도 크기 재조정
    setTimeout(function() {
        map.relayout();
    }, 100);

    // 부드럽게 이동
    map.panTo(new kakao.maps.LatLng(lat, lon));
};

function displayMarker(title, JHLat, JHLong, ImageFile, InfoBox) {
    //console.log( "add" ); // 디버그

    var latlng = new kakao.maps.LatLng(JHLat, JHLong);

    // 마커 이미지의 이미지 크기 입니다
    //var imageSize = new kakao.maps.Size(35, 51);
    var img_height = document.getElementById('maplevel') * 35;
    var img_width = img_height;
    var imageSize = new kakao.maps.Size(35, 51);

    // 마커 이미지를 생성합니다
    var markerImage = new kakao.maps.MarkerImage(ImageFile, imageSize);

    // 마커를 생성하고 지도에 표시합니다
    var marker = new kakao.maps.Marker({
        map: map,
        position: latlng, // 마커를 표시할 위치
        title: title, // 마커의 타이틀, 마커에 마우스를 올리면 타이틀이 표시됩니다
        image: markerImage, // 마커 이미지
    });

    markers.push(marker);

    // 마커에 클릭이벤트를 등록합니다
    kakao.maps.event.addListener(marker, 'click', function () {
        // 마커를 클릭하면 장소명이 인포윈도우에 표출됩니다
        var enhancedInfoBox = InfoBox +
            `<div style="padding:5px;"><button style="background:#4285f4;color:white;border:none;border-radius:3px;padding:5px 10px;cursor:pointer;" onclick="showRoadview(${JHLat}, ${JHLong})">로드뷰 보기</button></div>`;
        infowindow.setContent(enhancedInfoBox);
        infowindow.open(map, marker);

        // 마커가 화면 중앙에 오도록 조정
        setTimeout(function() {
            map.panTo(marker.getPosition());
        }, 100);
    });
}

/* 키워드 검색을 요청하는 함수입니다. */
function searchPlaces() {
    // 새로운 검색 시작 시 로드뷰 닫기 및 초기화
    if (roadviewVisible) {
        toggleRoadview();
        console.log('새 검색 시작 - 로드뷰 닫기');
    }

    var keyword = document.getElementById('keyword').value;
    let type = document.querySelector('#type').value;
    var categoryCode = document.getElementById('categorySelect').value;

    if (!keyword.replace(/^\s+|\s+$/g, '')) {
        if (type === 'coord') {
            alert('위경도를 입력해주세요! (예: 37.5665, 126.9780)');
        } else if (type === 'keycoord') {
            alert('키워드와 위경도를 입력해주세요! (예: 카페,37.5665,126.9780,1000)');
        } else if (type === 'category') {
            alert('위경도를 입력해주세요! (예: 37.5665,126.9780,1000)');
        } else {
            alert('키워드를 입력해주세요!');
        }
        return false;
    }

    // 카테고리 검색인데 카테고리가 선택되지 않은 경우
    if (type === 'category' && !categoryCode) {
        alert('카테고리를 선택해주세요!');
        return false;
    }

    // 입력 형식 검증
    if (type === 'coord') {
        var coordPattern = /^-?\d+\.?\d*\s*,\s*-?\d+\.?\d*$/;
        if (!coordPattern.test(keyword)) {
            alert('위경도 형식이 올바르지 않습니다.\n예시: 37.5665, 126.9780');
            return false;
        }
    } else if (type === 'keycoord') {
        var keycoordPattern = /^.+\s*,\s*-?\d+\.?\d*\s*,\s*-?\d+\.?\d*(\s*,\s*\d+)?$/;
        if (!keycoordPattern.test(keyword)) {
            alert('위경도 키워드 형식이 올바르지 않습니다.\n예시: 카페,37.5665,126.9780 또는 카페,37.5665,126.9780,1000');
            return false;
        }
    } else if (type === 'category') {
        var categoryPattern = /^-?\d+\.?\d*\s*,\s*-?\d+\.?\d*(\s*,\s*\d+)?$/;
        if (!categoryPattern.test(keyword)) {
            alert('위경도 형식이 올바르지 않습니다.\n예시: 37.5665,126.9780 또는 37.5665,126.9780,1000');
            return false;
        }
    }

    // 카테고리 검색인 경우 카테고리 코드를 앞에 붙이고 기본 반경 1000m 추가
    var queryData = keyword;
    if (type === 'category') {
        queryData = categoryCode + ',' + keyword + ',1000';
    }

    // 장소검색 객체를 통해 키워드로 장소검색을 요청합니다
    //ps.keywordSearch(keyword, placesSearchCB);
    $.ajax({
        url: '/map/loadMap.php',
        dataType: 'html',
        type: 'GET',
        data: { type: type, query: queryData },
        async: true,
        cache: false,
        success: function (result) {
            $('#resultTable').empty().html(result);
            // 검색 결과가 있으면 헤더 표시
            if (result.trim() !== '') {
                $('#resultHeader').show();
            } else {
                $('#resultHeader').hide();
                // 검색 결과가 없으면 메시지 표시
                $('#resultTable').html('<tr><td colspan="2" style="text-align:center;padding:20px;color:#666;font-size:16px;">검색 결과가 없습니다.<br><small style="color:#999;">다른 키워드로 검색해보세요.</small></td></tr>');
                $('#resultHeader').show();
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

$(document).on('click', '.trList', function () {
    lat = $(this).attr('data-y');
    lon = $(this).attr('data-x');

    let latlon = new kakao.maps.LatLng(lat, lon);

    let message = createLatLonPopupContent(latlon.getLat(), latlon.getLng());

    // 마커 위치를 클릭한 위치로 옮깁니다.
    marker.setPosition(latlon);
    marker.setTitle(`${latlon.getLat()}, ${latlon.getLng()}`);
    marker.setVisible(true);

    // 정보창 위치를 클릭한 위치로 옮깁니다.
    infowindow.open(map, marker);
    infowindow.setContent(message);
    infowindow.setPosition(latlon);

    map.setLevel(4);
    map.panTo(latlon);

    // 자동으로 주소 찾기 실행
    coordToAddress(latlon.getLat(), latlon.getLng());
});

/* 장소검색이 완료됐을 때 호출되는 콜백함수 입니다. */
function placesSearchCB(data, status, pagination) {
    if (status === kakao.maps.services.Status.OK) {
        // 정상적으로 검색이 완료됐으면
        // 검색 목록과 마커를 표출합니다
        displayPlaces(data);

        // 페이지 번호를 표출합니다
        displayPagination(pagination);
    } else if (status === kakao.maps.services.Status.ZERO_RESULT) {
        alert('검색 결과가 존재하지 않습니다.');
    } else if (status === kakao.maps.services.Status.ERROR) {
        alert('검색 결과 중 오류가 발생했습니다.');
    }
}

/* 검색 결과 목록과 마커를 표출하는 함수입니다. */
function displayPlaces(places) {
    var listEl = document.getElementById('placesList'),
        menuEl = document.getElementById('src_frame_box'),
        fragment = document.createDocumentFragment(),
        bounds = new kakao.maps.LatLngBounds(),
        listStr = '';

    // 검색 결과 목록에 추가된 항목들을 제거합니다
    removeAllChildNods(listEl);

    // 지도에 표시되고 있는 마커를 제거합니다
    removeMarker();

    for (let i = 0; i < places.length; i++) {
        // 마커를 생성하고 지도에 표시합니다
        var placePosition = new kakao.maps.LatLng(places[i].y, places[i].x),
            marker = addMarker(placePosition, i),
            itemEl = getListItem(i, places[i]); // 검색 결과 항목 Element를 생성합니다

        // 검색된 장소 위치를 기준으로 지도 범위를 재설정하기위해
        // LatLngBounds 객체에 좌표를 추가합니다
        bounds.extend(placePosition);

        // 마커와 검색결과 항목에 mouseover 했을때
        // 해당 장소에 인포윈도우에 장소명을 표시합니다
        // mouseout 했을 때는 인포윈도우를 닫습니다
        (function (marker, title) {
            kakao.maps.event.addListener(marker, 'mouseover', function () {
                displayInfowindow(marker, title);
            });

            kakao.maps.event.addListener(marker, 'mouseout', function () {
                infowindow.close();
            });

            itemEl.onmouseover = function () {
                displayInfowindow(marker, title);
            };

            itemEl.onmouseout = function () {
                infowindow.close();
            };
        })(marker, places[i].place_name);

        fragment.appendChild(itemEl);
    }

    // 검색결과 항목들을 검색결과 목록 Element에 추가합니다
    listEl.appendChild(fragment);
    menuEl.scrollTop = 0;

    // 검색된 장소 위치를 기준으로 지도 범위를 재설정합니다
    //map.setBounds(bounds);
}

// 검색결과 항목을 Element로 반환하는 함수입니다
function getListItem(index, places) {
    var el = document.createElement('li'),
        itemStr = '<span class="markerbg marker_' + (index + 1) + '"></span>' + '<div class="info">' + '   <h5>' + places.place_name + '</h5>';

    if (places.road_address_name) {
        itemStr += '    <span>' + places.road_address_name + '</span>' + '   <span class="jibun gray">' + places.address_name + '</span>';
    } else {
        itemStr += '    <span>' + places.address_name + '</span>';
    }

    itemStr += '  <span class="tel">' + places.phone + '</span>' + '</div>';

    el.innerHTML = itemStr;
    el.className = 'item';

    return el;
}

// 마커를 생성하고 지도 위에 마커를 표시하는 함수입니다
function addMarker(position, idx, title) {
    var imageSrc = 'https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_number_blue.png', // 마커 이미지 url, 스프라이트 이미지를 씁니다
        imageSize = new kakao.maps.Size(36, 37), // 마커 이미지의 크기
        imgOptions = {
            spriteSize: new kakao.maps.Size(36, 691), // 스프라이트 이미지의 크기
            spriteOrigin: new kakao.maps.Point(0, idx * 46 + 10), // 스프라이트 이미지 중 사용할 영역의 좌상단 좌표
            offset: new kakao.maps.Point(13, 37), // 마커 좌표에 일치시킬 이미지 내에서의 좌표
        },
        markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imgOptions),
        marker = new kakao.maps.Marker({
            position: position, // 마커의 위치
            image: markerImage,
        });

    marker.setMap(map); // 지도 위에 마커를 표출합니다
    markers.push(marker); // 배열에 생성된 마커를 추가합니다

    return marker;
}

// 지도 위에 표시되고 있는 마커를 모두 제거합니다
function removeMarker() {
    //console.log( "remove" ); // 디버그
    for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
    }

    markers = [];
}

// 검색결과 목록 하단에 페이지번호를 표시는 함수입니다
function displayPagination(pagination) {
    let paginationEl = document.getElementById('pagination');
    let fragment = document.createDocumentFragment();

    // 기존에 추가된 페이지번호를 삭제합니다
    while (paginationEl.hasChildNodes()) {
        paginationEl.removeChild(paginationEl.lastChild);
    }

    for (let i = 1; i <= pagination.last; i++) {
        var el = document.createElement('a');
        el.href = '#';
        el.innerHTML = i;

        if (i === pagination.current) {
            el.className = 'on';
        } else {
            el.onclick = (function (i) {
                return function () {
                    pagination.gotoPage(i);
                };
            })(i);
        }

        fragment.appendChild(el);
    }
    paginationEl.appendChild(fragment);
}

// 검색결과 목록 또는 마커를 클릭했을 때 호출되는 함수입니다
// 인포윈도우에 장소명을 표시합니다
function displayInfowindow(marker, title) {
    var content = '<div style="padding:5px;z-index:1;">' + title + '</div>';

    infowindow.setContent(content);
    infowindow.open(map, marker);
}

// 검색결과 목록의 자식 Element를 제거하는 함수입니다
function removeAllChildNods(el) {
    while (el.hasChildNodes()) {
        el.removeChild(el.lastChild);
    }
}

$('#searchBtn').click(function () {
    // 주소-좌표 변환 객체를 생성합니다
    var geocoder = new kakao.maps.services.Geocoder();

    //주소로 좌표를 검색합니다.
    geocoder.addressSearch($('#address').val(), function (result, status) {
        if (status === kakao.maps.services.Status.OK) {
            var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
            map.setCenter(coords);
            marker.setPosition(coords);
            var message = '' + result[0].y.substr(0, 7) + ' , ' + result[0].x.substr(0, 8);
            var resultDiv = document.getElementById('clickLatlng');
            resultDiv.innerHTML = message;
        }
    });
});

////////////////////////////////////////////////////////////////////////////////
// KAKAO MAP EVENT
////////////////////////////////////////////////////////////////////////////////

// 지도가 확대 또는 축소되면 마지막 파라미터로 넘어온 함수를 호출하도록 이벤트를 등록합니다
kakao.maps.event.addListener(map, 'zoom_changed', function () {
    //alert(map.getLevel());

    zoom_level = map.getLevel();

    //marker.height = map.getLevel() * 35;
    //marker.width = map.getLevel() * 35;

    //map.setCenter(new kakao.maps.LatLng(lat, lon));
    //map.relayout();
});

// 지도에 클릭 이벤트를 등록합니다.
// 지도를 클릭하면 마지막 파라미터로 넘어온 함수를 호출합니다.
kakao.maps.event.addListener(map, 'click', function (mouseEvent) {
    // 클릭한 위도, 경도 정보를 가져옵니다.
    let latlng = mouseEvent.latLng;
    lat = latlng.getLat().toFixed(4);
    lon = latlng.getLng().toFixed(4);

    console.log(`lat:${latlng.getLat()}\nlon:${latlng.getLng()}`);

    let message = createLatLonPopupContent(latlng.getLat(), latlng.getLng());

    // 마커 위치를 클릭한 위치로 옮깁니다.
    marker.setPosition(latlng);
    alt = marker.getAltitude();
    console.log(`alt:${alt}`);

    marker.setTitle(`${lat}, ${lon}`);
    marker.setVisible(true);

    // 정보창 위치를 클릭한 위치로 옮깁니다.
    infowindow.open(map, marker);
    infowindow.setContent(message);
    infowindow.setPosition(latlng);

    // 자동으로 주소 찾기 실행
    coordToAddress(latlng.getLat(), latlng.getLng());
});

function save_site() {
    /* TODO 저장 로직

	*/
}

// 위경도 팝업 내용 생성 함수
function createLatLonPopupContent(lat, lng) {
    return `<div style="display:flex;flex-direction:column;gap:0px;width:280px;max-width:90vw;box-sizing:border-box;">
        <div>
            <div style="text-align:center;">
                <input id="latlon_text" type="text" value="${lat.toFixed(6)}, ${lng.toFixed(6)}" style="width:100%;text-align:center;padding:6px;border:1px solid #ccc;border-radius:4px;font-size:16px;box-sizing:border-box;"></input>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;margin-top:0px;">
                <button style="font-size:14px;height:32px;background:#28a745;color:white;border:none;border-radius:4px;cursor:pointer;width:100%;font-weight:bold;" onclick="copy_latlon()">※ 위경도 복사</button>
            </div>
        </div>
        <div style="text-align:center;padding:4px;color:#666;font-size:13px;">주소 검색 중...</div>
    </div>`;
}

function copy_latlon() {
    const latlon_text = document.getElementById('latlon_text');
    const value = latlon_text.value;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(value).then(() => {
            latlon_text.select();
            alert("위경도 복사 성공");
        }).catch(err => {
            console.error("위경도 복사 실패:", err);
        });
    } else {
        latlon_text.select();
        if (document.execCommand('copy')) {
            alert("위경도가 복사되었습니다");
        }
    }
}

// 검색 결과 지우기 함수
function clearSearchResults() {
    $('#resultTable').empty();
    $('#resultHeader').hide();
    $('#placesList').empty();
    $('#pagination').empty();

    // 마커와 인포윈도우는 그대로 유지
    // 검색 결과 리스트만 닫기
}

// 로드뷰 토글 함수
function toggleRoadview() {
    roadviewVisible = !roadviewVisible;

    if (roadviewVisible) {
        $('.cs_frame_box').addClass('roadview_active');

        // 지도 크기 재조정
        setTimeout(function() {
            map.relayout();
        }, 300);
    } else {
        $('.cs_frame_box').removeClass('roadview_active');

        // 지도 크기 재조정
        setTimeout(function() {
            map.relayout();
        }, 300);
    }

    // 인포윈도우 내 로드뷰 버튼 텍스트도 업데이트
}

// 특정 위치의 로드뷰 표시 함수
function showRoadview(lat, lng) {

    // 로드뷰 열기
    if (!roadviewVisible) {
        toggleRoadview();
    }

    var position = new kakao.maps.LatLng(lat, lng);

    // 로드뷰 파노라마 ID를 가져와서 로드뷰를 띄워줍니다
    roadviewClient.getNearestPanoId(position, 50, function(panoId) {
        if (panoId === null) {
            alert('해당 위치에는 로드뷰 정보가 없습니다.');
            // 로드뷰가 없으면 다시 닫기
            if (roadviewVisible) {
                toggleRoadview();
            }
                    return;
        }
        roadview.setPanoId(panoId, position);
        });
}


// 좌표를 주소로 변환하는 함수
function coordToAddress(lat, lng) {
    var coords = new kakao.maps.LatLng(lat, lng);

    geocoder.coord2Address(coords.getLng(), coords.getLat(), function(result, status) {
        if (status === kakao.maps.services.Status.OK) {
            var detailAddr = !!result[0].road_address ? result[0].road_address.address_name : result[0].address.address_name;

            // 현재 인포윈도우 내용을 확인하고 주소 정보가 이미 있는지 체크
            var currentContent = infowindow.getContent();
            console.log('현재 인포윈도우 내용:', currentContent);

            // 이미 주소 정보가 있으면 제거하고 새로 추가
            if (currentContent && currentContent.includes('※ 주소를 클릭하면')) {
                var addressStartIndex = currentContent.indexOf('<div style="padding:5px;border-top:1px solid #ccc;background:#f9f9f9;">');
                if (addressStartIndex !== -1) {
                    currentContent = currentContent.substring(0, addressStartIndex);
                    console.log('기존 주소 정보 제거 후:', currentContent);
                }
            }

            // currentContent에서 "주소 검색 중..." 부분을 주소 정보로 교체
            var finalContent;
            if (currentContent.includes('주소 검색 중...')) {
                // "주소 검색 중..." 부분을 찾아서 주소 정보로 교체
                var searchingText = '<div style="text-align:center;padding:4px;color:#666;font-size:13px;">주소 검색 중...</div>';
                var timestamp = Date.now();
                var addressInfo = `<div style="text-align:center;padding:4px;border-top:1px solid #ccc;background:#f9f9f9;font-size:13px;">
                    <span id="addr_${timestamp}" data-address="${detailAddr}" style="color:#0066cc;cursor:pointer;text-decoration:underline;">${detailAddr}</span>
                    <div style="margin-top:4px;">
                        <button style="font-size:13px;height:28px;background:#ff6b6b;color:white;border:none;border-radius:4px;cursor:pointer;width:100%;font-weight:bold;margin-bottom:4px;" onclick="moveToAddress('${detailAddr}')">→ 주소로 이동</button>
                        <button style="font-size:13px;height:28px;background:#4285f4;color:white;border:none;border-radius:4px;cursor:pointer;width:100%;font-weight:bold;" onclick="showRoadview(${lat}, ${lng})">◉ 로드뷰 보기</button>
                    </div>
                </div>`;

                finalContent = currentContent.replace(searchingText, addressInfo);
                console.log('주소 검색 중... 텍스트를 주소 정보로 교체');
            } else {
                // 기존 방식 (수동 주소 찾기인 경우)
                if (!currentContent || currentContent.trim() === '') {
                    console.log('인포윈도우 내용이 비어있음. 기본 내용 재생성');
                    var coords = new kakao.maps.LatLng(lat, lng);
                    currentContent = createLatLonPopupContent(coords.getLat(), coords.getLng()).replace('<div style="text-align:center;padding:4px;color:#666;font-size:13px;">주소 검색 중...</div>', '');
                }

                var timestamp = Date.now();
                var addressInfo = `<div style="text-align:center;padding:4px;border-top:1px solid #ccc;background:#f9f9f9;font-size:13px;">
                    <strong>주소:</strong><br>
                    <span id="addr_${timestamp}" data-address="${detailAddr}" style="color:#0066cc;cursor:pointer;text-decoration:underline;">${detailAddr}</span>
                    <div style="margin-top:4px;">
                        <button style="font-size:13px;height:28px;background:#ff6b6b;color:white;border:none;border-radius:4px;cursor:pointer;width:100%;font-weight:bold;margin-bottom:4px;" onclick="moveToAddress('${detailAddr}')">→ 주소로 이동</button>
                        <button style="font-size:13px;height:28px;background:#4285f4;color:white;border:none;border-radius:4px;cursor:pointer;width:100%;font-weight:bold;" onclick="showRoadview(${lat}, ${lng})">◉ 로드뷰 보기</button>
                    </div>
                </div>`;

                finalContent = currentContent + addressInfo;
            }
            console.log('최종 인포윈도우 내용:', finalContent);

            // 마커를 건드리지 않고 인포윈도우만 업데이트하는 방식으로 변경
            console.log('마커 건드리지 않고 인포윈도우만 업데이트');

            // 인포윈도우가 열린 상태에서만 내용 업데이트
            if (infowindow.getMap()) {
                infowindow.setContent(finalContent);
                console.log('인포윈도우 내용만 업데이트 완료');
            } else {
                // 인포윈도우가 닫혀있으면 다시 열기
                infowindow.setContent(finalContent);
                infowindow.open(map, marker);
                console.log('인포윈도우 다시 열기 완료');
            }

            // 동적으로 생성된 주소 링크에 이벤트 추가
            setTimeout(function() {
                var addressElement = document.getElementById(`addr_${timestamp}`);
                if (addressElement) {
                    addressElement.addEventListener('click', function() {
                        var address = this.getAttribute('data-address');
                        moveToAddress(address);
                        console.log('주소 클릭:', address);
                    });
                } else {
                    console.log('주소 요소를 찾을 수 없음:', `addr_${timestamp}`);
                }
            }, 100);
        } else {
            alert('주소 변환에 실패했습니다.');
        }
    });
}

// 주소로 지도 이동하는 함수
function moveToAddress(address) {
    console.log('moveToAddress 호출됨, 주소:', address);

    geocoder.addressSearch(address, function(result, status) {
        console.log('주소 검색 결과:', result, 'status:', status);

        if (status === kakao.maps.services.Status.OK) {
            var coords = new kakao.maps.LatLng(result[0].y, result[0].x);

            console.log(`검색된 좌표: ${result[0].y}, ${result[0].x}`);
            console.log(`현재 지도 중심: ${map.getCenter().getLat()}, ${map.getCenter().getLng()}`);

            // 로드뷰가 열려있으면 닫기
            if (roadviewVisible) {
                toggleRoadview();
                console.log('주소 이동 시 로드뷰 닫기');
            }

            // 지도 중심을 해당 위치로 이동 (마커와 인포윈도우는 그대로 유지)
            map.setCenter(coords);
            map.setLevel(4);

            // 마커와 인포윈도우는 기존 위치에 그대로 유지
            // 사용자가 원래 클릭했던 위치 정보를 보존

            console.log(`주소로 이동 완료: ${address} (${result[0].y}, ${result[0].x})`);

            // 이동 후 지도 중심 확인
            setTimeout(function() {
                console.log(`이동 후 지도 중심: ${map.getCenter().getLat()}, ${map.getCenter().getLng()}`);
            }, 1000);
        } else {
            console.log('주소 검색 실패:', status);
            alert('주소 검색에 실패했습니다.');
        }
    });
}
