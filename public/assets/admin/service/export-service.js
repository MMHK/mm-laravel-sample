define([
    "jquery",
    "promise",
    'xlsx',
], function ($, _promise, xlsx) {

    _promise.polyfill();

    var base_path = API_URI;

    return {
        list: function ($path) {
            return $.ajax({
                url: base_path + $path,
                dataType: "json",
                method: "get",
            })
                .then(function (json) {
                    return new Promise(function (resolve, reject) {
                        if (json && json.status) {
                            resolve(json.data);
                            return;
                        }

                        if (json && json.error && json.error.validate) {
                            reject(json.error.validate);
                            return;
                        }
                        if (json && json.error && json.error.msg) {
                            reject(json.error.msg);
                            return;
                        }

                        reject("error");
                    })
                });
        },
        export: function (data, $fields, $titles, filename = 'test') {
            var ws = xlsx.utils.json_to_sheet(
                data,
                {
                    header: $fields
                }
            );
            var range = xlsx.utils.decode_range(ws['!ref']);

            for (let c = range.s.c; c <= range.e.c; c++) {
                const header = XLSX.utils.encode_col(c) + '1';
                if ($titles[ws[header].v] !== undefined) {
                    ws[header].v = $titles[ws[header].v];
                }
            }

            var wb = xlsx.utils.book_new();

            xlsx.utils.book_append_sheet(wb, ws, filename);
            /* Trigger Download with `writeFile` */
            xlsx.writeFile(wb, filename + ".xlsx", {compression: true});
        }
    }
});