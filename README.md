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
php composer.phar require trialfireinc/tracker:v1.1.0
```

2. Enable the module.
```
bin/magento module:enable Trialfire_Tracker --clear-static-content
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## Configure

The only required configuration is the API key from your project in Trialfire. You can find the API key by logging in to your Trialfire account and navigating to _Settings > Tracking Code and API key_.

1. Open the Admin page for your store and navigate to _Stores > Configuration_, then click on the __Trialfire__ tab. 

2. Set the __Enable Tracking__ field to __Yes__, and then input your API key into the __API token__ field.

3. Save the configuration.

4. Flush the __Configuration__, __Block HTML__, and __Full_Page__ caches. You can clear the caches from _System > Cache Management_ or use the command `bin/magento cache:flush config block_html full_page`.

### Advanced Configuration

Complete documentation is available [here](https://docs.trialfire.com/#/magento2-tracker).

#### Custom Initialization

Customize how Trialfire tracking code is included into the DOM. 
The default implementation is similar to the following:
```js
var s = document.createElement('script');
s.src = this.assetUrl;
document.head.appendChild(s);
Trialfire.init(this.apiToken);
```

The value of `this` for the custom initializer is the following.
```js
{
  // The value of the Asset URL setting in your store's Trialfire configuration.
  assetUrl: '...',

  // The value of the API Token setting in your store's Trialfire configuration.
  apiToken: '...',                  

  /**
   * Call the default initialization logic.
   */
  callDefaultInit: function () ...  

  /**
   * Inject the Trialfire script into the DOM but do not call Trialfire.init().
   */
  injectScript: function () ...
}
```

To prevent Trialfire from loading on the 'agent' subdomain, input the following JavaScript into the __Initialization Code__ textarea.
```js
// If host does not start with 'agent'.
if (!location.host.startsWith('agent.myshop.com')) {
  // Invoke the default initialization logic.
  this.callDefaulInit();
}
```

#### Custom Event Handlers

Customize how Trialfire fires tracking events. 
The value of `this` for the custom event handler is the following.

`this.event`: 
An object containing information about the event. 
The contents vary with the event type but are similar to the following.
```js
this.event = {
  $name: 'placeOrder',
  apiToken: '...',
  userId: 1,
  traits: {
    email: '...',
    firstName: '...'
  },
  props: {
    orderId: '...',
    products: [{
      id: '...',
      name: '...',
      sku: '...'
    },{
      ...
    }]
  }
}
```
`this.tfMage`: 
The service which tracks the events. 
It contains utility methods that you may use in your custom handler.
```js
this.tfMage = {
  /**
   * Calls the default handler for an event.
   */
  callDefaultEventHandler: function (event) ...
};
```

__Example:__

To prevent Trialfire from tracking orders on the 'agent' subdomain, input the following JavaScript into the __Place Order__ textarea.
```js
// If host does not start with 'agent'.
if (!location.host.startsWith('agent.myshop.com')) {
  // Invoke the default handler for this event.
  this.tfMage.callDefaultEventHandler(this.event);
}
```