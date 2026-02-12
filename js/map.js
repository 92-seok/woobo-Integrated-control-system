// JavaScript Document
(async () => {
    try {
        var infowindow = new kakao.maps.InfoWindow({ zIndex: 1 });
        var mapContainer = document.getElementById('id_frame_box'), // 지도를 표시할 div
            mapOption = {
                center: new kakao.maps.LatLng(37.57300926064723, 127.12835737202485), // 지도의 중심좌표
                level: 3, // 지도의 확대 레벨
            };
        var positions = [];
        var map = new kakao.maps.Map(mapContainer, mapOption); // 지도를 생성합니다
        var bounds = new kakao.maps.LatLngBounds();

        /* 일반 지도와 스카이뷰로 지도 타입을 전환할 수 있는 지도타입 컨트롤을 생성합니다*/
        var mapTypeControl = new kakao.maps.MapTypeControl();
        map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPLEFT);

        /* 지도 확대 축소를 제어할 수 있는 줌 컨트롤을 생성합니다. */
        var zoomControl = new kakao.maps.ZoomControl();
        map.addControl(zoomControl, kakao.maps.ControlPosition.LEFT);

        document.addEventListener('DOMContentLoaded', async function (event) {
            await getMarker();

            $(document).on('click', '.cs_viewBtn', function () {
                var type = $(this).attr('data-type');
                var areaCode = $(this).attr('data-num');

                if (type == 'rain' || type == 'water' || type == 'dplace' || type == 'flood' || type == 'snow') {
                    window.location.href = 'data/dataFrame.php?dType=' + type + '&equip=' + areaCode;
                } else if (type == 'broad') {
                    window.location.href = 'broad/broadFrame.php?type=' + type + '&areaCode=' + areaCode;
                } else if (type == 'display') {
                    window.location.href = 'display/displayFrame.php?type=' + type + '&areaCode=' + areaCode;
                } else if (type == 'gate') {
                    window.location.href = 'gate/gateFrame.php?type=' + type + '&areaCode=' + areaCode;
                }
            });
        });
    } catch (e) {
        let frame = document.getElementById('id_frame_box');
        frame.style.backgroundImage = "url('/image/map.png')";
        frame.style.backgroundSize = '100% 100%';
        $('#id_loading').css('display', 'block');
    }

    async function getMarker() {
        const res = await fetch('/include/server/mainMap.php');
        const data = await res.json();
        console.log(data);
        if (data !== null || data !== '') {
            data.forEach((e) => {
                displayMarker(e.title, e.lat, e.lon, e.ImageFile, createInfo(e));
            });

            map.setBounds(bounds, 200, 200, 200, 200);
        }
    }

    var markers = [];
    function displayMarker(title, JHLat, JHLong, ImageFile, InfoBox) {
        //console.log( "add" ); // 디버그
        var latlng = new kakao.maps.LatLng(JHLat, JHLong);

        if (JHLat != null && JHLong != null) bounds.extend(latlng);
        // 마커 이미지의 이미지 크기 입니다
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
            infowindow.setContent(InfoBox);
            infowindow.open(map, marker);
        });
    }

    function removeMarker() {
        //console.log( "remove" ); // 디버그

        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        markers = [];
    }

    function createInfo(markerObject) {
        let infoDiv = document.createElement('div');
        let titleDiv = document.createElement('div');
        let dataTableDiv = document.createElement('div');
        let table = document.createElement('table');
        let tableTr = document.createElement('tr');
        let tableTh = document.createElement('th');
        let tableTd = document.createElement('td');
        let viewBtn = document.createElement('div');
        let type = null;

        infoDiv.className = 'cs_info';

        titleDiv = document.createElement('div');
        titleDiv.className = 'cs_title';
        titleDiv.style.backgroundColor = markerObject.color;
        titleDiv.innerText = markerObject.nmDistObsv;
        infoDiv.appendChild(titleDiv);

        dataTableDiv = document.createElement('div');
        table = document.createElement('table');
        table.style.width = '100%';
        table.style.border = 0;
        table.className = 'cs_infotable';

        switch (markerObject.gbObsv) {
            case '01':
                if (markerObject.today !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '금일';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} mm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '시간';
                    tableTr.appendChild(tableTh);
                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.now} mm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);
                }

                type = 'rain';
                break;

            case '02':
                if (markerObject.today !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '금일최고';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} M`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '시간';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.now} M`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);
                }

                type = 'water';
                break;

            case '03':
                if (markerObject.today !== null) {
                    for (const [key, value] of Object.entries(markerObject.today)) {
                        tableTr = document.createElement('tr');
                        tableTh = document.createElement('th');
                        tableTh.style.width = '40%';
                        tableTh.style.backgroundColor = markerObject.color;
                        tableTh.innerText = key;
                        tableTr.appendChild(tableTh);

                        table.appendChild(tableTr);

                        tableTd = document.createElement('td');
                        tableTd.innerText = `${value} mm`;
                        tableTr.appendChild(tableTd);

                        table.appendChild(tableTr);
                    }
                }

                type = 'dplace';
                break;

            case '04':
                if (markerObject.today !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '금일';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '시간';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.now} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);
                }

                type = 'soil';
                break;

            case '06':
                console.log(markerObject.today);
                if (markerObject.today !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '금일최고';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '현재';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '신적설';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.newSnow} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);
                }

                type = 'snow';
                break;

            case '08':
                if (markerObject.today !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '금일';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.today} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '40%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '시간';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.innerText = `${markerObject.now} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);
                }

                type = 'tilt';
                break;

            case '21':
                if (markerObject.water !== null) {
                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '50%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '침수위';
                    tableTr.appendChild(tableTh);

                    tableTd = document.createElement('td');
                    tableTd.colSpan = 3;
                    tableTd.innerText = `${markerObject.water} Cm`;
                    tableTr.appendChild(tableTd);

                    table.appendChild(tableTr);

                    tableTr = document.createElement('tr');
                    tableTh = document.createElement('th');
                    tableTh.style.width = '50%';
                    tableTh.style.backgroundColor = markerObject.color;
                    tableTh.innerText = '침수상태';
                    tableTr.appendChild(tableTh);

                    let floodState = Array.from(`000${markerObject.flood}`.slice(-3));
                    floodState.forEach((f) => {
                        tableTd = document.createElement('td');
                        tableTd.style.width = '16.6%';
                        tableTd.innerText = f == '1' ? 'O' : 'X';
                        tableTr.appendChild(tableTd);
                    });

                    table.appendChild(tableTr);
                }

                type = 'flood';
                break;

            case '17':
                type = 'broad';
                break;

            case '18':
                tableTr = document.createElement('tr');
                tableTh = document.createElement('th');
                tableTh.style.width = '50%';
                tableTh.style.backgroundColor = markerObject.color;
                tableTh.innerText = '표출상태';
                tableTr.appendChild(tableTh);

                tableTd = document.createElement('td');
                tableTd.innerText = markerObject.mode;
                tableTr.appendChild(tableTd);

                table.appendChild(tableTr);

                type = 'display';
                break;

            case '20':
                tableTr = document.createElement('tr');
                tableTh = document.createElement('th');
                tableTh.style.width = '50%';
                tableTh.style.backgroundColor = markerObject.color;
                tableTh.innerText = '차단기상태';
                tableTr.appendChild(tableTh);

                tableTd = document.createElement('td');
                tableTd.innerText = markerObject.mode;
                tableTr.appendChild(tableTd);

                table.appendChild(tableTr);

                type = 'gate';
                break;
        }

        tableTr = document.createElement('tr');
        tableTd = document.createElement('td');
        tableTd.colSpan = type != 'flood' ? 2 : 4;
        tableTd.style.padding = '3px';
        tableTd.style.backgroundColor = markerObject.color;

        viewBtn = document.createElement('div');
        viewBtn.className = `cs_viewBtn ${type}`;

        // 마커의 type 에 따라 viewBtn text 변경
        if (type === 'rain') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '데이터검색';
        }
        if (type === 'water') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '데이터검색';
        }
        if (type === 'flood') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '데이터검색';
        }
        if (type === 'snow') {
            viewBtn.setAttribute('dattype', type);
            viewBtn.setAttribute('datnum', markerObject.cdDistObsv);
            viewBtn.innerText = '데이터검색';
        }
        if (type === 'dplace') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '데이터검색';
        }
        if (type === 'display') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '전광판관리';
        }
        if (type === 'broad') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '방송관리';
        }
        if (type === 'gate') {
            viewBtn.setAttribute('data-type', type);
            viewBtn.setAttribute('data-num', markerObject.cdDistObsv);
            viewBtn.innerText = '차단기관리';
        }

        tableTd.appendChild(viewBtn);
        tableTr.appendChild(tableTd);

        table.appendChild(tableTr);

        dataTableDiv.appendChild(table);
        infoDiv.appendChild(dataTableDiv);

        return infoDiv;
    }
})();
