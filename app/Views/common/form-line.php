<?php
/**
 * @var $errors \Illuminate\Support\ViewErrorBag
 * @var $name string
 * @var $key string
 * @var $value string
 * @var $title string
 * @var $row array
 */
?>
<div class="form-group <?= $errors->has($key) ? 'has-error' : ''; ?>">
    <?= view('common.form-fields', compact('value', 'title', 'name', 'row'))->render(); ?>

    <span class="text-danger"><?= $errors->first($key); ?></span>
</div>