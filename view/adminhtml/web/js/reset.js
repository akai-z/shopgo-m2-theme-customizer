require([
    'Magento_Ui/js/modal/confirm'
], function(confirm){
    function resetCustomizer(url, message) {
        confirm({
            content: message,
            actions: {
                confirm: function() {
                    setLocation(url)
                }
            }
        });
    }

    window.resetCustomizer = resetCustomizer;
});
