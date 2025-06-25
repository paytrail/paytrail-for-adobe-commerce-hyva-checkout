<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Paytrail\PaymentService\Gateway\Command\MethodProvider;
use Paytrail\PaymentService\Gateway\Config\Config;
use Paytrail\PaymentServiceHyvaCheckout\Magewire\Checkout\Payment\PaymentMethods;

class PaymentMethodsViewModel implements ArgumentInterface
{
    /**
     * PaymentMethodsViewModel constructor.
     *
     * @param Session $checkoutSession
     * @param Config $gatewayConfig
     * @param MethodProvider $methodProvider
     */
    public function __construct(
        private Session        $checkoutSession,
        private Config         $gatewayConfig,
        private MethodProvider $methodProvider
    ) {
    }

    /**
     * @return array
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function getGroupedPaymentMethods(): array
    {
        $amount = $this->checkoutSession->getQuote()->getGrandTotal();
        $response = $this->methodProvider->execute(['amount' => $amount]);

        return $response['data'] ?? [];
    }

    /**
     * @param string $paymentMethodId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isSelectedPaymentMethod(string $paymentMethodId): bool
    {
        if ($paymentMethodId === $this->getSelectedMethod()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSelectedMethod(): string
    {
        return $this->checkoutSession->getQuote()->getPayment()->getAdditionalInformation(PaymentMethods::SELECTED_PAYMENT_METHOD_ID) ?? '';
    }

    public function isSkippedBankSelection()
    {
        return $this->gatewayConfig->getSkipBankSelection();
    }
}
