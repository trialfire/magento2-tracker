/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Event tracking logic for events captured from the Magento backend.
 */
define(function () {
  'use strict';

  var eventQueue = [];

  /**
   * Add an array of events to be tracked.
   */
  function pushEvents (events) {
    for (var i = 0; i < events.length; i++) {
      eventQueue.push(events[i]);
    }
  }

  /**
   * Emit track calls for each event in the queue.
   */
  function trackNow () {
    while (eventQueue.length) {
      try {
        var event = eventQueue.shift();
        switch (event.$name) {
          case 'register':
            console.log('register', event);
            break;
          case 'login':
            console.log('login', event);
            break;
          case 'logout':
            console.log('logout', event);
            break;
          case 'billingAddress':
            console.log('billingAddress', event);
            break;
          case 'addToCart':
            console.log('addToCart', event);
            break;
          case 'removeFromCart':
            console.log('removeFromCart', event);
            break;
          case 'addToWishlist':
            console.log('addToWishlist', event);
            break;
          case 'startCheckout':
            console.log('startCheckout', event);
            break;
          case 'placeOrder':
            console.log('palceOrder', event);
            break;
          case 'viewCategory':
            console.log('viewCategory', event);
            break;
          case 'viewProduct':
            console.log('viewProduct', event);
            break;
          case 'newsletter':
            console.log('newsletter', event);
            break;
          default:
            // Track a generic event.
            console.log('Unknown event:', event);
            break;
        }
      } catch (error) {
        console.log('failed to track', event, error);
      }
    }
  }

  return {
    pushEvents: pushEvents,
    trackNow: trackNow
  };
});