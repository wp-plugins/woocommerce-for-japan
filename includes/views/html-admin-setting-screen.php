<?php global $woocommerce; ?>
<form id="wc4jp-setting-form" method="post" action="">
<?php wp_nonce_field( 'my-nonce-key','wc4jp-setting');?>
<h3><?php echo __( 'Address Display Setting', 'woocommerce-4jp' );?></h3>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_yomigana"><?php echo __( 'Name Yomigana', 'woocommerce-4jp' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="yomigana" value="1" <?php $options['yomigana'] =get_option('wc4jp-yomigana') ;checked( $options['yomigana'], 1 ); ?>><?php echo __( 'Name Yomigana', 'woocommerce-4jp' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use input field for Yomigana of Name', 'woocommerce-4jp' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Company Name', 'woocommerce-4jp' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="company-name" value="1" <?php $options['company-name'] =get_option('wc4jp-company-name') ;checked( $options['company-name'], 1 ); ?>><?php echo __( 'Company Name', 'woocommerce-4jp' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use input field for Company Name', 'woocommerce-4jp' );?></p></td>
</tr>
</table>
<h3><?php echo __( 'Payment Method', 'woocommerce-4jp' );?></h3>
<table class="form-table">
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_yomigana"><?php echo __( 'BANK PAYMENT IN JAPAN', 'woocommerce-4jp' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="bankjp" value="1" <?php $options['bankjp'] =get_option('wc4jp-bankjp') ;checked( $options['bankjp'], 1 ); ?>><?php echo __( 'BANK PAYMENT IN JAPAN', 'woocommerce-4jp' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of BANK PAYMENT IN JAPAN', 'woocommerce-4jp' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Postal transfer', 'woocommerce-4jp' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="postofficebank" value="1" <?php $options['postofficebank'] =get_option('wc4jp-postofficebank') ;checked( $options['postofficebank'], 1 ); ?>><?php echo __( 'Postal transfer', 'woocommerce-4jp' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of Postal transfer', 'woocommerce-4jp' );?></p></td>
</tr>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="woocommerce_input_company"><?php echo __( 'Pay at store', 'woocommerce-4jp' );?></label>
    </th>
    <td class="forminp"><input type="checkbox" name="atstore" value="1" <?php $options['atstore'] =get_option('wc4jp-atstore') ;checked( $options['atstore'], 1 ); ?>><?php echo __( 'Pay at store', 'woocommerce-4jp' );?>
    <p class="description"><?php echo __( 'Please check it if you want to use the payment method of Pay at store', 'woocommerce-4jp' );?></p></td>
</tr>
</table>
<p class="submit">
   <input name="save" class="button-primary" type="submit" value="<?php echo __( 'Save changes', 'woocommerce' );?>">
</p>
</form>
