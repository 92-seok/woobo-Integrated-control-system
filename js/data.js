$(document).ready(function (e) {
    $(document).on('click', '#id_search', function (e) {
        e.stopPropagation();
        sub_mit('data');
    });

    $(document).on('change', '#id_select', function (e) {
        e.stopPropagation();
        sub_mit('data');
    });

    $(document).on('click', '#id_search_graph', function (e) {
        e.stopPropagation();
        sub_mit('graph');
    });

    $(document).on('change', '#id_select_graph', function (e) {
        e.stopPropagation();
        sub_mit('graph');
    });

    $(document).on('click', '#id_excel', function () {
        let url = $('#id_form').serialize();

        if ($('#id_addr').val() == 'Time.php') {
            url = 'TimeExcel' + url.substr(9, url.length - 9);
        } else if ($('#id_addr').val() == 'Day.php') {
            url = 'DayExcel' + url.substr(8, url.length - 8);
        } else if ($('#id_addr').val() == 'Month.php') {
            url = 'MonthExcel' + url.substr(10, url.length - 10);
        } else if ($('#id_addr').val() == 'Year.php') {
            url = 'YearExcel' + url.substr(9, url.length - 9);
        } else if ($('#id_addr').val() == 'Period.php') {
            url = 'PeriodExcel' + url.substr(11, url.length - 11);
        }

        url = 'table/excel/' + url.replace('&', '?');

        window.location.href = url;
    });
});

$(document).on('click', '#id_gBtn', function () {
    $('#id_gBtn').toggleClass('gOpen gClose');
    if ($('#id_gBtn').hasClass('gOpen')) {
        $('#id_myChart').hide();
    } else {
        $('#id_myChart').show();
    }
});

function sub_mit(type) {
    let url = $('#id_form').serialize();
    let dType = document.getElementsByName('dType');
    let area = document.getElementsByName('area')[0];
    let idx = 0;

    if (type == 'data') {
        if ($('#id_sDate2').val() == undefined || $('#id_sDate2').val() == '') {
            let sDate = $('#id_form #id_sDate').val();
            let year = sDate.substring(0, 4);
            let month = sDate.substring(5, 7);
            let day = sDate.substring(8, 10);
            url = url + '&year=' + year + '&month=' + month + '&day=' + day;
        } else {
            let sDate1 = $('#id_form #id_sDate1').val();
            let year1 = sDate1.substring(0, 4);
            let month1 = sDate1.substring(5, 7);
            let day1 = sDate1.substring(8, 10);
            let sDate2 = $('#id_form #id_sDate2').val();
            let year2 = sDate2.substring(0, 4);
            let month2 = sDate2.substring(5, 7);
            let day2 = sDate2.substring(8, 10);
            url = url + '&year1=' + year1 + '&month1=' + month1 + '&day1=' + day1 + '&year2=' + year2 + '&month2=' + month2 + '&day2=' + day2;
        }
    }

    switch (dType[0].value) {
        case 'rain':
            idx = 0;
            dType = 'rain';
            break;
        case 'water':
            idx = 1;
            dType = 'water';
            break;
        case 'dplace':
            idx = 2;
            dType = 'dplace';
            break;
        case 'snow':
            idx = 3;
            dType = 'snow';
            break;
        case 'flood':
            idx = 4;
            dType = 'flood';
            break;
        case 'soil':
            idx = 5;
            dType = 'soil';
            break;
        case 'tilt':
            idx = 6;
            dType = 'tilt';
            break;
        default:
            idx = 0;
            dType = 'rain';
    }

    url = url.substr(5, url.length - 5);
    if (type == 'data') url = 'table/' + url.replace('&', '?');
    else {
        idx += 5;
        url = 'graph/' + url.replace('&', '?');
    }

    getFrame(`${url}&dType=${dType}&CD_DIST_OBSV=${area}`, pType, idx, 'false');
}
