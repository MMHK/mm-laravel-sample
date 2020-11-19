<?php
/**
 * @var $list array|\Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
 * @var $heads array
 */
if ($list instanceof \Illuminate\Pagination\LengthAwarePaginator) {
    $list->appends(\Request::query());
}
?>
    <div class="form-group">
        <table class="table table-responsive table-bordered table-striped table-hover">
            <tr>
                <?php foreach ($heads as $row) { ?>
                    <th><?= $row['label']; ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($list as $row) { ?>
                <tr>
                    <?php foreach ($heads as $item) {
                        $render = \Arr::get($item, 'render');
                        $key    = \Arr::get($item, 'name');
                        ?>
                        <td><?= $render && is_callable($render) ?
                                call_user_func_array($render, [$row, $key]) : \Arr::get($row, $key); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php if ($list instanceof \Illuminate\Contracts\Pagination\Paginator) { ?>
    <div class="form-group">
        <p class="text-center">
            <?= sprintf('Showing %d to %d of %d entrles', $list->currentPage(), $list->lastPage(), $list->total()) ?>
        </p>
    </div>
    <div class="form-group">
        <?= $list->links(); ?>
    </div>
<?php } ?>