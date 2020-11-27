# 前端部分

## JS文件规范

已经加入 `webpack` 及 `babel` 支持，请优先使用ES6语法。

**前端代码都需要编译才能正常执行。**

## 项目结构

由于项目使用的是后端`MVC`模式组件，所以前端的大部分页面都会以`view`的形式嵌入到项目中。
文件分布规则如下：
- `app/Views` 此目录下存放大部分的前端页面。
- `public/assets` 此目录存放 前端使用到的 `js`/`css`/`image`/`font` 等静态资源。
    - `public/assets/admin` 后台使用的JS静态资源。
    - `public/assets/common` 前/后台共用的部分静态资源，大部分公共组件都会放到此目录。
    - `public/assets/default` 前台使用的JS静态资源
        - `public/assets/static/style` 前台css存放的地方
        - `public/assets/static/images` 前台存放图片/图标的地方
        - `public/assets/static/fonts` 前台字体文件  

## 项目依赖

已经完全使用`npm` 管理包依赖，需要的请自定安装。

- [jQuery](https://jquery.com/) [参考文档](http://www.css88.com/jqapi-1.9/)，大量使用。
- [bootstrap](http://getbootstrap.com/) 整个项目的基础UI库 [参考文档](http://v3.bootcss.com/)
- [metisMenu](https://github.com/onokumus/metisMenu) `bootstrap` 引入的菜单组件。
- [Vue](https://cn.vuejs.org/) 在一些联动表单中引入的`MVVM`库【注意这个项目使用的是最新Vue版本，API有大量的不同】 [参考文档](https://cn.vuejs.org/v2/api/)
- [VeeValidate](http://vee-validate.logaretm.com) Vue的表单验证组件  [参考文档](http://vee-validate.logaretm.com/index.html#basic-example)
- [vue-i18n](https://kazupon.github.io/vue-i18n/) Vue 多语言处理


## JS引入方式


匹配每个页面中带有 ``data-page`` 的元素的 ``data-page`` 属性，**不建议在不同的页面重复使用**：

```HTML
<div data-page="index"></div>
```
> 标签代表引入``public/assets/[namespace]/page/index.js``
>
> **注意:在该架构中 ``page`` 只会加载一次。**
>
> 现在的架构中没有对 ``page`` 进行严格的约束，是比较自由的引入方式。

示例:

```javascript
import $ from "jquery";

const $console = $(".console-wrapper"),
    $win = $(window);

$win.on("resize", () => {
    $console.text("resize!!!!");
});

```

页面中：

```HTML
<div data-page="mypage">
    <div class="console-wrapper"></div>
</div>

```


## 编译前端代码

- 准备好 [NodeJS](https://nodejs.org/dist/v12.18.3/node-v12.18.3-x64.msi)  环境。
- 常驻编译开发服务，`npm run serve`
- 产品环境打包，`npm run build`
