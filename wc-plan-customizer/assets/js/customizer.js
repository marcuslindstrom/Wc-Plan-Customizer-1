jQuery(function($) {
    const basePrice = parseFloat($('#plan_price').val());
    const validityMultiplier = 2;
    const internetMultiplier = 0.5;

    function updatePrice() {
        const validity = parseInt($('#plan_validity').val());
        const internetData = parseInt($('#plan_internet_data').val());
        const currency = $('#plan_currency').val();

        const newPrice = basePrice +
            (validity - 30) * validityMultiplier +
            (internetData - 100) * internetMultiplier;

        const symbol = getCurrencySymbol(currency);
        $('.currency-symbol').text(symbol);
        $('#final_price').text(newPrice.toFixed(2));
        $('#plan_price').val(newPrice.toFixed(2));
    }

    function getCurrencySymbol(currency) {
        switch(currency) {
            case 'EUR': return '€';
            case 'GBP': return '£';
            default: return '$';
        }
    }

    // Validity controls
    $('#validity_slider, #plan_validity').on('input', function() {
        const value = $(this).val();
        $('#validity_slider, #plan_validity').val(value);
        updatePrice();
    });

    // Internet data controls
    $('#data_slider, #plan_internet_data').on('input', function() {
        const value = $(this).val();
        $('#data_slider, #plan_internet_data').val(value);
        updatePrice();
    });

    // Currency change
    $('#plan_currency').on('change', updatePrice);

    // Initialize
    updatePrice();
});