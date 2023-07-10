console.log("Loading DRS Script");
require(['jquery', 'Magento_Customer/js/customer-data'], function($, customerData) {
    console.log("Starting DRS Script");

    async function getTSSDKClientId() {
        console.log("getTSSDKClientId");
        let clientId = sessionStorage.getItem('clientId');
        if (clientId) {
            return clientId;
        }
        let response = await $.ajax({
            url: '/security/index/index',
            method: 'GET',
            dataType: 'json'
        });
        sessionStorage.setItem('clientId', response.client_id);
        return response.client_id;
    }

    async function handleTSSDK() {
        console.log("handleTSSDK");
        const clientId = await getTSSDKClientId();
        let customer = customerData.get("customer")();
        let customerId = null;
        if (customer) {
            customerId = customer.id;
        }
        console.log("handleTSSDK setAuthenticatedUser", customerId);
        await window.tsPlatform.initialize({ clientId, drs: { userId } });
    }

    //Init DRS SDK
    const script = document.createElement('script');
    script.defer = true;
    script.src = "https://platform-websdk.transmitsecurity.io/platform-websdk/latest/ts-platform-websdk.js";
    script.id = "ts-platform-script";
    document.head.appendChild(script);
    script.addEventListener("load", handleTSSDK);
});
