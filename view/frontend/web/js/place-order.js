define(['mage/utils/wrapper'], function (wrapper) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, async function (originalAction, paymentData, redirectOnSuccess) {
            const actionResponse = await window.tsPlatform.drs.triggerActionEvent("checkout");
            console.log(actionResponse);
            paymentData['additional_data'] = { action_token: actionResponse.actionToken };
            return originalAction(paymentData, redirectOnSuccess);
        });
    };
});