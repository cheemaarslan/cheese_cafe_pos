<?php

use App\Enums\Ask;
use App\Models\Currency;
use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Smartisan\Settings\Facades\Settings;

return new class extends Migration {
    /**
     * Amounts are quoted in PKR, so the default currency moves off USD and its
     * symbol is left blank - prices render as plain numbers everywhere.
     *
     * @return void
     */
    public function up()
    {
        $currency = Currency::firstOrCreate(
            ['code' => 'PKR'],
            [
                'name'              => 'Pakistani Rupee',
                'symbol'            => '',
                'is_cryptocurrency' => Ask::NO,
                'exchange_rate'     => 1
            ]
        );

        $currency->update(['symbol' => '']);

        Settings::group('site')->set([
            'site_default_currency'        => $currency->id,
            'site_default_currency_symbol' => ''
        ]);

        (new EnvEditor())->addData([
            'CURRENCY'        => 'PKR',
            'CURRENCY_SYMBOL' => ''
        ]);

        Artisan::call('optimize:clear');
    }

    /**
     * @return void
     */
    public function down()
    {
        $currency = Currency::where('code', 'USD')->first();

        if ($currency) {
            Settings::group('site')->set([
                'site_default_currency'        => $currency->id,
                'site_default_currency_symbol' => $currency->symbol
            ]);

            (new EnvEditor())->addData([
                'CURRENCY'        => $currency->code,
                'CURRENCY_SYMBOL' => $currency->symbol
            ]);

            Artisan::call('optimize:clear');
        }
    }
};
