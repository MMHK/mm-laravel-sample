import $ from 'jquery'
import "jquery.inputmask"

const ready = window["datetime-input-ready"] || false;
if (!ready) {
    $("*[data-page=datetime-input]").inputmask({
        mask: "9999-99-99 99:99:99",
        placeholder: "YYYY-MM-DD hh:mm:ss"
    });
}

window["datetime-input-ready"] = true;



