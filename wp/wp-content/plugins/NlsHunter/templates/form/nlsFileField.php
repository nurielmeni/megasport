<?php

/**
 * @wrapperClass
 * @label
 * @name
 * @buttonText
 * @accept
 * @textClass
 * @buttonClass
 * @validators
 * @mode text/button
 */
$mode = isset($mode) ? $mode : null;
$value = isset($value) ? $value : '';
$required =  isset($validators) && is_array($validators) && in_array('required', $validators) !== false;
?>
<div class="nls-field file <?= isset($wrapperClass) ? $wrapperClass : '' ?>">
  <input type="file" name="<?= isset($name) ? $name : '' ?>" accept="<?= isset($accept) ? $accept : '' ?>" class="hidden" validator="<?= is_array($validators) ? implode(' ', $validators) : '' ?>">
  <label class="w-100 flex justify-between <?= $mode === 'text' ? 'invisible' : '' ?>"><?= isset($label) ? $label : '' ?></label>

  <div class="flex file-picker items-center">
    <?php if ($mode !== 'text') : ?>
      <input type="text" readonly name="file-name" value="<?= isset($value) ? $value : '' ?>" class="border-2 truncate <?= isset($textClass) ? $textClass : '' ?>" validator="<?= is_array($validators) ? implode(' ', $validators) : '' ?>" aria-invalid="false" aria-required="<?= $required  ? 'true' : 'false' ?>">
    <?php endif; ?>
    <button type="button" class="<?= isset($buttonClass) ? $buttonClass : '' ?>"><?= isset($buttonText) ? $buttonText : '' ?></button>
    <?php if ($mode == 'text' && isset($iconSrc)) : ?>
      <img src="<?= $iconSrc ?>" class="select-indication mr-2 <?= isset($iconClass) ? $iconClass : '' ?>"></img>
    <?php endif; ?>
  </div>

  <div class="help-block"></div>
</div>