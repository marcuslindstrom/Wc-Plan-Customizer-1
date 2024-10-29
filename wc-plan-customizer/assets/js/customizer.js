// Initialize the Plan Customizer React app
document.addEventListener('DOMContentLoaded', function() {
    const customizer = document.getElementById('plan-customizer-root');
    if (!customizer) return;

    // Create a container for our React app
    const appContainer = document.createElement('div');
    appContainer.id = 'plan-customizer-app';
    customizer.appendChild(appContainer);

    // Initialize the React app
    const script = document.createElement('script');
    script.src = planCustomizerData.pluginUrl + '/assets/index-98529e29.js';
    script.type = 'module';
    document.body.appendChild(script);

    // Add the styles
    const styles = document.createElement('link');
    styles.rel = 'stylesheet';
    styles.href = planCustomizerData.pluginUrl + '/assets/index-9da27e87.css';
    document.head.appendChild(styles);

    // Handle form submission
    const form = document.querySelector('form.cart');
    if (form) {
        form.addEventListener('submit', function(e) {
            const customizationData = window.planCustomizerState || {};
            
            // Add customization data to the form
            const dataInput = document.createElement('input');
            dataInput.type = 'hidden';
            dataInput.name = 'plan_customizer_data';
            dataInput.value = JSON.stringify(customizationData);
            form.appendChild(dataInput);
        });
    }
});
