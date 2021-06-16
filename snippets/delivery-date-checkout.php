<?php

add_action('wp_enqueue_scripts', 'enqueue_date_picker');
add_action('woocommerce_after_order_notes', 'add_delivery_date_field', 10, 1);
add_action('woocommerce_checkout_process', 'checkout_validate_delivery_date');
add_action('woocommerce_checkout_update_order_meta', 'add_delivery_date_to_order_meta');


/**
 * Enqueue datepicker js
 *
 * @return void
 */
function enqueue_date_picker() {
    // Only on front-end and checkout page
    if(is_admin() || !is_checkout()) return;
    wp_enqueue_script('jquery-ui-datepicker');
}


/**
 * Add Delivery date field to Checkout page
 *
 * @return void
 */
function add_delivery_date_field($checkout) {

    date_default_timezone_set('Europe/Oslo');
    $dateoptions = array('' => __('Select Pickup Date', 'tillit-payment-gateway'));

    echo '<div id="delivery-date">';
    echo '<h3>'.__('Delivery Date', 'tillit-payment-gateway').'</h3>';

    echo '
    <script>
        jQuery(function($){
            $("#delivery-date-picker").datepicker({minDate: 0, dateFormat: "yy-mm-dd"});
        });
    </script>
    <style>
        #ui-datepicker-div {
            background: #fff;
        }
    </style>';

   woocommerce_form_field('delivery_date', array(
            'type'          => 'text',
            'class'         => array('form-row-wide'),
            'id'            => 'delivery-date-picker',
            'required'      => true,
            'label'         => __('Delivery Date', 'tillit-payment-gateway'),
            'placeholder'   => __('Select Date', 'tillit-payment-gateway'),
            'options'       => $dateoptions
        ),
        $checkout->get_value('cylinder_collect_date')
    );

    echo '</div>';
}


/**
 * Validate if delivery date was sent after clicking Placing order
 *
 * @return void
 */
function checkout_validate_delivery_date() {

    if (!$_POST['delivery_date']) {
        // the required field delivery_date was not sent
        wc_add_notice('<strong>' . __('Delivery Date', 'tillit-payment-gateway') . '</strong> '
                      . __('is a required field.', 'tillit-payment-gateway'), 'error');
    } else if (!validate_date($_POST['delivery_date'])) {
        // delivery_date is of incorrect format
        wc_add_notice('<strong>' . __('Delivery Date', 'tillit-payment-gateway') . '</strong> '
                      . __('cannot be parsed.', 'tillit-payment-gateway'), 'error');
    }
}


/**
 * Add the delivery date to order meta
 *
 * @return void
 */
function add_delivery_date_to_order_meta($order_id) {
    if (!empty($_POST['delivery_date'])) {
        update_post_meta($order_id, 'delivery_date', sanitize_text_field($_POST['delivery_date']));
    }
}


/**
 * Validate if a string is of a specific date format
 *
 * @return bool
 */
function validate_date($date_str, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date_str);
    return $d && $d->format($format) === $date_str;
}
