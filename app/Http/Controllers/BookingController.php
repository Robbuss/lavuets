<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Setting;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Location;
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
    public function stepLocation()
    {
        // TODO: Rewrite this so it queries the locations table instead of Units..
        $showCount = Setting::where('key', 'show_locations_booking')->first();
        // return Location::with(
        //     ['units' => function ($q) {
        //         return $q->bookableUnits(); // this doesn't seem to work
        //     }])->withCount('units')->get();

        return Unit::bookableUnits()
            ->with(['location', 'location.media'])
            ->get()->groupBy('location_id')->map(function ($q) use ($showCount) {
                return [
                    'units_count' => $showCount->value ? $q->count() : false,
                    'facility_name' => $q->first()->location->facility_name,
                    'id' => $q->first()->location->id,
                    'image' => ($q->first()->location->media->last()) ? $q->first()->location->media->last()->getUrl() : '',
                ];
            });
    }

    public function stepUnits(Request $request)
    {
        $free = Unit::with('media')->bookableUnits()->where('location_id', $request->location_id)->get();
        $free->load('media');
        $m3 = $free->mapToGroups(function ($item) {
            return [
                $item['size_m3'] => $item,
            ];
        });
        $m2 = $free->mapToGroups(function ($item) {
            return [
                $item['size_m2'] => $item,
            ];
        });
        return [
            'm2' => $m2,
            'm3' => $m3
        ];
    }

    public function stepTenant(Request $request){
        return [
            'logo' => Customer::current()->logo
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // create a tenant
        $tenant = Tenant::create(
            array_merge(
                $request->tenant,
                ['customer_id' => Customer::current()->id]
            ),
        );

        // create a contract that is inactive till after payment is received (activated in the webhook)
        $contract = Contract::create(array_merge($request->contract, [
            'customer_id' => Customer::current()->id,
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
