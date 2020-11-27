
## Laravel 5 IDE Helper Generator

### 安装

默认项目在 ``composer.json`` 里面已经添加对此项目的引用。
只需要执行一下命令即可安装
```
composer update
```

### 自动创建 laravel Facades 接口的依赖关系

你可以更新相关的依赖关系。(当你为项目增加了 vendor，或者在容器中注入了自己的类)

```bash
php artisan ide-helper:generate
```

> 注意: 如果文件 `bootstrap/compiled.php` 已经被创建 (一般是因为执行了 `php artisan compiled`), 
> 应该在生成依赖文件之前，先执行 `php artisan clear-compiled`.

你可以在 `composer.json` 中加入执行钩子，那在每次 `composer update` 之后都会重新生成相关的依赖引用。

> 译者注：在开发环境下建议不要这样做。

```js
"scripts":{
    "post-update-cmd": [
        "php artisan clear-compiled",
        "php artisan ide-helper:generate",
        "php artisan optimize"
    ]
},
```

你也可以将生成 `laravel-ide-helper` 的配置文件，去控制这个扩展的一些行为。（例如为 一些接口绑定特定的实现类）

```bash
php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config
```

构建器会尝试识别出实现的类，如果未能找到实现类，你可以在配置文件中指定某个接口的实现类。

有部分接口的实现需要使用的数据库连接，如果默认你没有一个可用的数据库连接，会导致某些接口不会生成实现类的引用。
你可以使用`-M`参数，启用一个内存 `SQLite` 驱动。

你可以选择包含一些帮助方法引用。当然这个不是默认启用的，可以用过使用参数 `--helpers (-H)` 去启用这个功能。
`laravel` 默认的帮助方法 `Illuminate/Support/helpers.php` 已经被包含进配置文件中，你可以自己修改配置文件去增加或者移除相关的帮助类。

### 自动构建 Model 的属性方法注释 （IDE友好提示）

> 你需要为你的项目添加依赖 `doctrine/dbal: ~2.3` ，才能使helper能读出你数据表的列属性。

```bash
composer require doctrine/dbal
```


如果你不想为你的`model`添加属性注释，你可以使用以下命令生成所有的属性注释 `php artisan ide-helper:models` ，默认是根据你的数据表的列生成 属性/关系/getters/setters。你可以使用参数 `--write (-W)` 将注释输出到你的modle定义文件。
默认helper会问你是否覆盖原来的model文件，或者输出到IDE友好的索引文件中(`_ide_helper_models.php`)。你也可以选择使用参数`--nowrite (-N)`不输出任何东西。
使用前请确认备份你的自己写的model文件。你可以使用 `--reset (-R)` 参数去跳过已经存在的注释。

```bash
php artisan ide-helper:models Post
```

```php
/**
 * An Eloquent Model: 'Post'
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $title
 * @property string $text
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \User $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Comment[] $comments
 */
```

默认情况下helper会扫描 `app/models` 目录，之后的参数告诉应该扫面哪个model

```bash
php artisan ide-helper:models Post User
```

你可以使用 `--dir` 参数修改扫面的目录（相对于项目根目录）

```bash
php artisan ide-helper:models --dir="path/to/models" --dir="app/src/Model"
```

当然可以通过修改配置文件(输出配置文件的方法：`php artisan vendor:publish`)的方式修改扫描目录。

部分model可以使用  `--ignore (-I)`参数被忽略

```bash
php artisan ide-helper:models --ignore="Post,User"
```

注意：可以使用完整命名空间的方式指定扫描的model `php artisan ide-helper:models "API\User"` 不带双引号的时候需要转义 (`Api\\User`)

## 为代码补充 容器注入的实例 Class引用

为容器方法生成注入实力的Class引用是可能的，参考链接[add support for factory design pattern](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata). 所以我们可以让 PhpStorm 理解容器中被调用的是神码实例。例如：`app('event')` 会返回一个实现 `Illuminate\Events\Dispatcher` 接口的对象，然后IDE就会提示出该对象的所有方法了。

> 译者注，确认是个很方便的功能。

``` bash
php artisan ide-helper:meta
```

```php
app('events')->fire();
\App::make('events')->fire();

/** @var \Illuminate\Foundation\Application $app */
$app->make('events')->fire();

// When the key is not found, it uses the argument as class name
app('App\SomeClass');
```

> 注意：你可能需要重启 PhpStorm，让IDE重新索引 `.phpstorm.meta.php` 文件。
> 如果运行时候发现 `FatalException`异常说没有找到某个Class的时候，请检查你的项目有配置文件，将没有引入的配置项去掉（例如 S3驱动 redis驱动等）
