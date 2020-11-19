<?php
/**
 * @var $errors \Illuminate\Support\ViewErrorBag
 */
?>
<!DOCTYPE html>
<html lang="en" class="sb-admin-2">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?= asset('/favicon.ico'); ?>" />

    <title><?= $site_name; ?></title>

    <?php if (config('app.debug')) { ?>
    <link href="<?= asset('static/admin/dev/css/main.css') ?>" rel="stylesheet">
    <?php } else { ?>
    <link href="<?= asset('static/admin/dist/css/main.min.css') ?>" rel="stylesheet">
    <?php } ?>
    <script>
    var PAGE = {
        debug: <?= config('app.debug') ?>,
        meta : <?= json_encode($meta); ?>
    };
    var VERSION = "<?= app('url')->getVersion(); ?>";
    var BASE_PATH = '<?= url('/assets'); ?>';
    </script>
</head>

<body>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Please Sign In</h3>
                </div>
                <div class="panel-body">
                    <form role="form"
                          action="<?= route('admin.sign-in')?>" method="Post">
                        <?= csrf_field(); ?>
                        <fieldset>
                            <div class="form-group <?= $errors->has('login_id') ? 'has-error' : '' ?>">
                                <input class="form-control" placeholder="Login ID"
                                       name="login_id" value="<?= old('login_id') ?>" type="text" autofocus>
                                <span class="text-danger"><?= $errors->first('login_id') ?></span>
                            </div>
                            <div class="form-group <?= $errors->has('login_id') ? 'has-error' : '' ?>">
                                <input class="form-control" placeholder="Password"
                                       name="password"  value="<?= old('password') ?>" type="password">
                                <span class="text-danger"><?= $errors->first('password') ?></span>
                            </div>
                            <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (config('app.debug')) { ?>
<script src="<?= asset('static/admin/dev/js/vendor.js'); ?>"></script>
<script src="<?= asset('static/admin/dev/js/main.js'); ?>"></script>
<?php } else { ?>
<script src="<?= asset('static/admin/dist/js/vendor.min.js'); ?>"></script>
<script src="<?= asset('static/admin/dist/js/main.min.js'); ?>"></script>
<?php } ?>
</body>

</html>
