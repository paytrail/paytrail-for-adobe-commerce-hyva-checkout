<?php

namespace Paytrail\PaymentServiceHyvaCheckout\Magewire;

use Magewirephp\Magewire\Component;

class SomeBlock extends Component
{
    /**
     * @var string
     */
    public $nested;

    /**
     * @var string[]
     */
    protected $listeners = [
        'payment_detail_1' => 'someDetail',
        'payment_detail_2' => 'someDetail'
    ];

    public function updatingNested(string $value): string
    {
        // Make sure "nested.foo.bar" is always strtolower (helloworld)
        return strtolower($value);
    }
}
