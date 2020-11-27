PHP部分
===============

依赖
---------------
 - ``Laravel``, 整个项目使用``Laravel 6.0`` 开发


安装说明
---------------
0. 准备好开发环境, ``php cli >= 7.2`` & ``composer``
1. checkout SVN
   ```bash
   composer create-project mmhk/mm-laravel-sample [替换成你的安装目录] --repository-url=https://raw.githubusercontent.com/MMHK/mm-laravel-sample/main/create-project.json --remove-vcs
   ```
2. copy ``.env.example`` to ``.env``， 并修改好具体的配置项。

3. 更新依赖 
   ```bash
   composer update
   ```
4. 生成密匙
   ```bash
   php artisan key:generate
   ```
5. 启动PHP内置的web server运行项目
   ```bash
   php artisan serve
   ```
6. 根据返回的提示打开浏览器，默认是 ``http://localhost:8000/``


Laravel
---------------
 - [Laravel 6.0 文档](https://learnku.com/docs/laravel/6.x/releases/5121)


Composer 使用
---------------

由于 ``laravel`` 使用 ``Composer`` 方案实现包依赖及autoload，
所以使用前必须熟悉 ``Composer`` 的[使用方法](http://docs.phpcomposer.com/00-intro.html)。

 Composer 使用 ``composer.json`` 描述整个项目的依赖，当项目需要引入第三方包时，
 请注意在 ``require`` 节点添加引用；

  > 这里需要注意，当引入的包只是开发用环境使用的时候，

  > 请添加到``*-dev``的节点下。 

  > 例如安装 ``barryvdh/laravel-debugbar``

  > 请使用命令 ``composer require --dev barryvdh/laravel-debugbar``

SVN 提交
---------------
 已经统一配置了 SVN 忽略，不需要人工判定处理提交。

项目环境配置
---------------
 - 根据 laravel 的设计思想，项目中的一些主要敏感配置是不会提交进版本库的，项目会使用一个``.env``
   文件进行一个敏感配置的加载。而且当 ``.env``中的配置一旦被加载在整个运行时都是不可以修改的。
   当然项目会保留一个基础的样例文件 ``.env.example`` 用于说明有那个项目可以被设置。

 - ``.env.example`` (已经长期脱离实际文件，请看`.env.example`)
   ```
    # 项目基础配置部分
    APP_ENV=local #项目的运行环境标识，可用值 local 本地开发， testing demo/UAT 测试，production 产品/线上环境
    APP_DEBUG=true #是否开启 laravel 调试模式，关闭后不会出现具体报错信息
    APP_KEY=SomeRandomString # laravel 用户csf及加密cookie的随机密匙

    # 数据库链接部分
    DB_CONNECTION=mysql  # 数据库类型
    DB_HOST=127.0.0.1 # 数据库连接的host
    DB_DATABASE=homestead # 链接的数据库名
    DB_USERNAME=homestead # 数据库连接用户
    DB_PASSWORD=secret # 数据库链接密码
    
    # 缓存/持久化部分
    CACHE_DRIVER=file # 缓存驱动 可以用memcache/redis/file 等
    SESSION_DRIVER=file #session 存储驱动，可以用memcahe/redis/file 等
    QUEUE_DRIVER=sync #队列服务驱动

    # redis 配置
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    # 邮件服务器配置
    MAIL_DRIVER=smtp
    MAIL_HOST=mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
   ```
 - 全局帮助文件
   我们通过 Composer 的方式，引入了一个全局的[帮助文件](https://docs.phpcomposer.com/04-schema.html#Files)。
   
   存放在 ``app/Helper/helper.php``，此文件包含了一些全局使用的函数及常量定义。
   其中项目的环境标识常量就是在这个文件里面定义的。
   
   分别是 ``ENV_DEV | ENV_TEST | ENV_PRO``
   
 - 开发中判定环境的方法
   
   ```php
   <?php
   //在provider等中有$app实例的情况下
   $app->environment(ENV_DEV);
   //使用帮助function
   app()->environment(ENV_DEV);
   //全局接口[Facades]引用
   \App::environment(ENV_DEV);
   ```



开发协定
---------------
 - 开始编码前一定要读下 [PSR 1 ~ 4](https://github.com/PizzaLiu/PHP-FIG)，这很多
   PHP工程师总结出来的编码风格及规范，不是说里面都是圣经，但是根据此规范执行会使你少走很多弯路。
   **其中大部分风格规范都可以在``IDE``帮助下非常简单的完成。**
   
   放心如果你没有完全遵循以上范式，我们可以使用工具强制**优化**你的``code``, 这个[神奇的工具](https://github.com/FriendsOfPHP/PHP-CS-Fixer)。
   
代码注释
---------------
 - 项目是一个HK本地项目，所有提示及页面问题，默认使用繁体中文。繁体内容的校队请使用[OpenCC](http://opencc.byvoid.com/)进行转换。
 - 由于开发人员都是在中国内地，所以所有代码注释请使用简体中文。
 - 如果使用``PHPStorm IDE``进行开发，只需要在function/变量 前一行键入 ``/**[enter]`` 就会自动补完函数及变量的对应注释模板。


项目全局常量
---------------

所有的常量都会定义在 `app/Helper/helper.php`里面。

- `ENV_DEV`， 本地开发环境标识
- `ENV_TEST`， demo测试环境标识
- `ENV_PRO`， 上线产品环境标识


辅助插件
---------------
在项目默认的配置里面，引入了一些开发用的工具，他们会在本地开发环境下自动被引入。

 - [IDE-helper](https://github.com/barryvdh/laravel-ide-helper)，由于 ``Laravel`` 代码解耦非常厉害。
   这样使得很多IDE都无法识别出 关键字的依赖，如果强行使用IDE，很可能会自动引入依赖关系，这就跟``Laravel``设计原则相违背了。
   一般是使用这个插件去生成 ``Laravel`` 具体的依赖关系，这样IDE就好使了。具体的使用方法请查看官网。
   
   [laravel-ide-helper 使用说明](vendor/laravel-ide-helper.md)
   
 - [intervention/image](http://image.intervention.io/)，图片处理的封装。
   
   [intervention/image 使用说明](http://image.intervention.io/use/basics)
   
项目中间件
---------------
项目中间件都会存放在 `app\Http\Middleware` 目录下。

- `RenderLayout`，用于实现layout 布局的中间件。
  具体用法
  ```php
  <?php
  //方法1
  view('view')->with('layout', 'layout.default');
  //方法2
  \View::share('layout', 'layout.default');
  ```
  只需要在使用view的时候，输入`layout`标量，值为使用的layout的view alias 即可。
  在layout view中使用 ``<?= $content; ?>`` 输出原始view内容即可。
  
- `View Section`的纯PHP用法

  补充一个View Section的纯PHP方案，最主要是用来构建页面的渲染钩子，
  在layout中预留一些在之后流程才决定输出什么的 section 块。
  在主view 中使用
  ```php
  <?php View::startSection('script'); ?>
  <!-- 这里是需要的定义的代码块，这部分不会被这个view输出 -->
  <?php View::stopSection(); ?>
  ```
  在layout view中，使用 `View::yieldContent` 就可以延时输出 script section里面的内容。
  ```php
  <?= View::yieldContent('script'); ?>
  ```
  
  
第三方/自家用 服务
---------------
- `make:rule`，这个是一个自定义的 `artisan` 命令。
  具体的命令格式如下：
  ```bash
  php artisan make:rule [fullname of Model Class|包含命名空间完整命名的Model类]
  ```
  使用此命令，会生成一个Model Trait，
  可以在Model里面使用，对应的方法会生成 使用 `common.form`、`common.grid`的生成具体的表单及表格。
  
Admni UI
---------------
项目已经自带一套基于 [bootstrap-3.7](https://v3.bootcss.com/getting-started/) UI，
访问 `/admin`即可见到Admin SignIn 界面， 不过 admin 账户需要额外独立增加，否则你也没有账户登入。

创建admin 账户步骤：

0. 确认在 `MySQL` 中建立好数据库，并配置好 `.env` 中的 `MySQL` 配置信息。
1. 创建系统需要的表结构:
```bash
php artisan migrate
```
2. 使用 `make:admin` 命令创建 Admin 登入账户。
```bash
php artisan make:admin
```

编写测试用例
---------------

请到[这里](./PHPUnit.md)查看详情