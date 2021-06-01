<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 */
namespace Trialfire\Tracker\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const CONFIG_ACTIVE = 'trialfire_tracker/general/active';
    const CONFIG_API_TOKEN = 'trialfire_tracker/general/api_token';
    const CONFIG_ASSET_URL = 'trialfire_tracker/general/asset_url';
    const CONFIG_CUSTOM_INIT = 'trialfire_tracker/advanced/custom_init';
    const CONFIG_HANDLER_REGISTER = 'trialfire_tracker/handlers/handler_register';
    const CONFIG_HANDLER_LOGIN = 'trialfire_tracker/handlers/handler_login';
    const CONFIG_HANDLER_LOGOUT = 'trialfire_tracker/handlers/handler_logout';
    const CONFIG_HANDLER_BILLING_ADDRESS = 'trialfire_tracker/handlers/handler_billing_address';
    const CONFIG_HANDLER_VIEW_CATEGORY = 'trialfire_tracker/handlers/handler_view_category';
    const CONFIG_HANDLER_VIEW_PRODUCT = 'trialfire_tracker/handlers/handler_view_product';
    const CONFIG_HANDLER_ADD_TO_CART = 'trialfire_tracker/handlers/handler_add_to_cart';
    const CONFIG_HANDLER_REMOVE_FROM_CART = 'trialfire_tracker/handlers/handler_remove_from_cart';
    const CONFIG_HANDLER_ADD_TO_WISHLIST = 'trialfire_tracker/handlers/handler_add_to_wishlist';
    const CONFIG_HANDLER_NEWSLETTER = 'trialfire_tracker/handlers/handler_newsletter';
    const CONFIG_HANDLER_START_CHECKOUT = 'trialfire_tracker/handlers/handler_start_checkout';
    const CONFIG_HANDLER_PLACE_ORDER = 'trialfire_tracker/handlers/handler_place_order';

    public $logger;
    protected $storeManager;
    protected $scopeConfig;
    protected $jsonEncoder;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->jsonEncoder = $jsonEncoder;
    }

    // Settings

    /**
     * Get the Trialfire API token from the module settings.
     */
    public function getApiToken($scope = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_API_TOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * Get the Trialfire asset URL from the module settings.
     */
    public function getAssetUrl($scope = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_ASSET_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        );
    }

    /**
     * Get the Trialfire initialization code if there is custom JavaScript in the module settings..
     */
    public function getCustomJSInit($scope = null)
    {
        $jsCode = trim($this->scopeConfig->getValue(
            self::CONFIG_CUSTOM_INIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        ));

        return empty($jsCode) ? null : $jsCode;
    }

    /**
     * Add a mapping for an event handler if there is custom JavaScript in the module settings.
     */
    public function addHandlerIfNotEmpty(&$handlers, $eventName, $configKey, $scope)
    {
        $jsCode = trim($this->scopeConfig->getValue(
            $configKey,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scope
        ));
        if (!empty($jsCode)) {
            // Wrap the custom handler code in a JavaScript function.
            $handlers[$eventName] = "function () {" . $jsCode . "}";
        }
    }

    /**
     * Get a map of event names to custom event handlers from the module settings.
     */
    public function getCustomJSEventHandlers($scope = null)
    {
        $handlers = [];
        $this->addHandlerIfNotEmpty($handlers, 'register', self::CONFIG_HANDLER_REGISTER, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'login', self::CONFIG_HANDLER_LOGIN, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'logout', self::CONFIG_HANDLER_LOGOUT, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'billingAddress', self::CONFIG_HANDLER_BILLING_ADDRESS, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'viewCategory', self::CONFIG_HANDLER_VIEW_CATEGORY, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'viewProduct', self::CONFIG_HANDLER_VIEW_PRODUCT, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'addToCart', self::CONFIG_HANDLER_ADD_TO_CART, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'removeFromCart', self::CONFIG_HANDLER_REMOVE_FROM_CART, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'addToWishlist', self::CONFIG_HANDLER_ADD_TO_WISHLIST, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'newsletter', self::CONFIG_HANDLER_NEWSLETTER, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'startCheckout', self::CONFIG_HANDLER_START_CHECKOUT, $scope);
        $this->addHandlerIfNotEmpty($handlers, 'placeOrder', self::CONFIG_HANDLER_PLACE_ORDER, $scope);
        return $handlers;
    }

    /**
     * Get the currency code for the store.
     */
    public function getCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * Format an address as an array.
     */
    public function formatAddress($address)
    {
        return [
            'company' => $address->getCompany(),
            'country' => $address->getCountryId(),
            'city' => $address->getCity(),
            'region' => $address->getRegion(),
            'address' => join(', ', $address->getStreet()),
            'postal' => $address->getPostcode(),
            'phone' => $address->getTelephone()
        ];
    }

    /**
     * Stringify an object as JSON.
     */
    public function jsonStringify($data)
    {
        return $this->jsonEncoder->encode($data);
    }
}
