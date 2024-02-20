<?php

use App\Components\FlujoEfectivo\Models\PaymentSource;
use Illuminate\Database\Seeder;

class PaymentSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentSource::create([
            'key' => 'efectivo',
            'title' => 'Efectivo'
        ]);
        PaymentSource::create([
            'key' => 'tranfer',
            'title' => 'Transferencia Bancaria'
        ]);
        PaymentSource::create([
            'key' => 'check',
            'title' => 'Cheque (SBC)'
        ]);
        PaymentSource::create([
            'key' => 'debit_card',
            'title' => 'Tarjeta Debito'
        ]);
        PaymentSource::create([
            'key' => 'credit_card',
            'title' => 'Tarjeta Credito'
        ]);
        PaymentSource::create([
            'key' => 'unidentified',
            'title' => 'No Identificado'
        ]);
    }
}
