<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2020/8/6
 * Time: 15:38
 */
$form = [
    [
        'name' => 'hidden',
        'title' => 'Hidden',
        'type' => 'hidden',
    ],
    [
        'name' => 'select',
        'title' => 'Select',
        'type' => 'select',
        'options' => [
            1 => 'Enable',
            0 => 'Disable',
        ],
    ],
    [
        'name' => 'input',
        'title' => 'Input',
        'type' => 'input',
    ],
    [
        'name' => 'password',
        'title' => 'Password',
        'type' => 'password',
    ],
    [
        'name' => 'textarea',
        'title' => 'Textarea',
        'type' => 'textarea',
    ],
    [
        'name' => 'date',
        'title' => 'Date',
        'type' => 'date',
    ],
    [
        'name' => 'datetime',
        'title' => 'Datetime',
        'type' => 'datetime',
    ],
    [
        'name' => 'editor',
        'title' => 'Editor',
        'type' => 'editor',
    ],
    [
        'name' => 'cover',
        'title' => 'Cover',
        'type' => 'cover',
    ],
    [
        'name' => 'image',
        'title' => 'Image',
        'type' => 'image',
    ],
];
?>
<div class="row">
    <div class="col-md-offset-2 col-md-8">
        <?= view('common.form', [
            'form' => $form,
        ])->render(); ?>
    </div>
</div>
