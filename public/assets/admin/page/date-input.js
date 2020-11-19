import $ from 'jquery'
import "jquery.inputmask"

const ready = window["date-input-ready"] || false;
if (!ready) {
    $("*[data-page=date-input]").inputmask({
        mask: "9999-99-99",
        placeholder: "YYYY-MM-DD"
    });

}

window["date-input-ready"] = true;


