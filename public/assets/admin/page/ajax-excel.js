import $ from 'jquery'

import(
    /* webpackChunkName: "xlsx" */
    /* webpackMode: "lazy" */
    /* webpackExports: ["default", "named"] */
    "xlsx/dist/xlsx.full.min.js")
    .then((XLSX) => {
        $("[data-page='ajax-excel']").each(function (i, ele) {
            const $ele = $(ele),
                url = $ele.attr("href");

            $ele.on("click", function (e) {
                e.preventDefault();

                $ele.prop("disabled", true);

                $.ajax({
                    url: url,
                    dataType: "json",
                    method: "GET"
                }).done(function (res) {
                    if (res && res.status) {
                        const wb = XLSX.utils.book_new(),
                            ws = XLSX.utils.aoa_to_sheet(res.data),
                            now = new Date();
                        XLSX.utils.book_append_sheet(wb, ws, "sheet");
                        XLSX.writeFile(wb, now.getTime() + ".xlsx");
                    }
                }).always(function () {
                    $ele.prop("disabled", false);
                })
            })
        });
    });


