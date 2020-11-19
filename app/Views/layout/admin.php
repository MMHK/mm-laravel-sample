<!DOCTYPE html>
<html lang="en" class="sb-admin-2">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?= $site_name; ?></title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= asset('/favicon.ico'); ?>" />
    <?php if (config('app.debug')) { ?>
    <link href="<?= asset('static/admin/dev/css/main.css') ?>" rel="stylesheet">
    <?php } else { ?>
    <link href="<?= asset('static/admin/dist/css/main.min.css') ?>" rel="stylesheet">
    <?php } ?>
    <script>
        var PAGE = {
            debug: <?= config('app.debug', false) ? 'true' : 'false'; ?>,
            meta : <?= json_encode($meta); ?>
        };
        var VERSION = "<?= app('url')->getVersion(); ?>";
        var BASE_PATH = '<?= url('/assets'); ?>';
        var API_URI = '<?= url('/admin/api'); ?>';
    </script>
</head>

<body>

<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= route('admin.dashboard') ?>"><?= $site_name; ?></a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="<?= route('admin.logout')?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>

        <?= view('admin.nav')->render(); ?>
    </nav>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $page_title; ?></h1>
            </div>
        </div>
        <?= isset($content)? $content : \View::yieldContent('content'); ?>
    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<?php if (config('app.debug')) { ?>
<script src="<?= asset('static/admin/dev/js/vendor.js'); ?>"></script>
<script src="<?= asset('static/admin/dev/js/main.js'); ?>"></script>
<?php } else { ?>
<script src="<?= asset('static/admin/dist/js/vendor.min.js'); ?>"></script>
<script src="<?= asset('static/admin/dist/js/main.min.js'); ?>"></script>
<?php } ?>
</body>

</html>
