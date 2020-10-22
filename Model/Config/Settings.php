<?php

namespace Magezil\OrderPostApi\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Settings
{
    const MODULE_ENABLE = 'magezil_order_post_api/general/enable';
    const ORDER_POST_API_KEY = 'magezil_order_post_api/general/api_key';
    const ORDER_POST_ENDPOINT = 'magezil_order_post_api/general/endpoint';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::MODULE_ENABLE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::ORDER_POST_API_KEY, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getEndpoint(): string
    {
        return $this->scopeConfig->getValue(self::ORDER_POST_ENDPOINT, ScopeInterface::SCOPE_WEBSITE);
    }
}
