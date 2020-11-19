import $ from "jquery";
import "trumbowyg/dist/ui/icons.svg"
import "trumbowyg/dist/ui/trumbowyg.min.css"
import(
    /* webpackChunkName: "trumbowyg" */
    /* webpackMode: "lazy" */
    "trumbowyg/dist/trumbowyg.min").then(() => {

    const ready = window["date-html-editor"] || false;
    if (!ready) {
        $.trumbowyg.svgPath = __webpack_public_path__ + '/node_modules/trumbowyg/dist/ui/icons.svg';

        const $target = $("*[data-page=html-editor]");

        $target.each(function (index, ele) {
            const $wrapper = $(ele).find("textarea");

            $wrapper.trumbowyg({
                btns: [
                    ['viewHTML'],
                    ['historyUndo', 'historyRedo'],
                    ['fontsize'],
                    ['foreColor', 'backColor'],
                    ['strong', 'em', 'del'],
                    ['superscript', 'subscript'],
                    ['link'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                    ['removeformat']
                ],
                removeformatPasted: true,
                tagsToRemove: ['script', 'link'],
                plugins:{
                    fontsize: {
                        sizeList: [
                            '0.5em',
                            '1em',
                            '1.5em',
                            '2em'
                        ]
                    }
                }
            });
        });
    }


    window["date-html-editor"] = true;
});




