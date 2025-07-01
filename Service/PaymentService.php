<?php

namespace Paytrail\PaymentServiceHyvaCheckout\Service;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Command\CommandManagerPoolInterface;
use Paytrail\PaymentService\Gateway\Config\Config;
use Paytrail\PaymentService\Model\Email\Order\PendingOrderEmailConfirmation;
use Paytrail\PaymentService\Model\ProviderForm;
use Psr\Log\LoggerInterface;

class PaymentService
{
    /**
     * PaymentService constructor.
     *
     * @param CommandManagerPoolInterface $commandManagerPool
     * @param SessionCheckout $sessionCheckout
     * @param UrlInterface $url
     * @param ResultFactory $resultFactory
     * @param ProviderForm $providerForm
     * @param PendingOrderEmailConfirmation $pendingOrderEmailConfirmation
     * @param Config $gatewayConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected CommandManagerPoolInterface   $commandManagerPool,
        protected SessionCheckout               $sessionCheckout,
        protected UrlInterface                  $url,
        protected ResultFactory                 $resultFactory,
        protected ProviderForm                  $providerForm,
        protected PendingOrderEmailConfirmation $pendingOrderEmailConfirmation,
        protected Config                        $gatewayConfig,
        protected LoggerInterface               $logger
    ) {
    }

    /**
     * Execute function.
     *
     * @throws CommandException
     * @throws NotFoundException
     */
    public function execute()
    {
        $commandExecutor = $this->commandManagerPool->get(Config::CODE);
        $order = $this->sessionCheckout->getLastRealOrder();
        $selectedPaymentMethod = $this->combineCreditCardMethods(
            $order->getPayment()->getAdditionalInformation()['selectedPaymentMethodId'] ?? Config::CODE
        );

        /**
         * @See \Paytrail\PaymentService\Gateway\Command\Payment::execute
         * @var array $response
         */
        $response = $commandExecutor->executeByCode(
            'payment',
            null,
            [
                'order' => $order,
                'payment_method' => $this->gatewayConfig->getSkipBankSelection() ? Config::CODE : $selectedPaymentMethod,
            ]
        );

        if (empty($response['data'] ?? null)) {
            $this->logger->error('Paytrail Error: Empty response');
        }

        if (!empty($response['error'])) {
            $this->logger->error('Paytrail Error: ' . $response['error']);
        }

        if ($this->gatewayConfig->getSkipBankSelection()) {
            return $response["data"];
        } else {
            $formParams = $this->providerForm->getFormParams(
                $response['data'],
                $selectedPaymentMethod
            );

            // send order confirmation for pending order
            if ($response) {
                $this->pendingOrderEmailConfirmation->pendingOrderEmailSend($order);
            }

            $block = $this->resultFactory->create(ResultFactory::TYPE_PAGE)
                ->getLayout()
                ->createBlock(\Paytrail\PaymentService\Block\Redirect\Paytrail::class)
                ->setUrl($formParams['action'])
                ->setParams($this->getInputs($formParams['inputs']));

            return ['success' => true, 'html_data' => $block->toHtml()];
        }
    }

    /**
     * GetInputs function.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    private function getInputs($inputs)
    {
        $formFields = [];
        foreach ($inputs as $input) {
            $formFields[$input['name']] = $input['value'];
        }

        return $formFields;
    }

    /**
     * Combine credit card methods into one.
     *
     * @param $selectedPaymentMethodId
     * @return string
     */
    private function combineCreditCardMethods($selectedPaymentMethodId): string
    {
        if (str_contains($selectedPaymentMethodId, 'Visa') || str_contains($selectedPaymentMethodId, 'Mastercard')) {
            $selectedPaymentMethodId = 'creditcard';
        }

        return $selectedPaymentMethodId;
    }
}
