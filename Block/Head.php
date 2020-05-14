<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 *
 * Outputs a tracking script include at the top of every page.
 */
namespace Trialfire\Tracker\Block;

class Head extends \Magento\Framework\View\Element\Template
{

    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Trialfire\Tracker\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * Get the Trialfire API token from the module settings.
     */
    public function getApiToken()
    {
        return $this->helper->getApiToken();
    }

    /**
     * Get the Trialfire asset URL from the module settings.
     */
    public function getAssetUrl()
    {
        return $this->helper->getAssetUrl();
    }
}
