require(['jquery',
    'Magento_Customer/js/customer-data'
], function($, customerData) {
    let customerId = customerData.get('customer')().id;
    if (customerId === undefined && sessionStorage.getItem('customer_id') !== null ){
        customerId = sessionStorage.getItem('customer_id');
    }
    if(customerId !== undefined && customerId !== null){
        window.tsPlatform.drs.setAuthenticatedUser(customerId);
    }
});
