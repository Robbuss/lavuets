<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Contract;
use App\Models\Tenant;
use App\Utils\MolliePayment;
use Illuminate\Http\Request;
use App\Utils\InvoiceGenerator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $free = Unit::doesntHave('contracts')->where('size', '>', 0)->where('active', 1)->get();
        $grouped = $free->mapToGroups(function ($item, $key) {
            return [$item['size'] => $item];
        });

        return ['units' => $grouped, 'count' => $free->count()];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // create a tenant
        $tenant = Tenant::create($request->tenant);

        // create a contract that is inactive till after payment is received (activated in the webhook)
        $contract = Contract::create(array_merge($request->contract, [
            'tenant_id' => $tenant->id,
            'auto_invoice' => true,
            'method' => 'addMonth',
            'payment_method' => 'incasso',
        ]));
        $contract->units()->sync($this->getSyncArray($request->units));

        // generate an invoice
        $invoice = new InvoiceGenerator($contract);

        // create a mollie customer, create a mollie payment and store the payment in our database. 
        // TODO: move the hardcoded url to a setting when tenants scope is active.
        $payments = (new MolliePayment($tenant, $contract, $invoice->lastInvoice, 'https://boekonline.opslagmagazijn.nl/webhooks/mollie-first'))->payment();

        // redirect tenant to Mollie checkout page
        return ['success' => true, 'payment_url' => $payments['molliepayment']->getCheckoutUrl()];
    }

    public function getSyncArray($priceArray = [])
    {
        $contractUnitPrice = [];
        foreach ($priceArray as $pu) {
            $contractUnitPrice[$pu['id']] = ['price' => $pu['price']];
        };

        return $contractUnitPrice;
    }
  
}
