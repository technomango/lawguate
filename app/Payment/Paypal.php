<?php

namespace App\Payment;

use App\Models\PaymentGatewaySetting;
use App\Traits\OrganizationTrait;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Modules\AdvSaas\Entities\Plan;
use Modules\AdvSaas\Entities\SaasCheckout;
use Modules\AdvSaas\Entities\SaasOrganizationPlanPurchaseRecord;
use Modules\AdvSaas\Entities\SaasOrganizePlanManagement;
use Modules\AdvSaas\Events\OrganizationCreated;
use Modules\AdvSaas\Jobs\NewOrganizationRegisteredMailJob;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class Paypal
{
    use OrganizationTrait;

    private $_api_context;
    private $mode;
    private $client_id;
    private $secret;
    /**
     * @var Plan
     */
    private $data;

    public function __construct(PaymentGatewaySetting $setting, array $data)
    {
        $this->data = $data;
        $paypal_conf = config('paypal');
        if ($setting->gateway_mode == "live") {
            $paypal_conf['settings']['mode'] = "live";
        }
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $setting->gateway_client_id,
                $setting->gateway_secret_key
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function handle()
    {
        $data = $this->data;
        $planPrice = gv($data, 'planPrice');
        $plan = gv($data, 'plan');
        $record = gv($data, 'record');

        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item1 = new Item();
        $item1->setName($plan->name . '('.$planPrice->subscriptionPackage->name.')')
            ->setCurrency(config('configs.currency_code', 'USD'))
            ->setQuantity(1)
            ->setPrice($planPrice->discount_price > 0 ? $planPrice->discount_price : $planPrice->price);

        $itemList = new ItemList();
        $itemList->setItems(array($item1));

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($planPrice->discount_price > 0 ? $planPrice->discount_price : $planPrice->price);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription(gv($data, 'description'))
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('payment_gateway_success_callback', ['PayPal', $record->id]))
            ->setCancelUrl(route('payment_gateway_cancel_callback', ['PayPal', $record->id]));

        $payment = new Payment();
        $payment->setIntent("order")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $payment->create($this->_api_context);

        return $approvalUrl = $payment->getApprovalLink();
    }


    public function successCallback(Request $request)
    {
        try {
            $payment_id = $request->paymentId;

            $check_existing_payment = SaasOrganizationPlanPurchaseRecord::where('payment_id', $payment_id)->first();
            if ($check_existing_payment) {
                Toastr::error('Payment record previously stored');
                return redirect()->route('saas-plans');
            }

            $record = gv($this->data, 'record');
            $payment = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->input('PayerID'));
            $result = $payment->execute($execution, $this->_api_context);
            if ($result->getState() != 'approved') {
                \Log::info('Payment state for Payment id ' . $result->getState() . ' organization name: ' . $record->organization->name);
                Toastr::error('Payment Failed');
                return redirect()->route('saas-plans');
            }

            $this->organizationApprove($record);
            $record->payment_id = $payment_id;
            $record->save();
            return true;


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error($e->getMessage(), 'Failed');
            return false;
        }
    }

    public function cancelCallback()
    {

    }
}
