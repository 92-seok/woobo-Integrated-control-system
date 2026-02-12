<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
$KAKAO_API = getenv('KAKAO_API') ?? false;
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>위/경도 찾기</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        font-family: 'Malgun Gothic', dotum, '맑은 고딕', sans-serif;
        font-size: 20px;
        text-align: center;
    }

    html,
    body {
        width: 100%;
        height: 100%;
    }

    .cs_frame_box {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        -o-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        display: flex;
        overflow: hidden;
    }

    .id_frame_box {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        flex: 1 1 100%;
        transition: flex 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .cs_frame_box.roadview_active .id_frame_box {
        flex: 1 1 25%;
    }

    .roadview_container {
        width: 0;
        height: 100%;
        background: #fff;
        border-left: 2px solid #ccc;
        overflow: hidden;
        position: relative;
        flex: 0 0 0;
        transition: flex 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform: translateX(100%);
        opacity: 0;
    }

    .roadview_container #roadviewClose {
        opacity: 0;
        transform: translateX(100%);
        transition: opacity 0.2s ease 0.1s, transform 0.2s ease 0.1s;
        pointer-events: none;
    }

    .cs_frame_box.roadview_active .roadview_container {
        flex: 1 1 75%;
        transform: translateX(0);
        opacity: 1;
    }

    .cs_frame_box.roadview_active .roadview_container #roadviewClose {
        opacity: 1;
        transform: translateX(0);
        pointer-events: auto;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }


    #roadviewClose:hover {
        background: #cc0000;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .src_frame_box {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        margin: 0;
        background: rgba(255, 255, 255, 0.95);
        z-index: 5;
        border-radius: 12px 12px 0 0;
        box-shadow: 0 -2px 12px rgba(0,0,0,0.2);
        max-height: 50vh;
    }

    #keyword {
        padding: 4px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    #submit {
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        background: #f5f5f5;
        cursor: pointer;
    }

    #submit:hover {
        background: #e0e0e0;
    }

    #clearResults:hover {
        background: #cc0000 !important;
        transform: scale(1.05);
    }


    .trList {
        cursor: pointer;
    }

    #placesList li {
        list-style: none;
    }

    #placesList .item {
        position: relative;
        border-bottom: 1px solid #888;
        overflow: hidden;
        cursor: pointer;
        min-height: 65px;
    }

    #placesList .item span {
        display: block;
        margin-top: 4px;
    }

    #placesList .item h5,
    #placesList .item .info {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }

    #placesList .item .info {
        padding: 10px 0 10px 55px;
    }

    #placesList .info .gray {
        color: #8a8a8a;
    }

    #placesList .info .jibun {
        padding-left: 26px;
    }

    #placesList .info .tel {
        color: #009900;
    }

    #placesList .item .markerbg {
        float: left;
        position: absolute;
        width: 36px;
        height: 37px;
        margin: 10px 0 0 10px;
        background: url(https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/marker_number_blue.png) no-repeat;
    }

    #placesList .item .marker_1 {
        background-position: 0 -10px;
    }

    #placesList .item .marker_2 {
        background-position: 0 -56px;
    }

    #placesList .item .marker_3 {
        background-position: 0 -102px
    }

    #placesList .item .marker_4 {
        background-position: 0 -148px;
    }

    #placesList .item .marker_5 {
        background-position: 0 -194px;
    }

    #placesList .item .marker_6 {
        background-position: 0 -240px;
    }

    #placesList .item .marker_7 {
        background-position: 0 -286px;
    }

    #placesList .item .marker_8 {
        background-position: 0 -332px;
    }

    #placesList .item .marker_9 {
        background-position: 0 -378px;
    }

    #placesList .item .marker_10 {
        background-position: 0 -423px;
    }

    #placesList .item .marker_11 {
        background-position: 0 -470px;
    }

    #placesList .item .marker_12 {
        background-position: 0 -516px;
    }

    #placesList .item .marker_13 {
        background-position: 0 -562px;
    }

    #placesList .item .marker_14 {
        background-position: 0 -608px;
    }

    #placesList .item .marker_15 {
        background-position: 0 -654px;
    }

    .cs_frame_box {
        flex-direction: row;
    }

    .id_frame_box {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        flex: 1 1 100%;
        transition: flex 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .cs_frame_box.roadview_active .id_frame_box {
        flex: 1 1 25%;
    }

    .roadview_container {
        width: 0;
        height: 100%;
        background: #fff;
        border-left: 2px solid #ccc;
        overflow: hidden;
        position: relative;
        flex: 0 0 0;
        transition: flex 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform: translateX(100%);
        opacity: 0;
    }

    .cs_frame_box.roadview_active .roadview_container {
        flex: 1 1 75%;
        transform: translateX(0);
        opacity: 1;
    }

    #roadview {
        width: 100% !important;
        height: 100% !important;
        position: relative;
    }


    #research form > div {
        flex-direction: column;
        gap: 8px;
        padding: 0;
        align-items: stretch;
        width: 100%;
        margin: 0;
        box-sizing: border-box;
    }

    #type, #categorySelect {
        min-width: auto;
        width: 100%;
        padding: 14px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    #keyword {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    #submit {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        min-width: auto;
        background: #4285f4;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: bold;
    }

    #submit:hover {
        background: #3367d6;
    }

    #resultTable {
        max-height: 200px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
        display: block;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin: 0;
    }

    #placesList {
        max-height: 200px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
        margin: 0;
        padding: 0;
    }

    #resultTable::-webkit-scrollbar, #placesList::-webkit-scrollbar {
        width: 8px;
    }

    #resultTable::-webkit-scrollbar-track, #placesList::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #resultTable::-webkit-scrollbar-thumb, #placesList::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #resultTable::-webkit-scrollbar-thumb:hover, #placesList::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #resultTable tr {
        display: table-row;
    }

    #roadviewClose {
        top: 10px;
        right: 10px;
        padding: 12px 16px;
        font-size: 16px;
    }

    .infowindow {
        max-width: 280px !important;
    }

    #homeButton:hover {
        background: #4285f4;
        color: white;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    }
    </style>
