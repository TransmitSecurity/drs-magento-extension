var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'TransmitSecurity_DrsSecurityExtension/js/place-order': true
            }
        }
    },
    deps: [
        "js/sdk_init"
    ]
};