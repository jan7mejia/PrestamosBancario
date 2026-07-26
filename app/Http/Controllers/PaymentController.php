<?php

namespace App\Http\Controllers;

use App\Models\Amortization;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Registra el pago de una cuota específica.
     */
    public function pay(Amortization $amortization)
    {
        // Verificar que la cuota no esté ya pagada
        if ($amortization->status === 'paid') {
            return back()->with('error', 'Esta cuota ya fue registrada como pagada.');
        }

        DB::beginTransaction();
        try {
            // Marcar cuota como pagada con fecha actual
            $amortization->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            // Verificar si todas las cuotas del préstamo están pagadas
            $loan = $amortization->loan;
            $pendingCount = $loan->amortizations()->where('status', 'pending')->count();

            if ($pendingCount === 0) {
                $loan->update(['status' => 'paid']);
            }

            DB::commit();
            return back()->with('success', '✅ Cuota N° ' . $amortization->installment_number . ' registrada como pagada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Anula el pago de una cuota (reversa).
     */
    public function unpay(Amortization $amortization)
    {
        if ($amortization->status !== 'paid') {
            return back()->with('error', 'Esta cuota no está marcada como pagada.');
        }

        DB::beginTransaction();
        try {
            $amortization->update([
                'status'  => 'pending',
                'paid_at' => null,
            ]);

            // Si el préstamo estaba marcado como pagado, vuelve a activo
            $loan = $amortization->loan;
            if ($loan->status === 'paid') {
                $loan->update(['status' => 'active']);
            }

            DB::commit();
            return back()->with('success', '↩️ Pago de cuota N° ' . $amortization->installment_number . ' anulado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular el pago: ' . $e->getMessage());
        }
    }
}
