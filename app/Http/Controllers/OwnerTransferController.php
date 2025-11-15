<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerTransferController extends Controller
{
    public function transfer(Request $request, Lot $lot)
    {
        $validated = $request->validate([
            'new_owner_id' => 'required|exists:owners,id',
        ]);

        $newOwnerId = $validated['new_owner_id'];

        if ($lot->owner_id == $newOwnerId) {
            return back()->with('error', 'El lote ya pertenece a este socio.');
        }

        $lot->update(['owner_id' => $newOwnerId]);

        return redirect()->route('lots.edit', $lot)->with('success', 'Socio del lote actualizado exitosamente.');
    }
}