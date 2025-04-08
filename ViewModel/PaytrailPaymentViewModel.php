<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\ViewModel;

//use Goodahead\ABTesting\Model\ConfigProvider;
//use Goodahead\ABTesting\Model\CookieProvider;
use Paytrail\PaymentServiceHyvaCheckout\Model\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PaytrailPaymentViewModel implements ArgumentInterface
{
    /**
     * @param Config $config
     */
    public function __construct(
        protected Config $config,
//        protected ConfigProvider $abTestingConfig,
//        protected CookieProvider $cookieProvider
    ) {}

    /**
     * @return bool
     */
    public function canDisplayAdditionalInformation(): bool
    {
//        if (!$this->abTestingConfig->isCheckoutPaymentMethodsABEnabled()) {
            return $this->config->canShowAdditionalInformation();
//        }

//        if ($this->cookieProvider->get() == 1) {
//            return $this->config->canShowAdditionalInformation();
//        }

        return false;
    }
}
