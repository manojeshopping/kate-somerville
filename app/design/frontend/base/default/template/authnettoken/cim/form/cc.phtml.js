<?php
/**
 * StoreFront Authorize.Net CIM Tokenized Payment Extension
 *
 * This source file is subject to commercial source code license of StoreFront Consulting, Inc.
 *
 * @category	SFC
 * @package    	SFC_AuthnetToken
 * @author      Garth Brantley
 * @website 	http://www.storefrontconsulting.com/
 * @copyright 	Copyright (C) 2009-2013 StoreFront Consulting, Inc. All Rights Reserved.
 * @license     http://www.storefrontconsulting.com/media/downloads/ExtensionLicense.pdf StoreFront Consulting Commercial License
 *
 */
?>
<?php $_code=$this->getMethodCode() ?>
<ul class="form-list" id="payment_form_<?php echo $_code ?>" style="display:none;">
    <li>
        <label for="<?php echo $_code ?>_cc_type" class="required"><em>*</em><?php echo $this->__('Credit Card Type') ?></label>
        <div class="input-box">
            <select id="<?php echo $_code ?>_cc_type" name="payment[cc_type]" class="required-entry validate-cc-type-select" onchange="jQuery('#authorizenet_cc_type').val(jQuery(this).val())">
                <option value=""><?php echo $this->__('--Please Select--')?></option>
            <?php $_ccType = $this->getInfoData('cc_type') ?>
            <?php foreach ($this->getCcAvailableTypes() as $_typeCode => $_typeName): ?>
                <option value="<?php echo $_typeCode ?>"<?php if($_typeCode==$_ccType): ?> selected="selected"<?php endif ?>><?php echo $_typeName ?></option>
            <?php endforeach ?>
            </select>
        </div>
    </li>
    <li>
        <label for="<?php echo $_code ?>_cc_number" class="required"><em>*</em><?php echo $this->__('Credit Card Number') ?></label>
        <div class="input-box">
            <input type="text" id="<?php echo $_code ?>_cc_number" name="payment[cc_number]" title="<?php echo $this->__('Credit Card Number') ?>" class="input-text validate-cc-number validate-cc-type" value="" onchange="jQuery('#authorizenet_cc_number').val(jQuery(this).val())" />
        </div>
    </li>
    <li id="<?php echo $_code ?>_cc_type_exp_div">
        <label for="<?php echo $_code ?>_expiration" class="required"><em>*</em><?php echo $this->__('Expiration Date') ?></label>
        <div class="input-box">
            <div class="v-fix">
                <select id="<?php echo $_code ?>_expiration" name="payment[cc_exp_month]" class="month validate-cc-exp required-entry" onchange="jQuery('#authorizenet_expiration').val(jQuery(this).val())">
                <?php $_ccExpMonth = $this->getInfoData('cc_exp_month') ?>
                <?php foreach ($this->getCcMonths() as $k=>$v): ?>
                    <option value="<?php echo $k?$k:'' ?>"<?php if($k==$_ccExpMonth): ?> selected="selected"<?php endif ?>><?php echo $v ?></option>
                <?php endforeach ?>
                </select>
            </div>
            <div class="v-fix">
                <?php $_ccExpYear = $this->getInfoData('cc_exp_year') ?>
                <select id="<?php echo $_code ?>_expiration_yr" name="payment[cc_exp_year]" class="year required-entry" onchange="jQuery('#authorizenet_expiration_yr').val(jQuery(this).val())">
                <?php foreach ($this->getCcYears() as $k=>$v): ?>
                    <option value="<?php echo $k?$k:'' ?>"<?php if($k==$_ccExpYear): ?> selected="selected"<?php endif ?>><?php echo $v ?></option>
                <?php endforeach ?>
                </select>
            </div>
        </div>
    </li>
    <?php echo $this->getChildHtml() ?>
    <?php if($this->hasVerification()): ?>
    <li id="<?php echo $_code ?>_cc_type_cvv_div">
        <label for="<?php echo $_code ?>_cc_cid" class="required"><em>*</em><?php echo $this->__('Card Verification Number') ?></label>
        <div class="input-box">
            <div class="v-fix">
                <input type="text" title="<?php echo $this->__('Card Verification Number') ?>" class="input-text cvv required-entry validate-cc-cvn" id="<?php echo $_code ?>_cc_cid" name="payment[cc_cid]" value="" onchange="jQuery('#authorizenet_cc_cid').val(jQuery(this).val())" />
            </div>
            <a href="#" class="cvv-what-is-this"><?php echo $this->__('What is this?') ?></a>
        </div>
    </li>
    <?php endif; ?>

</ul>
