<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Plan_Customizer extends WC_Product {
    
    public function __construct($product) {
        $this->product_type = 'plan_customizer';
        parent::__construct($product);
    }

    public function get_type() {
        return 'plan_customizer';
    }

    // Support add-to-cart form and price display
    public function is_purchasable() {
        return true;
    }

    public function is_virtual() {
        return true;
    }

    public function is_sold_individually() {
        return true;
    }
}
