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

  var service = {
    /**
     * Queue of events to be tracked.
     */
    eventQueue: [],

    /**
     * Active handlers for all supported events.
     */
    eventHandlers: {},

    /**
     * Define the default handler for all supported events.
     */
    defaultEventHandlers: {
      'register': function () {
        Trialfire.do('track', 'Register', this.event.traits);
      },
      'login': function () {
        Trialfire.do('identify', this.event.userId, this.event.traits);
        Trialfire.do('track', 'Login', {
          userId: this.event.userId,
          email: this.event.traits.email
        });
      },
      'logout': function () {
        Trialfire.do('track', 'Logout', {});
        Trialfire.do('logout');
      },
      'billingAddress': function () {
        Trialfire.do('identify', this.event.userId, this.event.traits);
        Trialfire.do('track', 'Updated Default Billing Address', this.event.traits);
      },
      'viewCategory': function () {
        Trialfire.do('track', 'Viewed Category', this.event.props);
      },
      'viewProduct': function () {
        Trialfire.do('track', 'Viewed Product', this.event.props);
      },
      'addToCart': function () {
        Trialfire.do('track', 'Added to Cart', this.event.props);
      },
      'removeFromCart': function () {
        Trialfire.do('track', 'Removed from Cart', this.event.props);
      },
      'addToWishlist': function () {
        Trialfire.do('track', 'Add to Wishlist', this.event.props);
      },
      'newsletter': function () {
        Trialfire.do('track', 'Subscribed Newsletter', this.event.props);
      },
      'startCheckout': function () {
        Trialfire.do('track', 'Started Checkout', this.event.props);
      },
      'placeOrder': function () {
        Trialfire.do('identify', this.event.userId, this.event.traits);
        Trialfire.do('track', 'Completed Order', this.event.props);
      }
    }
  };

  // Initialize the service using the default event handlers.
  for (var eventName in service.defaultEventHandlers) {
    if (service.defaultEventHandlers.hasOwnProperty(eventName)) {
      service.eventHandlers[eventName] = service.defaultEventHandlers[eventName];
    }
  }

  /**
   * Call the default handler for an event.
   */
  service.callDefaultEventHandler = function callDefaultEventHandler (event) {
    var eventHandler = service.defaultEventHandlers[event.$name];
    if (typeof eventHandler === 'function') {
      return eventHandler.call({
        tfMage: service,
        event: event
      });
    } else {
      throw new Error('no default handler for ' + event.$name);
    }
  };

  /**
   * Call the handler for an event.
   */
  service.callEventHandler = function callEventHandler (event) {
    var eventHandler = service.eventHandlers[event.$name];
    if (typeof eventHandler === 'function') {
      return eventHandler.call({
        tfMage: service,
        event: event
      });
    } else {
      throw new Error('no handler for ' + event.$name);
    }
  };

  /**
   * Set the handler for an event.
   */
  service.setEventHandler = function setEventHandler (eventName, handler) {
    if (typeof handler === 'function') {
      service.eventHandlers[eventName] = handler;
    } else {
      throw new Error('handler must be a function');
    }
  };

  /**
   * Add an array of events to be tracked.
   */
  service.pushEvents = function pushEvents (events) {
    for (var i = 0; i < events.length; i++) {
      service.eventQueue.push(events[i]);
    }
  };

  /**
   * Invoke the handler for each event in the queue.
   */
  service.trackNow = function trackNow () {
    while (service.eventQueue.length) {
      try {
        service.callEventHandler(service.eventQueue.shift());
      } catch (error) {
        console.log('tfMage: error', event, error);
      }
    }
  };

  return service;
});