<?php
/**
 * @global WC_Checkout $checkout
 */
$checkout = WC()->checkout(); ?>
<div class="woocommerce-billing-fields woocommerce-representative-fields">
    <h3><?php esc_html_e('Representative (person placing the order)', 'woocommerce-gateway-tillit'); ?></h3>
    <div class="woocommerce-representative-fields__field-wrapper">
        <?php
        $fields = $checkout->get_checkout_fields( 'representative' );
        foreach($fields as $key => $field){
            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
        }
        ?>
    </div>
</div>
