<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function condoneInterest(Installment $installment)
    {
        $installment->update(['interest_amount' => 0]);

        return back()->with('success', 'Intereses condonados exitosamente.');
    }

    public function update(Request $request, Installment $installment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $installment->update($validated);

        return back()->with('success', 'Monto de la cuota actualizado.');
    }

}