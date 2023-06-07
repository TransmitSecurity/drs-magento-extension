require(['jquery', 'Magento_Customer/js/customer-data'], function($, customerData) {
    let clientSec;
    let customerId;
    let clientId = clientSec = customerId = null;
    $.ajax({
        url: '/security/index/index',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            clientId = response.client_id;
            clientSec = response.client_secret;
            customerId = response.customer_id;
            sessionStorage.setItem('customer_id', customerId);
        },
        error: function() {
            console.error('Failed to retrieve client_id');
        }
    });
    customerId = sessionStorage.getItem('customer_id');
    if(customerId !== undefined && customerId !== null){
        window.tsPlatform.drs.setAuthenticatedUser(customerId);
        window.tsPlatform.drs.triggerActionEvent("transaction").then((actionResponse) => {
            let actionToken = actionResponse.actionToken;
            $.ajax({
                url: 'https://api.transmitsecurity.io/oidc/'+ actionToken,
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                data: {
                    grant_type: 'client_credentials',
                    client_id: clientId,
                    client_secret: clientSec,
                    resource: 'https://risk.identity.security'
                },
                success: function(response) {
                    var access_token = response.access_token;
                    // Your custom JavaScript logic using the access_token
                    // ...
                },
                error: function() {
                    console.error('Failed to fetch access token');
                }
            });

            const query = new URLSearchParams({
                action_token: actionToken
            }).toString();

            $.ajax({
                url: `https://api.transmitsecurity.io/risk/v1/recommendation?${query}`,
                method: 'GET',
                headers: {
                    Authorization: 'Bearer '+ actionToken
                },
                success: function(response) {
                    var isHighRisk = response.is_high_risk; // Assuming the API response includes a field indicating high risk
                    if (isHighRisk) {
                        // Perform actions to deny the transaction
                        console.log('Transaction denied due to high risk');
                        // You can redirect the user to an error page or show an error message
                    } else {
                        // Proceed with the transaction
                        console.log('Transaction approved');
                    }
                },
                error: function() {
                    console.error('Failed to fetch recommendation');
                }
            });
        });
    }
});
