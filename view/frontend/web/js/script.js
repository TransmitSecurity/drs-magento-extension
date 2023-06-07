require(['jquery', 'Magento_Customer/js/customer-data'], async function($, customerData) {

    async function getTSSDKClientId() {
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
        const clientId = await getTSSDKClientId();
        await window.tsPlatform.initialize({ clientId });
        let customerId = customerData.get('customer')().id;

        if (customerId) {
            window.tsPlatform.drs.setAuthenticatedUser(customerId);
        }
    }

    //Init DRS SDK
    const script = document.createElement('script');
    script.defer = true;
    script.src = "https://platform-websdk.transmitsecurity.io/platform-websdk/latest/ts-platform-websdk.js";
    script.id = "ts-platform-script";
    document.head.appendChild(script);
    script.addEventListener("load", handleTSSDK);
});
