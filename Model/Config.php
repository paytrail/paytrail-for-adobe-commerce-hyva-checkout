<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    public const XML_PATH_PAYMENT_PAYTRAIL_SHOW_ADDITIONAL_INFORMATION = 'payment/paytrail/show_additional_information';

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected StoreManagerInterface $storeManager
    ) {
    }

    public function canShowAdditionalInformation(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_PAYTRAIL_SHOW_ADDITIONAL_INFORMATION,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
