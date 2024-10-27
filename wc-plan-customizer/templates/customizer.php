<div id="plan-customizer" class="wc-plan-customizer">
    <div class="plan-customizer-header">
        <h3><?php esc_html_e('Customize Your Plan', 'wc-plan-customizer'); ?></h3>
    </div>

    <div class="plan-customizer-body">
        <div class="customizer-field">
            <label for="plan_currency"><?php esc_html_e('Currency', 'wc-plan-customizer'); ?></label>
            <select id="plan_currency" name="plan_currency">
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
            </select>
        </div>

        <div class="customizer-field">
            <label for="plan_validity"><?php esc_html_e('Validity (days)', 'wc-plan-customizer'); ?></label>
            <div class="input-group">
                <input type="number" id="plan_validity" name="plan_validity" value="30" min="1" max="365">
                <span class="unit">days</span>
            </div>
            <input type="range" id="validity_slider" min="1" max="365" value="30">
            <div class="slider-labels">
                <span>1 day</span>
                <span>30 days</span>
                <span>365 days</span>
            </div>
        </div>

        <div class="customizer-field">
            <label for="plan_internet_data"><?php esc_html_e('Internet Data (GB)', 'wc-plan-customizer'); ?></label>
            <div class="input-group">
                <input type="number" id="plan_internet_data" name="plan_internet_data" value="100" min="1" max="100000">
                <span class="unit">GB</span>
            </div>
            <input type="range" id="data_slider" min="1" max="100000" value="100">
            <div class="slider-labels">
                <span>1 GB</span>
                <span>100 GB</span>
                <span>100 TB</span>
            </div>
        </div>

        <div class="price-display">
            <span class="currency-symbol">$</span>
            <span id="final_price"><?php echo esc_html($base_price); ?></span>
        </div>

        <input type="hidden" name="plan_price" id="plan_price" value="<?php echo esc_attr($base_price); ?>">
    </div>
</div>