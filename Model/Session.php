<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 *
 * Maintain the tracker events list in the session.
 */
namespace Trialfire\Tracker\Model;

class Session extends \Magento\Framework\Session\SessionManager {
    
    // Events that are captured from the backend (typically via observers) are queued in this event list. The list
    // is retrieved and rendered by a block for non-cacheable pages, or by private content knockout for cached pages.

    const KEY_EVENTS = 'tf_t_events';
    
    /**
     * Clear the list of events to track.
     */
    public function clearEvents() {
        return $this->setData(self::KEY_EVENTS, []);
    }

    /**
     * Get the list of events to track.
     * 
     * @param bool $clear Clear the events list after returning it.
     */
    public function getEvents($clear) {
        $events = $this->getData(self::KEY_EVENTS);
        $events = is_array($events) ? $events : [];
        if ($clear) {
            $this->clearEvents();
        }
        return $events;
    }

    /**
     * Add an event to the events list.
     */
    public function pushEvent($event) {
        $events = $this->getEvents(false);
        array_push($events, $event);
        $this->setData(self::KEY_EVENTS, $events);
    }

    // The "recently tracked order IDs list" is used to prevent additional order events from being emitted whenever
    // the checkout success pages are reloaded.

    const KEY_RECENT_ORDER_IDS = 'tf_t_recent_order_ids';

    /**
     * Get the list of recently tracked order IDs.
     */
    public function getRecentlyTrackedOrderIds() {
        $recentIds = $this->getData(self::KEY_RECENT_ORDER_IDS);
        $recentIds = is_array($recentIds) ? $recentIds : [];
        return $recentIds;
    }

    /**
     * Add an order ID to the recently tracked order IDs list.
     */
    public function pushRecentlyTrackedOrderId($orderId) {
        $recentIds = $this->getRecentlyTrackedOrderIds();
        
        // Remember up to 10 unique order IDs.
        array_unshift($recentIds, $orderId);
        $recentIds = array_unique($recentIds);
        $recentIds = array_slice($recentIds, 0, 10);

        $this->setData(self::KEY_RECENT_ORDER_IDS, $recentIds);
    }

    /**
     * True if the order ID was recently tracked, otherwise false.
     */
    public function isRecentlyTrackedOrderId($orderId) {
        return in_array($orderId, $this->getRecentlyTrackedOrderIds());
    }
        
}
    