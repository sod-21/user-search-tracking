(function ($) {
    function convertDateFormat(date) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();

        return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
    }

    jQuery.ajaxSetup({
        error: function (jqXHR, exception) {
            jQuery(".memberpress-sod-challenge").removeClass("loading");
        }
    })

    jQuery.ajax({
        type: "post",
        url: memberpresssodajax.ajaxurl,
        data: {
            "nonce": memberpresssodajax.nonce,
            "action": "memberpress_sod_data"
        },
        success: function (msg) {
            if (msg && msg != "error") {
                var data = JSON.parse(msg);
                jQuery(".memberpress-sod-challenge > h2").text(data.title);
                var headers = data.data.headers;
                var header_tds = headers.map(function (item) {
                    return `<th>${item}</th>`;
                });

                jQuery(".memberpress-sod-challenge .table thead > tr").html(
                    header_tds.join("")
                );

                var rows = data.data.rows;
                var rows_trs = Object.keys(rows).map(function (i) {
                    var item = rows[i];
                    var date = new Date(item.date * 1000);

                    return `<tr><td>${item.id}</td><td>${item.fname}</td><td>${item.lname}</td><td>${item.email}</td><td>${convertDateFormat(date)}</td></tr>`
                });

                jQuery(".memberpress-sod-challenge").removeClass("loading");
                jQuery(".memberpress-sod-challenge .table tbody").append(rows_trs.join(""));
                jQuery(".memberpress-sod-challenge .table").DataTable();
            } else {
                jQuery(".memberpress-sod-challenge").removeClass("loading");
            }
        }
    });

})(jQuery);