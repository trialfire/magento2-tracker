/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Install a handler to receive data for a private content knockout.
 * Send events captured from the Magento backend to the tfMage module.
 */
define([
  'ko',
  'uiComponent',
  'Magento_Customer/js/customer-data',
  'Trialfire_Tracker/js/tfMage'
], function (ko, Component, customerData, tfMage) {
  'use strict';
  return Component.extend({
    initialize: function () {
      customerData.get('trialfire-tracker-events').subscribe(function (loadedData) {
        if (loadedData && loadedData.events) {
          tfMage.pushEvents(loadedData.events);
          tfMage.trackNow();
        }
      });
    }
  });
});
