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
    public function pushEvent($data) {
        $events = $this->getEvents(false);
        array_push($events, $data);
        $this->setData(self::KEY_EVENTS, $events);
    }
        
}
    