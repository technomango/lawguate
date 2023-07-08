<?php

namespace App\Payment;

use App\Models\PaymentGatewaySetting;
use App\Traits\OrganizationTrait;
use Stripe\Charge;
use Stripe\Stripe as StripPayment;

class Stripe
{
    use OrganizationTrait;

    private $setting;
    private $data;

    public function __construct(PaymentGatewaySetting $setting, array $data)
    {
        $this->data = $data;
        $this->setting = $setting;
    }

    public function handle(){
        StripPayment::setApiKey($this->setting->gateway_secret_key);

        $data = $this->data;
        $planPrice = gv($data, 'planPrice');
        $plan = gv($data, 'plan');
        $request = gv($data, 'request');
        $record = gv($data, 'record');

        $charge = Charge::create([
            "amount" => ($planPrice->discount_price ?? $planPrice->price) * 100,
            "currency" => config('configs.currency_code', 'USD'),
            "source" => $request->stripeToken,
            "description" => gv($data, 'description')
        ]);

        if($charge->status == 'succeeded'){
            $this->organizationApprove($record);
            $record->payment_id = $charge->id;
            $record->save();
            $organization = $record->organization;
            return organization_url($organization);
        }

    }
}
