<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    public function creating(Sale $sale): void
    {
        $this->calculateTotal($sale);
    }

    public function updating(Sale $sale): void
    {
        $this->calculateTotal($sale);
    }

    private function calculateTotal(Sale $sale): void
    {
        // Ensure values are numeric
        $subtotal = $sale->subtotal ?? 0;
        $descuento = $sale->descuento ?? 0;

        $sale->total = $subtotal - $descuento;
    }
}
