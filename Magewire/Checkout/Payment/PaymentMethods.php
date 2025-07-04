<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\Magewire\Checkout\Payment;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Magewirephp\Magewire\Component;

class PaymentMethods extends Component
{
    public const SELECTED_PAYMENT_METHOD_ID = 'selectedPaymentMethodId';

    /**
     * @var string
     */
    public string $selectedPaymentMethodId = '';

    /**
     * PaymentMethods constructor.
     *
     * @param Session $sessionCheckout
     */
    public function __construct(
        private Session $sessionCheckout
    ) {
    }

    /**
     * @param string $paymentMethodId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setPaymentMethod(string $paymentMethodId): void
    {
        $this->selectedPaymentMethodId = $paymentMethodId;
        $this->sessionCheckout
            ->getQuote()
            ->getPayment()
            ->setAdditionalInformation(
                self::SELECTED_PAYMENT_METHOD_ID,
                $paymentMethodId
            )
            ->save();
    }
}
