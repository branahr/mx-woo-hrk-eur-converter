<?php
/*
Plugin Name: MX WooCommerce HRK 2 EUR converter
Plugin URI: https://media-x.hr
Description: Alat za promjenu cijene iz HRK u EUR + promjenu glavne valute u WooCommerceu
Version: 1.2
Author: Media X
WC tested up to: 7.2
Author URI: https://media-x.hr
*/

if ( !defined('ABSPATH') ) { 
    die;
}

add_action( 'manage_posts_extra_tablenav', 'mx_convert_hrk_to_eur_button', 20, 1 );
function mx_convert_hrk_to_eur_button( $which ) {
    global $pagenow, $typenow;

    if ( 'product' === $typenow && 'edit.php' === $pagenow && 'top' === $which ) {
        ?>
        <div class="alignleft actions hrk2eur">
            <button type="submit" name="hrk2eur_converter" style="height:32px;color:red;font-weight:bold;" class="button" value="yes" onclick="return confirm('OPREZ! Jeste li sigurni da želite pokrenuti alat za promjenu cijena svih proizvoda? Akciju nije moguće poništiti te će vam za povratak na staro stanje trebat backup baze podataka.')"><?php
                echo __( 'Promijeni cijene iz Kn u € ', 'woocommerce' ); ?></button>
        </div>
        <?php
    }
}

add_action( 'restrict_manage_posts', 'mx_trigger_hrk2eur_conversion' );
function mx_trigger_hrk2eur_conversion() {
    global $pagenow, $typenow;

    if ( 'product' === $typenow && 'edit.php' === $pagenow && isset($_GET['hrk2eur_converter']) && $_GET['hrk2eur_converter'] === 'yes' ) {
        
        mx_convert_hrk_to_eur_tool();

    }
}

function mx_convert_hrk_to_eur_tool() {
    global $wpdb;

    $current_currency = get_woocommerce_currency();

    if (!isset($current_currency) || $current_currency == 'EUR'){
        return;
    }

		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_regular_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_sale_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_regular_price_tmp' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_sale_price_tmp' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_price_tmp' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_min_variation_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_max_variation_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_min_variation_regular_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_max_variation_regular_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_min_variation_sale_price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_max_variation_sale_price' AND meta_value != ''");
		//Najniža cijena u zadnjih 30 dana
		$wpdb->query("UPDATE $wpdb->price_history SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = 'price' AND meta_value != ''");
		$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = ROUND(meta_value/7.53450, 5)  WHERE meta_key = '_lowest_price_30_days' AND meta_value != ''");
		//Pobriši transiente + promijeni glavnu valutu
		$wpdb->query("DELETE FROM $wpdb->options WHERE (option_name LIKE '_transient_wc_var_prices_%' OR option_name LIKE '_transient_timeout_wc_var_prices_%')");
		$wpdb->query("UPDATE $wpdb->options SET option_value = 'EUR' WHERE option_name = 'woocommerce_currency'");
}