<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\ViewModel;

use Paytrail\PaymentServiceHyvaCheckout\Model\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PaytrailPaymentViewModel implements ArgumentInterface
{
    /**
     * @param Config $config
     */
    public function __construct(
        protected Config $config,
    ) {}

    /**
     * @return bool
     */
    public function canDisplayAdditionalInformation(): bool
    {
            return $this->config->canShowAdditionalInformation();
    }
}
