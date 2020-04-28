<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Renders the tracker events list into a block for pages that are not cacheable.
 */
namespace Trialfire\Tracker\Block;

class Events extends \Magento\Framework\View\Element\Template {

    protected $tfSessionFactory;
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }

    /**
     * Return the events list as JSON and then clear it.
     */
    public function getEvents() {
        $events = $this->tfSessionFactory->create()->getEvents(true);
        return $this->helper->jsonStringify($events);
    }

}
