// CCTV 페이지 로드 시 우측패널 자동 열기 및 CCTV 탭 활성화
$(document).ready(function() {
    // 우측 패널 열기
    $('#id_popup').animate({ right: '0px' });
    $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
    sessionStorage.setItem('pStatus', 'true');

    // CCTV 탭 활성화 및 내용 표시
    setTimeout(function() {
        // 모든 탭 비활성화
        $('.cs_pCate').removeClass('active');

        // CCTV 탭 활성화
        $('.cs_pCate[data-type="cctv"]').addClass('active');

        // CCTV 탭 내용 표시
        getPopupData('cctv');
    }, 0);
});
