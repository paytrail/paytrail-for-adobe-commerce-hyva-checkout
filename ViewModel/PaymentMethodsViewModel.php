<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\ViewModel;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Vault\Api\PaymentTokenManagementInterface;
use Magento\Vault\Block\Customer\PaymentTokens;

class PaymentMethodsViewModel implements ArgumentInterface
{
    /**
     * @param PaymentTokenManagementInterface $paymentTokenManagement
     * @param Session $customerSession
     */
    public function __construct(
        private PaymentTokenManagementInterface $paymentTokenManagement,
        private Session $customerSession
    ) {
    }

    /**
     * @return array
     */
    public function getCustomerTokens(): array
    {
        $customerId = $this->customerSession->getCustomerId();

        return $this->paymentTokenManagement->getListByCustomerId($customerId);
    }

    /**
     * @return string
     */
    public function getSomeText(): string
    {
        return 'someTextToReturn';
    }
}
