<?php
declare(strict_types=1);

namespace Paytrail\PaymentServiceHyvaCheckout\Magewire\Checkout\Payment\Method;

use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Magewirephp\Magewire\Component\Form;
use Rakit\Validation\Validator;

class Card extends Form
{
    public $foo;

    /**
     * @var string[]
     */
    protected $listeners = [
        'billing_address_activated' => 'refresh',
        'shipping_method_selected' => 'refresh',
        'payment_method_selected' => 'refresh',
        'coupon_code_applied' => 'refresh',
        'coupon_code_revoked' => 'refresh'
    ];

    /**
     * @var string[]
     */
    protected $rules = [
        'card-number' => 'required|numeric',
        'expiration-date' => 'required',
        'cvv' => 'required|numeric|digits:3'
    ];

    /**
     * @var string[]
     */
    protected $messages = [
        'card-number:required' => 'Card Number is a required field.',
        'expiration-date:required' => 'Expiration Date is a required field.',
        'cvv:required' => 'CVV is a required field.'
    ];

    /**
     * @var Session
     */
    private Session $sessionCheckout;

    /**
     * @param Validator $validator
     * @param Session $sessionCheckout
     */
    public function __construct(
        Validator $validator,
        Session $sessionCheckout
    ) {
        $this->sessionCheckout = $sessionCheckout;
        parent::__construct($validator);
    }

    /**
     * Set token
     *
     * @param string $token
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function setToken(string $token): void
    {
        $this->sessionCheckout
            ->getQuote()
            ->getPayment()
            ->setAdditionalInformation(
                'payment_method_nonce',
                $token
            )->save();
    }

    /**
     * Set is card vaulted
     *
     * @param bool $isVaulted
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function setIsVaulted(bool $isVaulted): void
    {
        $this->sessionCheckout
            ->getQuote()
            ->getPayment()
            ->setAdditionalInformation(
                VaultConfigProvider::IS_ACTIVE_CODE,
                $isVaulted
            )->save();
    }

    /**
     * Triggers form validation
     */
    public function place(): void
    {
        try {
            $this->validate();
        } catch (Exception $e) {
            $this->dispatchErrorMessage('There was an error processing your payment.');
        }
    }
}
