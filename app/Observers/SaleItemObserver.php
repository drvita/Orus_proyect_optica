<?php

namespace App\Observers;

use App\Models\SaleItem;

class SaleItemObserver
{
    public function created(SaleItem $saleitem): void
    {
        $saleitem->writeOffProcess();
    }

    public function deleted(SaleItem $saleitem): void
    {
        // $saleitem->writeOnProcess();
    }
}
