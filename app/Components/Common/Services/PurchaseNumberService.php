<?php

namespace App\Components\Common\Services;
use App\Components\Common\Models\Settings;


class PurchaseNumberService
{
    private $setting;
    private $lockedSetting;

    public function __construct()
    {
        
        $this->setting = Settings::query();
        $this->lockedSetting = $this->setting->lockForUpdate()->first();
    }

    public function setNextPurchaseNumber()
    {
        $currentNumber = $this->nextPurchaseNumber();
        $this->increasePurchaseNumber();

        return $currentNumber;
    }

    public function setPurchaseNumber(Int $purchaseNumber)
    {
        $this->lockedSetting->purchase_number = $purchaseNumber;
        return $this->lockedSetting->save();
    }

    public function nextPurchaseNumber()
    {
        return $this->setting->first()->purchase_number;
    }

    private function increasePurchaseNumber()
    {
        $this->lockedSetting->purchase_number++;
        return $this->lockedSetting->save();
    }

    // $currentInvoiceNumber = optional(Invoice::query()->orderByDesc('invoice_number')->limit(1)->first())->invoice_number;
}
