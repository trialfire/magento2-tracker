<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 *
 * Renders the tracker events list into a section for a private content knockout.
 */
namespace Trialfire\Tracker\CustomerData;

class Events implements \Magento\Customer\CustomerData\SectionSourceInterface
{

    protected $tfSessionFactory;

    public function __construct(
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory
    ) {
        $this->tfSessionFactory = $tfSessionFactory;
    }

    /**
     * Return the events list and then clear it.
     */
    public function getSectionData()
    {
        return [
            
            'events' => $this->tfSessionFactory->create()->getEvents(true)
        ];
    }
}
