<?php
/**
 * @var $row array
 * @var $name string
 * @var $title string
 * @var $value string|null
 */
$id = str_replace(['[',']'],'_', $name);
?>
<?php
if (\Arr::get($row, 'type') == 'hidden') { ?>
    <!--hidden-->
    <input name="<?= $name; ?>" type="hidden"
           class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'input') { ?>
    <!--input-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <input name="<?= $name; ?>" type="text"
           class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'password') { ?>
    <!--password-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <input  name="<?= $name; ?>" type="password"
            class="form-control" value="<?= old($name); ?>" id="<?= $id; ?>">
<?php } ?>

<?php if (\Arr::get($row, 'type') == 'textarea') { ?>
    <!--textarea-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <textarea name="<?= $name; ?>" rows="6"
              class="form-control" id="<?= $id; ?>"><?= $value; ?></textarea>
<?php } ?>
<?php
if (\Arr::get($row, 'type') == 'select') {
    $options = \Arr::get($row, 'options', []);
    ?>
    <!--select-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <select  id="<?= $id; ?>" name="<?= $name; ?>"
             class="form-control">
        <option value="" selected disabled>請選擇</option>
        <?php foreach ($options as $k => $item) { ?>
            <option <?= $k == $value ? 'selected': ''; ?> value="<?= $k; ?>"><?= $item; ?></option>
        <?php } ?>
    </select>
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'date') { ?>
    <!--date-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <input data-page="date-input" name="<?= $name; ?>" type="text"
           class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'datetime') { ?>
    <!--datetime-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <input data-page="datetime-input" name="<?= $name; ?>" type="text"
           class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'time') { ?>
    <!--time-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <input data-page="time-input" name="<?= $name; ?>" type="text"
           class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'image') { ?>
    <!--image-->
    <div data-page="image-upload">
        <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
        <div v-if="hasImage" class="col-xs-12 form-group clearfix">
            <img :src="url" class="img-responsive" />
        </div>
        <div class="col-xs-12 form-group clearfix">
            <vue-plupload text="Upload"
                          @added="handleAdded"
                          @progress="handleProgress"
                          @error="handleError"
                          @uploaded="handleUploaded"
                          :options="uploadOptions"></vue-plupload>
        </div>
        <input name="<?= $name; ?>"
               data-upload="<?= route('admin.api.upload.save'); ?>"
               data-url="<?= $value ? url($value): ''; ?>"
               type="hidden"
               class="form-control" value="<?= $value; ?>" v-model="url" id="<?= $name; ?>">
        <span class="text-warning">{{msg}}</span>
    </div>
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'cover') { ?>
    <!--image-->
    <div data-page="cover-upload">
        <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
        <div class="col-xs-12 form-group clearfix" style="display: none;">
            <img class="img-responsive" />
        </div>
        <div class="input-group">
            <input name="<?= $name; ?>"
                   type="text"
                   readonly
                   class="form-control" value="<?= $value; ?>" id="<?= $id; ?>">
            <span class="input-group-btn">
                <button class="btn btn-primary"
                        data-upload="<?= route('admin.api.upload.save'); ?>"
                        data-url="<?= $value ? url($value): ''; ?>"
                >Upload</button>
                <a class="btn btn-danger"><i class="fa fa-remove"></i></a>
            </span>
        </div>
        <span class="text-warning"></span>
    </div>
<?php } ?>

<?php
if (\Arr::get($row, 'type') == 'editor') { ?>
    <!--editor-->
    <label class="control-label" for="<?= $id; ?>"><?= $title; ?></label>
    <div data-page="html-editor">
        <textarea name="<?= $name; ?>" id="<?= $id; ?>_editor"><?= $value; ?></textarea>
    </div>
<?php } ?>
