

# Transmit Security Detection and Response Magento 2 Extension

## Overview

Detection and Response extension for Magento 2, the extension integrate a web SDK and trigger an API call that calls the recommendation API before very transcation.

https://developer.transmitsecurity.com/guides/risk/quick_start_web/

## Installation

Use Composer (see composer.json), follow these steps in the command line:
```
1. composer config repositories.drs-magento-extension git git@github.com:TransmitSecurity/drs-magento-extension.git
2. composer require drs/module-security-extension dev-main
3. php bin/magento module:enable TransmitSecurity_DrsSecurityExtension
4. php bin/magento setup:upgrade
5. php bin/magento setup:di:compile
```