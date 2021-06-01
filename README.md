# Trialfire/Tracker

This module adds support for Trialfire to Magento 2. In addition to normal behavioural tracking, the following events are also collected:

* Viewed Category
* Viewed Product
* Add to Cart
* Add to Whishlist
* Remove from Cart
* Started Checkout
* Completed Order
* Subscribed Newsletter
* Register an Account

## Install

### Install from Magento Marketplace

Coming soon.

### Install from Command Line

1. Require the trialfireinc/tracker package. A list of version tags is available [here](https://github.com/trialfire/magento2-tracker/releases).
```
php composer.phar require trialfireinc/tracker:v1.0.0
```

2. Enable the module.
```
bin/magento module:enable Trialfire_Tracker --clear-static-content
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
```

## Configure

The only required configuration is the API key from your project in Trialfire. You can find the API key by logging in to your Trialfire account and navigating to _Settings > Tracking Code and API key_.

1. Open the Admin page for your store and navigate to _Stores > Configuration_, then click on the __Trialfire__ tab. 

2. Set the __Enable Tracking__ field to __Yes__, and then input your API key into the __API token__ field.

3. Save the configuration.

4. Flush the __Configuration__, __Block HTML__, and __Full_Page__ caches. You can clear the caches from _System > Cache Management_ or use the command `bin/magento cache:flush config block_html full_page`.
