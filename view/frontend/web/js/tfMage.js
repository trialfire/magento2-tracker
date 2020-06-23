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
            Trialfire.do('track', 'Register', event.props);
            break;
          case 'login':
            Trialfire.do('identify', event.userId, event.props);
            Trialfire.do('track', 'Login', {
              userId: event.userId,
              email: event.props.email
            });
            break;
          case 'logout':
            Trialfire.do('track', 'Logout', {});
            Trialfire.do('logout');
            break;
          case 'billingAddress':
            Trialfire.do('identify', event.userId, event.props);
            Trialfire.do('track', 'Updated Default Billing Address', event.props);
            break;
          case 'addToCart':
            Trialfire.do('track', 'Added to Cart', event.props);
            break;
          case 'removeFromCart':
            Trialfire.do('track', 'Removed from Cart', event.props);
            break;
          case 'addToWishlist':
            Trialfire.do('track', 'Add to Wishlist', event.props);
            break;
          case 'startCheckout':
            Trialfire.do('track', 'Started Checkout', event.props);
            break;
          case 'orderIdentify':
            Trialfire.do('identify', event.userId, event.props);
            break;
          case 'placeOrder':
            Trialfire.do('track', 'Completed Order', event.props);
            break;
          case 'viewCategory':
            Trialfire.do('track', 'Viewed Category', event.props);
            break;
          case 'viewProduct':
            Trialfire.do('track', 'Viewed Product', event.props);
            break;
          case 'newsletter':
            Trialfire.do('track', 'Subscribed Newsletter', event.props);
            break;
          default:
            console.log('tfMage: unknown event:', event);
            break;
        }
      } catch (error) {
        console.log('tfMage: error', event, error);
      }
    }
  }

  return {
    pushEvents: pushEvents,
    trackNow: trackNow
  };
});