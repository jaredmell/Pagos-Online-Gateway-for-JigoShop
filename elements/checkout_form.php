<?php print $description; ?>
<p class="form-row form-row-first">
  <label for="pagosonline-id_type" class=""><?php print __('Identification type', 'pagosonline'); ?><span class="required">*</span></label>
  <select name="pagosonline_id_type" id="pagosonline-idtype" class="pagosonline">
    <option value=""><?php print __('Select', 'pagosonline'); ?>…</option>
    <?php foreach ($id_type_options as $id => $label): ?>
    <option value="<?php print $id; ?>"<?php print ($id_type == $id ? ' selected="selected"' : ''); ?>><?php print $label; ?></option>
    <?php endforeach; ?>
  </select>
</p>
<p class="form-row form-row-last">
  <label for="pagosonline-id_number"><?php print __('Identification number', 'pagosonline'); ?><span class="required">*</span></label>
  <span class="input-text"><input type="input" name="pagosonline_id_number" id="pagosonline-id_number" placeholder="<?php print __('Identification number', 'pagosonline'); ?>" value="<?php print $id_number; ?>"></span>
</p>
<div class="clear"></div>
<p class="form-row form-row-first">
  <label for="pagosonline-office_phone"><?php print __('Office phone', 'pagosonline'); ?></label>
  <span class="input-text"><input type="input" name="pagosonline_office_phone" id="pagosonline-office_phone" placeholder="<?php print __('Office phone', 'pagosonline'); ?>" value="<?php print $office_phone; ?>"></span>
</p>
<p class="form-row form-row-last">
  <label for="pagosonline-mobile_phone"><?php print __('Mobile phone', 'pagosonline'); ?></label>
  <span class="input-text"><input type="input" name="pagosonline_mobile_phone" id="pagosonline-mobile_phone" placeholder="<?php print __('Office phone', 'pagosonline'); ?>" value="<?php print $mobile_phone; ?>"></span>
</p>
<div class="clear"></div>