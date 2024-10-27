<?php
/**
 * Plugin Name: WooCommerce Plan Customizer
 * Description: Custom plan configurator for WooCommerce with admin dashboard
 * Version: 1.0.0
 * Author: Sabbir Ahmed
 * Text Domain: wc-plan-customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Plan_Customizer_Plugin {
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('woocommerce_before_add_to_cart_button', array($this, 'display_customizer'));
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 3);
        add_filter('woocommerce_get_item_data', array($this, 'display_cart_item_data'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_order_item_meta'), 10, 4);
        add_action('woocommerce_before_calculate_totals', array($this, 'calculate_custom_price'));

        // Create custom table on plugin activation
        register_activation_hook(__FILE__, array($this, 'create_custom_table'));
    }

    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>WooCommerce Plan Customizer requires WooCommerce to be installed and active.</p></div>';
            });
            return;
        }
    }

    public function create_custom_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'plan_customizer_orders';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            validity int(11) NOT NULL,
            internet_data int(11) NOT NULL,
            currency varchar(10) NOT NULL,
            final_price decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function enqueue_scripts() {
        if (is_product()) {
            wp_enqueue_style(
                'wc-plan-customizer',
                plugins_url('assets/css/style.css', __FILE__),
                array(),
                '1.0.0'
            );

            wp_enqueue_script(
                'wc-plan-customizer',
                plugins_url('assets/js/customizer.js', __FILE__),
                array('jquery'),
                '1.0.0',
                true
            );

            wp_localize_script('wc-plan-customizer', 'wcPlanCustomizer', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wc-plan-customizer')
            ));
        }
    }

    public function display_customizer() {
        global $product;

        // Only show for simple products
        if (!$product->is_type('simple')) {
            return;
        }

        $base_price = $product->get_price();
        include plugin_dir_path(__FILE__) . 'templates/customizer.php';
    }

    public function add_cart_item_data($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['plan_validity']) && isset($_POST['plan_internet_data'])) {
            $cart_item_data['plan_customizer'] = array(
                'validity' => sanitize_text_field($_POST['plan_validity']),
                'internet_data' => sanitize_text_field($_POST['plan_internet_data']),
                'currency' => sanitize_text_field($_POST['plan_currency']),
                'custom_price' => floatval($_POST['plan_price'])
            );
        }
        return $cart_item_data;
    }

    public function display_cart_item_data($item_data, $cart_item) {
        if (isset($cart_item['plan_customizer'])) {
            $item_data[] = array(
                'key' => __('Validity', 'wc-plan-customizer'),
                'value' => $cart_item['plan_customizer']['validity'] . ' days'
            );
            $item_data[] = array(
                'key' => __('Internet Data', 'wc-plan-customizer'),
                'value' => $this->format_data_size($cart_item['plan_customizer']['internet_data'])
            );
        }
        return $item_data;
    }

    public function calculate_custom_price($cart) {
        foreach ($cart->get_cart() as $cart_item) {
            if (isset($cart_item['plan_customizer'])) {
                $cart_item['data']->set_price($cart_item['plan_customizer']['custom_price']);
            }
        }
    }

    public function add_order_item_meta($item, $cart_item_key, $values, $order) {
        if (isset($values['plan_customizer'])) {
            $item->add_meta_data('_plan_validity', $values['plan_customizer']['validity']);
            $item->add_meta_data('_plan_internet_data', $values['plan_customizer']['internet_data']);
            $item->add_meta_data('_plan_currency', $values['plan_customizer']['currency']);

            // Save to custom table
            $this->save_order_details($order->get_id(), $values['plan_customizer']);
        }
    }

    private function save_order_details($order_id, $plan_data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'plan_customizer_orders';

        $wpdb->insert(
            $table_name,
            array(
                'order_id' => $order_id,
                'user_id' => get_current_user_id(),
                'validity' => $plan_data['validity'],
                'internet_data' => $plan_data['internet_data'],
                'currency' => $plan_data['currency'],
                'final_price' => $plan_data['custom_price']
            ),
            array('%d', '%d', '%d', '%d', '%s', '%f')
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Plan Orders', 'wc-plan-customizer'),
            __('Plan Orders', 'wc-plan-customizer'),
            'manage_options',
            'plan-customizer-orders',
            array($this, 'render_admin_page'),
            'dashicons-cart',
            56
        );
    }

    public function render_admin_page() {
        include plugin_dir_path(__FILE__) . 'templates/admin-page.php';
    }

    private function format_data_size($gb) {
        return $gb >= 1000 ? number_format($gb / 1000, 1) . ' TB' : $gb . ' GB';
    }
}

new WC_Plan_Customizer_Plugin();