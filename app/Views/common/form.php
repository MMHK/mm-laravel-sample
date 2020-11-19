<?php
/**
 * @var $form array
 * @var $values array
 * @var $errors \Illuminate\Support\ViewErrorBag
 */
echo csrf_field();

if (!isset($form)) {
    $form = [];
}
if (!isset($values)) {
    $values = [];
}

$getFieldName = function ($key) {
    $key_list = explode('.', $key);
    $path_first = array_shift($key_list);
    $key_list = array_map(function ($item){
        return "[{$item}]";
    }, $key_list);
     return $path_first . implode($key_list);
};

foreach ($form as $row) {

$key = \Arr::get($row, 'name');

$name = $getFieldName($key);
$title = \Arr::get($row, 'title');
$value = old($key, \Arr::get($values, $key,
        \Arr::get($row, 'value'))
    );
$i18n = \Arr::get($row, 'i18n', false);
$langMapping = [];
$defaultLang = false;
if ($i18n) {
    $service = app('\App\Services\i18nService');
    $langMapping = $service->getLangMap();
    $defaultLang = $service->getDefaultLang();
?>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <?php foreach ($langMapping as $langKey => $langName) {
        $id = $service->fieldID($key, $langKey);
        ?>
    <li class="<?= ($langKey == $defaultLang ? 'active' : '') ?>"
        role="presentation"><a href="#<?= $id ?>"
                               aria-controls="<?= $id ?>"
                               role="tab"
                               data-toggle="tab"><?= $langName; ?></a></li>
    <?php } ?>
</ul>
<?php
$errorFlag = false;
$errorList = [];
foreach ($langMapping as $langKey => $langName) {
    $_key = $service->fieldKey($key, $langKey);
    if ($errors->has($_key)) {
        $errorFlag = true;
        $errorList[] = $errors->first($_key);
    }
}
?>
<!-- Tab panes -->
<div class="tab-content <?= $errorFlag ? 'has-error' : ''; ?>">
    <?php foreach ($langMapping as $langKey => $langName) {
        $id = $service->fieldID($key, $langKey);
        $_key = $service->fieldKey($key, $langKey);
        $args = compact('errors', 'row', 'title');
        $args['name'] = $service->fieldName($key, $langKey);
        $args['key'] = $_key;
        $value = old($_key, \Arr::get($values, $_key));
        $args['value'] = $value;
    ?>
    <div role="tabpanel"
         class="tab-pane <?= ($langKey == $defaultLang ? 'active' : '') ?>" id="<?= $id ?>">
        <div class="form-group <?= $errors->has($_key) ? 'has-error' : ''; ?>">
            <?= view('common.form-fields', $args)->render(); ?>
        </div>
    </div>
    <?php } ?>
    <?php foreach ($errorList as $errMsg) { ?>
    <span class="text-danger"><?= $errMsg; ?></span>
    <?php } ?>
</div>
<?php } else { ?>
<?= view('common.form-line', compact('errors', 'row', 'name', 'title', 'value', 'key'))->render(); ?>
<?php } ?>


<?php } ?>


