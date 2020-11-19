import $ from 'jquery'

const ready = window["confirm-ready"] || false;
if (!ready) {
    $(document).on("click", "*[data-page=confirm]", function (e) {
        return confirm("Are you sure?");
    });
}

window["confirm-ready"] = true;


