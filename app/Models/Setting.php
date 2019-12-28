<?php

namespace App\Models;

use App\Scopes\CustomerScope;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends BaseModel
{
    use LogsActivity;
    protected $fillable = ['customer_id', 'public', 'key', 'value'];

    public function scopePublic()
    {
        return $this->where('public', 1);
    }

    public static function createDefault(Customer $customer)
    {
        foreach (config('settings.default') as $setting) {
            Setting::create([
                'customer_id' => $customer->id,
                'public' => $setting['public'],
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
        // update the name to the company name of the newly created customer.
        Setting::withoutGlobalScope(CustomerScope::class)
            ->where('customer_id', $customer->id)
            ->where('key', 'app_name')
            ->update(['value' => $customer->company_name]);

        // update the name to the company name of the newly created customer.
        Setting::withoutGlobalScope(CustomerScope::class)
            ->where('customer_id', $customer->id)
            ->where('key', 'login_endpoint')
            ->update(['value' => config('app.protocol') . $customer->domain . '.' . config('app.domain') . '/oauth/token']);
    }
}
