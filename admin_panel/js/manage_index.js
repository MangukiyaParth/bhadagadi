jQuery(function () {
    $("#income_chart_loader").show();
    $(".income_cnt_loader").show();
    get_dashboard_data();
});

function loadchart(dates, amounts) {
    var options = {
        series: [{
            name: 'Earnings',
            data: amounts
        }],
        chart: {
            height: 300,
            // zoom: { enabled: false },
            toolbar: {
                show: true,
                offsetX: 0,
                offsetY: -40,
                tools: {
                    download: false,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: '<i class="mdi mdi-reload chart-reload"></i>',
                    customIcons: []
                },
            },
            type: 'bar',
            events: {
                beforeZoom: function (ctx) {
                    // we need to clear the range as we only need it on the iniital load.
                    ctx.w.config.xaxis.range = undefined
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        zoom: { enabled: false },
        legend: { show: false },
        colors: ["#0acf97"],
        markers: {
            size: 4,
            colors: ["#fff"],
            strokeColor: "#0acf97",
            strokeWidth: 2,
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            tooltip: { enabled: false },
            axisBorder: { show: false },
            type: 'datetime',
            categories: dates,
            range: 1328889675
        },
        // tooltip: {
        //     x: {
        //         format: 'dd/MM/yy HH:mm'
        //     },
        // },
        // fill: {
        //     type: "gradient",
        //     gradient: {
        //         type: "vertical",
        //         shadeIntensity: 0.3,
        //         inverseColors: false,
        //         opacityFrom: 0.45,
        //         opacityTo: 0.05,
        //         stops: [45, 100],
        //     },
        // },
    };

    var chart = new ApexCharts(document.querySelector("#income_chart"), options);
    chart.render();
    $("#income_chart_loader").hide();
}

function loaduserschart(dates, count) {
    var options = {
        series: [{
            name: 'Count',
            data: count
        }],
        chart: {
            height: 300,
            // zoom: { enabled: false },
            toolbar: {
                show: true,
                offsetX: 0,
                offsetY: -40,
                tools: {
                    download: false,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: '<i class="mdi mdi-reload chart-reload"></i>',
                    customIcons: []
                },
            },
            type: 'bar',
            events: {
                beforeZoom: function (ctx) {
                    // we need to clear the range as we only need it on the iniital load.
                    ctx.w.config.xaxis.range = undefined
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        zoom: { enabled: false },
        legend: { show: false },
        // colors: ["#0acf97"],
        markers: {
            size: 4,
            colors: ["#fff"],
            strokeColor: "#0acf97",
            strokeWidth: 2,
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            tooltip: { enabled: false },
            axisBorder: { show: false },
            type: 'datetime',
            categories: dates,
            range: 1328889675
        },
        // tooltip: {
        //     x: {
        //         format: 'dd/MM/yy HH:mm'
        //     },
        // },
        // fill: {
        //     type: "gradient",
        //     gradient: {
        //         type: "vertical",
        //         shadeIntensity: 0.3,
        //         inverseColors: false,
        //         opacityFrom: 0.45,
        //         opacityTo: 0.05,
        //         stops: [45, 100],
        //     },
        // },
    };

    var chart = new ApexCharts(document.querySelector("#user_chart"), options);
    chart.render();
    $("#income_chart_loader").hide();
}

async function get_dashboard_data(data) {

    if (data && data != null && data.status == true) {
        hideLoading();
        $(".income_cnt_loader").hide();
        var earning_rows = data.earning_rows[0];
        $("#today_earning").html(numberFormat(earning_rows.today_earning));
        $("#week_earning").html(numberFormat(earning_rows.week_earning));
        $("#month_earning").html(numberFormat(earning_rows.month_earning));
        loadchart(data.earning_chart_dates, data.earning_chart_amount);

        var user_cnt_rows = data.user_cnt_rows[0];
        $("#today_user_cnt").html(numberFormat(user_cnt_rows.today_user_cnt));
        $("#week_user_cnt").html(numberFormat(user_cnt_rows.week_user_cnt));
        $("#month_user_cnt").html(numberFormat(user_cnt_rows.month_user_cnt));
        loaduserschart(data.users_chart_dates, data.users_chart_cnt);
        return false;
    }
    else if (data && data != null && data.status == false) {
        hideLoading();
        return false;
    }
    else if (!data) {
        showLoading();
        var data = {
            operation: "get_dashboard_data"
        };
        doAPICall(data, get_dashboard_data);
    }
    return false;
}

function numberFormat(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}