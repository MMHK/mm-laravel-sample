import $ from 'jquery'

const app = (()=>{
    const page_inited = global["page-inited"] || false;

    return {
        render_page(namespace) {

            if (page_inited) {
                return
            }

            //parse page
            $("*[data-page]").each((index, ele) => {
                var $ele = $(ele),
                    alias =  $ele.data("page");


                $ele.addClass("loading");

                import(
                    /* webpackMode: "eager" */
                    /* webpackInclude: /\.js$/ */
                    /* webpackExclude: /(webpack)/ */
                    `../${namespace}/page/${alias}.js`
                    );

                $ele.removeClass("loading");
            });

            global["page-inited"] = true;
        }
    }
})();

export default app