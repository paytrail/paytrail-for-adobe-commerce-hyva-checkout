<?php

namespace Paytrail\PaymentServiceHyvaCheckout\Model\Magewire\Checkout\Payment;

use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Paytrail\PaymentService\Gateway\Config\Config;
use Paytrail\PaymentServiceHyvaCheckout\Service\PaymentService;
use Hyva\Checkout\Model\Magewire\Payment\AbstractOrderData;
use Hyva\Checkout\Model\Magewire\Payment\AbstractPlaceOrderService;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;

class PaytrailPlaceOrderService extends AbstractPlaceOrderService
{
    private PaymentService $paymentService;

    /**
     * PaytrailPlaceOrderService constructor.
     *
     * @param CartManagementInterface $cartManagement
     * @param PaymentService $paymentService
     * @param Config $gatewayConfig
     * @param AbstractOrderData|null $orderData
     */
    public function __construct(
        CartManagementInterface $cartManagement,
        PaymentService $paymentService,
        private Config $gatewayConfig,
        AbstractOrderData $orderData = null
    ) {
        parent::__construct($cartManagement, $orderData);
        $this->paymentService = $paymentService;
    }

    /**
     * @param Quote $quote
     * @param int|null $orderId
     * @return string
     * @throws CommandException
     * @throws NotFoundException
     */
    public function getRedirectUrl(Quote $quote, ?int $orderId = null): string
    {
        $response = $this->paymentService->execute();
        return $response->getHref();
    }

    /**
     * @param EvaluationResultFactory $resultFactory
     * @param int|null $orderId
     * @return EvaluationResultInterface
     * @throws CommandException
     * @throws NotFoundException
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory, ?int $orderId = null): EvaluationResultInterface
    {
        if (!$this->gatewayConfig->getSkipBankSelection()) {
            $response = $this->paymentService->execute();
            // All the data that you want to add to the frontend.

            // Triggers a regular JS callback that you registered in the frontend.
            $formAction = $resultFactory
                ->createExecutable('my-form-action')
                ->withParams($response);

            // Adds it to the navigation tasks to let it automatically execute when the order is placed.
            $formActionTask = $resultFactory->createNavigationTask('checkout.payment.method.paytrail.task', $formAction)

                // IMPORTANT: only after-executes are triggered.
                ->executeAfter();

            // Push the final result to the frontend.
            return $resultFactory->createbatch()->push($formActionTask);
        }

        return $resultFactory->createSuccess();
    }

    /**
     * @return bool
     */
    public function canRedirect(): bool
    {
        if ($this->gatewayConfig->getSkipBankSelection()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function canPlaceOrder(): bool
    {
        return true;
    }
}
