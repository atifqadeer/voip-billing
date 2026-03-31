<?php
// app/Traits/CurrencyTrait.php

namespace App\Traits;

use App\Models\Setting;
use App\Models\CurrencyList;

trait CurrencyTrait
{
    /**
     * Fetch the currency from the settings table.
     *
     * @return string
     */
    public function getCurrency()
    {
        $currencySetting = Setting::where('param', 'currency')->first();
        
        // Default to USD if not found
        return $currencySetting ? $currencySetting->value : 'GBP';
    }

    /**
     * Format a given amount with the currency.
     *
     * @param float $amount
     * @return string
     */
    public function formatCurrency($amount)
    {
        $currency = $this->getCurrency();  // Fetch currency

        $currencyList = CurrencyList::where('code',$currency)->first();

        $currencySymbol = $currencyList ? $currencyList->symbol : '£';
        
        return $currencySymbol . ' ' . number_format($amount, 2);
    }

    public function currencySymbol()
    {
        $currency = $this->getCurrency();  // Fetch currency

        $currencyList = CurrencyList::where('code',$currency)->first();

        $currencySymbol = $currencyList ? $currencyList->symbol : '£';
        
        return $currencySymbol;
    }
}
