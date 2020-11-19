<?php
/**
 * @var $list \Illuminate\Pagination\LengthAwarePaginator
 */
$heads = \App\Models\Admin\User::map();

?>
<div class="row">
    <div class="col-md-12">
        <?= view('common.grid',
            compact('list', 'heads'))->render(); ?>
    </div>
</div>