</head>

<body>
    <div class="cs_frame_box">
        <!-- 홈 버튼 -->
        <button id="homeButton" onclick="location.reload()" style="position:fixed;top:10px;left:10px;z-index:1000;background:#fff;border:2px solid #4285f4;border-radius:50%;width:44px;height:44px;cursor:pointer;font-size:16px;color:#4285f4;box-shadow:0 2px 8px rgba(0,0,0,0.15);transition:all 0.2s ease;display:flex;align-items:center;justify-content:center;" title="새로고침">↻</button>
        <div class="id_frame_box map_container" id="id_frame_box">
            <div class="src_frame_box" id="src_frame_box">
                <div id="research">
                    <form onsubmit="searchPlaces(); return false;">
                        <div
                            style="display: flex;margin: auto;padding: unset;align-items: center;gap: 5px;">
                            <select name="type" id="type" style="flex: 0 0 auto;min-width: 120px;">
                                <option value="adr">주소 검색</option>
                                <option value="coord">위경도 검색</option>
                                <!-- <option value="key">키워드 검색</option> -->
                                <!-- <option value="keycoord">위경도->키워드</option> -->
                                <option value="category">카테고리 검색</option>
                            </select>
                            <select name="categorySelect" id="categorySelect" style="flex: 0 0 auto;min-width: 120px;display: none;">
                                <option value="">카테고리 선택</option>
                                <option value="MT1">대형마트</option>
                                <option value="CS2">편의점</option>
                                <option value="PS3">어린이집,유치원</option>
                                <option value="SC4">학교</option>
                                <option value="AC5">학원</option>
                                <option value="PK6">주차장</option>
                                <option value="OL7">주유소,충전소</option>
                                <option value="SW8">지하철역</option>
                                <option value="BK9">은행</option>
                                <option value="CT1">문화시설</option>
                                <option value="AG2">중개업소</option>
                                <option value="PO3">관공서</option>
                                <option value="AT4">관광명소</option>
                                <option value="AD5">숙박</option>
                                <option value="FD6">음식점</option>
                                <option value="CE7">카페</option>
                                <option value="HP8">병원</option>
                                <option value="PM9">약국</option>
                            </select>
                            <input type="text" value="" id="keyword" style="flex: 1;min-width: 400px;">
                            <button type="submit" id="submit" style="flex: 0 0 auto;white-space: nowrap;min-width: 50px;">검색</button>
                        </div>
                    </form>
                </div>
                <hr style="height:3px;border:0;border-top:2px solid #5F5F5F;margin:5px">
                <div id="resultHeader" style="display:none;position:relative;background:#f5f5f5;padding:8px;margin-bottom:5px;">
                    <strong>검색 결과</strong>
                    <button id="clearResults" style="position:absolute;right:8px;top:8px;background:#ff4444;color:white;border:none;border-radius:3px;padding:4px 8px;cursor:pointer;font-size:12px;">✕ 닫기</button>
                </div>
                <table id="resultTable">
                </table>
                <ul id="placesList"></ul>
                <div id="pagination"></div>
            </div>
        </div>
        <div class="roadview_container" id="roadview_container">
            <button id="roadviewClose" style="position:absolute;top:10px;right:10px;z-index:20;background:#ff4444;color:white;border:none;border-radius:6px;padding:12px 16px;cursor:pointer;font-size:14px;font-weight:bold;box-shadow:0 3px 8px rgba(0,0,0,0.2);transition:all 0.2s ease;">✕ 로드뷰 창 닫기</button>
            <div id="roadview" style="width:100%;height:100%;"></div>
        </div>
    </div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>

    <!-- Map 호출 -->
    <!-- <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=f4592e97c349ab41d02ff73bd314a201&libraries=services"></script> -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?= $KAKAO_API ?>&libraries=services,roadview">
    </script>
    <script src="/js/researchMap.js"></script>
</body>

</html>
