import $ from "jquery";
import Vue from "vue";
import VuePlupload  from "vue-plupload/src/components/vue-plupload.vue";

Vue.component('vue-plupload', VuePlupload);

const ready = window["date-image-upload"] || false;
if (!ready) {
    const init = function (ele) {
        var $ele = $(ele),
            $hidden = $ele.find("input[type=hidden]"),
            $api = $hidden.data("upload"),
            $url = $hidden.data("url");

        var vm = new Vue({
            el : ele,

            data: function () {
                return {
                    url: $url,
                    msg: ""
                };
            },

            computed: {
                hasImage: function () {
                    return (this.url && this.url.length > 0)
                },
                uploadOptions: function () {
                    return this.getUploadOption();
                }
            },

            methods: {
                getUploadOption: function() {
                    return {
                        multi_selection: false,
                        url: $api,
                        multipart_params: {
                            sync: 1
                        },
                        filters: {
                            max_file_size: '8mb',
                            mime_types: "image/*"
                        }
                    }
                },
                handleAdded: function (uploader, files) {
                    uploader.start()
                },

                handleProgress: function (uploader, file) {
                    vm.msg = "file:" + file.name + " uploading, progress:" + file.percent + "\n";
                },

                handleError: function (uploader, err) {
                    vm.msg = "upload error:\n======\n" + JSON.stringify(err) + "\n======\n";
                },

                handleUploaded: function (uploader, file, result) {
                    vm.msg = "";
                    var json = JSON.parse(result.response);
                    if (json && json.status) {
                        vm.url = json.data.path || "";
                    }
                }
            }
        });
    };

    const $target = $("*[data-page=image-upload]");

    $target.each(function (index, ele) {
        init(ele);
    });
}

window["date-image-upload"] = true;


