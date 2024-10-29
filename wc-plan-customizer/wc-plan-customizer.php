<?php
/**
 * Plugin Name: WC Plan Customizer
 * Description: A WooCommerce plugin for customizing subscription plans
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: wc-plan-customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-product-type.php';

class WC_Plan_Customizer_Plugin {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        
        // Product type hooks
        add_filter('product_type_selector', array($this, 'add_product_type'));
        add_filter('woocommerce_product_class', array($this, 'product_class'), 10, 2);
        
        // Display customizer on product page
        add_action('woocommerce_before_add_to_cart_button', array($this, 'display_plan_customizer'));
        
        // Handle cart item data
        add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 10, 3);
        add_filter('woocommerce_get_item_data', array($this, 'display_cart_item_data'), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_order_item_meta'), 10, 4);
    }

    public function add_product_type($types) {
        $types['plan_customizer'] = __('Plan Customizer', 'wc-plan-customizer');
        return $types;
    }

    public function product_class($classname, $product_type) {
        if ($product_type === 'plan_customizer') {
            $classname = 'WC_Product_Plan_Customizer';
        }
        return $classname;
    }

    public function display_plan_customizer() {
        global $product;
        
        if ($product && $product->get_type() === 'plan_customizer') {
            // Load the React app container
            echo '<div id="plan-customizer-root"></div>';
            
            // Enqueue the React app
            wp_enqueue_script(
                'plan-customizer-app',
                plugins_url('assets/js/customizer.js', __FILE__),
                array(),
                '1.0.0',
                true
            );

            // Pass necessary data to JavaScript
            wp_localize_script('plan-customizer-app', 'planCustomizerData', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('plan_customizer_nonce'),
                'productId' => $product->get_id(),
                'pluginUrl' => plugins_url('', __FILE__)
            ));
        }
    }

    public function add_cart_item_data($cart_item_data, $product_id, $variation_id) {
        if (isset($_POST['plan_customizer_data'])) {
            $cart_item_data['plan_customizer_data'] = json_decode(stripslashes($_POST['plan_customizer_data']), true);
        }
        return $cart_item_data;
    }

    public function display_cart_item_data($item_data, $cart_item) {
        if (isset($cart_item['plan_customizer_data'])) {
            foreach ($cart_item['plan_customizer_data'] as $key => $value) {
                $item_data[] = array(
                    'key' => ucfirst(str_replace('_', ' ', $key)),
                    'value' => $value
                );
            }
        }
        return $item_data;
    }

    public function add_order_item_meta($item, $cart_item_key, $values, $order) {
        if (isset($values['plan_customizer_data'])) {
            foreach ($values['plan_customizer_data'] as $key => $value) {
                $item->add_meta_data(ucfirst(str_replace('_', ' ', $key)), $value);
            }
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Plan Customizer', 'wc-plan-customizer'),
            __('Plan Customizer', 'wc-plan-customizer'),
            'manage_options',
            'wc-plan-customizer',
            array($this, 'admin_page_content'),
            'dashicons-admin-generic',
            56
        );
    }

    public function admin_page_content() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="plan-customizer-admin-content">
                <p><?php _e('Welcome to WC Plan Customizer settings.', 'wc-plan-customizer'); ?></p>
                <p><?php _e('To use the plan customizer:', 'wc-plan-customizer'); ?></p>
                <ol>
                    <li><?php _e('Create a new product', 'wc-plan-customizer'); ?></li>
                    <li><?php _e('Set the product type to "Plan Customizer"', 'wc-plan-customizer'); ?></li>
                    <li><?php _e('Set a base price for the plan', 'wc-plan-customizer'); ?></li>
                    <li><?php _e('Publish the product', 'wc-plan-customizer'); ?></li>
                </ol>
            </div>
        </div>
        <?php
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_wc-plan-customizer' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-admin',
            plugins_url('assets/css/admin.css', __FILE__),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'wc-plan-customizer-admin',
            plugins_url('assets/js/admin.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
    }

    public function enqueue_frontend_scripts() {
        if (!is_product()) {
            return;
        }

        wp_enqueue_style(
            'wc-plan-customizer-frontend',
            plugins_url('assets/css/frontend.css', __FILE__),
            array(),
            '1.0.0'
        );
    }

    public function activate() {
        // Flush rewrite rules to ensure our custom product type works
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
function wc_plan_customizer_init() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            ?>
            <div class="error">
                <p><?php _e('WC Plan Customizer requires WooCommerce to be installed and activated.', 'wc-plan-customizer'); ?></p>
            </div>
            <?php
        });
        return;
    }

    $plugin = WC_Plan_Customizer_Plugin::get_instance();
}

add_action('plugins_loaded', 'wc_plan_customizer_init');

// Register activation and deactivation hooks
register_activation_hook(__FILE__, array('WC_Plan_Customizer_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('WC_Plan_Customizer_Plugin', 'deactivate'));
