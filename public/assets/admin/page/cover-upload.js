import $ from 'jquery';
import plupload from "plupload";


const ready = global["date-cover-upload"] || false;
if (!ready) {
    function init(ele) {
        var $ele = $(ele),
            $btn = $ele.find("button.btn-primary"),
            $errorText = $ele.find("span.text-warning"),
            $img = $ele.find("img.img-responsive"),
            $imgWrapper = $ele.find(".form-group.clearfix"),
            $removeBtn = $ele.find("a.btn-danger"),
            $input = $ele.find("input"),
            api = $btn.data("upload"),
            defaultVal = $btn.data("url");


        function setResult(result) {
            if (result.image) {
                $img.attr("src", result.url);
                $input.val(result.url);
                $imgWrapper.show();
                return;
            }

            $input.val(result.url);
            $imgWrapper.hide();
        }

        function showError(msg) {
            $errorText.text(msg);
        }

        var uploader = new plupload.Uploader({
            runtimes: 'html5,html4',
            url: api,
            multi_selection: false,
            browse_button: $btn[0],
            multipart_params: {
                sync: 1
            },
            filters: {
                // mime_types : "image/*"
                mime_types:[ 
                  { title : "Upload files", extensions: "jpeg,png,jpg,gif,pdf,zip" }, 
                ]
            }
        });

        uploader.bind("FilesAdded", function (up, files) {
            up.start()
        });
        uploader.bind("UploadProgress", function (up, file) {
            showError("file:" + file.name + " uploading, progress:" + file.percent + "\n")
        });
        uploader.bind("FileUploaded", function (up, file, result) {
            var json = JSON.parse(result.response);
            if (json && json.status) {
                setResult(json.data);
                showError("");
            }
        });
        uploader.bind("Error", function (up, err) {
            showError("upload error:\n======\n" + JSON.stringify(err) + "\n======\n")
        });

        uploader.init();

        $removeBtn.on("click", function (e) {
            setResult({
                image: false,
                url: ""
            })
        });

        setResult({
            image: true,
            url: defaultVal
        });
    }

    $("*[data-page=cover-upload]").each(function (index, ele) {
        init(ele);
    });
}



global["date-cover-upload"] = true;
