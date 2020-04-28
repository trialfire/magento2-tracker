<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 */
namespace Trialfire\Tracker\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

  const CONFIG_ACTIVE = 'trialfire_tracker/general/active';
  const CONFIG_API_TOKEN = 'trialfire_tracker/general/api_token';
  const CONFIG_ASSET_URL = 'trialfire_tracker/general/asset_url';

  public $logger;
  protected $scopeConfig;
  protected $jsonEncoder;

  public function __construct(
    \Psr\Log\LoggerInterface $logger,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Framework\Json\EncoderInterface $jsonEncoder
  ) {
    $this->logger = $logger;
    $this->scopeConfig = $scopeConfig;
    $this->jsonEncoder = $jsonEncoder;
  }

  // Settings

  /**
   * Get the Trialfire API token from the module settings.
   */
  public function getApiToken($scope = null) {
    return $this->scopeConfig->getValue(
      self::CONFIG_API_TOKEN,
      \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
      $scope
    );
  }

  /**
   * Get the Trialfire asset URL from the module settings.
   */
  public function getAssetUrl($scope = null) {
    return $this->scopeConfig->getValue(
      self::CONFIG_ASSET_URL,
      \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
      $scope
    );
  }

  /**
   * Stringify an object as JSON.
   */
  public function jsonStringify($data) {
    return $this->jsonEncoder->encode($data);
  }

}
