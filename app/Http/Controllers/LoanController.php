<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Loan;
use App\Services\LoanCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    protected $calculator;

    public function __construct(LoanCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index()
    {
        $loans = Loan::with('client')->get();
        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $clients = Client::all();
        return view('loans.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0.1',
            'term_months' => 'required|integer|min:1',
            'amortization_system' => 'required|in:frances,aleman,americano',
            'start_date' => 'required|date',
            'guarantee_type' => 'nullable|string',
            'guarantee_details' => 'nullable|string',
            'contract_type' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $loan = Loan::create($validated);

            // Generate Amortization Plan
            $plan = [];
            if ($loan->amortization_system == 'frances') {
                $plan = $this->calculator->calculateFrances($loan->amount, $loan->interest_rate, $loan->term_months);
            } elseif ($loan->amortization_system == 'aleman') {
                $plan = $this->calculator->calculateAleman($loan->amount, $loan->interest_rate, $loan->term_months);
            } elseif ($loan->amortization_system == 'americano') {
                $plan = $this->calculator->calculateAmericano($loan->amount, $loan->interest_rate, $loan->term_months);
            }

            // Save amortizations
            foreach ($plan as $installment) {
                $loan->amortizations()->create([
                    'installment_number' => $installment['installment_number'],
                    'due_date' => \Carbon\Carbon::parse($loan->start_date)->addMonths($installment['installment_number']),
                    'installment_amount' => $installment['installment_amount'],
                    'principal_amount' => $installment['principal_amount'],
                    'interest_amount' => $installment['interest_amount'],
                    'remaining_balance' => $installment['remaining_balance'],
                    'status' => 'pending'
                ]);
            }

            DB::commit();
            return redirect()->route('loans.show', $loan)->with('success', 'Préstamo y plan de pagos generados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al generar el préstamo: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        $loan->load('client', 'amortizations');
        return view('loans.show', compact('loan'));
    }

    public function contract(Loan $loan)
    {
        $loan->load('client');
        return view('loans.contract', compact('loan'));
    }

    public function amortize(Request $request, Loan $loan)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $abono = $request->amount;
        $loan->load('amortizations');
        
        $pendingCuotas = $loan->amortizations->where('status', 'pending');
        if ($pendingCuotas->isEmpty()) {
            return back()->with('error', 'No hay cuotas pendientes para amortizar.');
        }

        $capitalVivo = $pendingCuotas->first()->remaining_balance;
        
        if ($abono > $capitalVivo) {
            return back()->with('error', 'El monto a abonar no puede ser mayor al capital vivo actual.');
        }

        $nuevoCapitalVivo = $capitalVivo - $abono;

        DB::beginTransaction();
        try {
            // Eliminar cuotas pendientes
            $loan->amortizations()->where('status', 'pending')->delete();

            if ($nuevoCapitalVivo > 0) {
                // Recalcular plan con el nuevo capital vivo
                $remainingTerm = $pendingCuotas->count();
                $firstPendingNum = $pendingCuotas->first()->installment_number;
                
                $plan = [];
                if ($loan->amortization_system == 'frances') {
                    $plan = $this->calculator->calculateFrances($nuevoCapitalVivo, $loan->interest_rate, $remainingTerm);
                } elseif ($loan->amortization_system == 'aleman') {
                    $plan = $this->calculator->calculateAleman($nuevoCapitalVivo, $loan->interest_rate, $remainingTerm);
                } elseif ($loan->amortization_system == 'americano') {
                    $plan = $this->calculator->calculateAmericano($nuevoCapitalVivo, $loan->interest_rate, $remainingTerm);
                }

                $lastDueDate = \Carbon\Carbon::parse($loan->start_date)->addMonths($firstPendingNum - 1);

                foreach ($plan as $index => $installment) {
                    $num = $firstPendingNum + $index;
                    $loan->amortizations()->create([
                        'installment_number' => $num,
                        'due_date' => $lastDueDate->copy()->addMonths($index + 1),
                        'installment_amount' => $installment['installment_amount'],
                        'principal_amount' => $installment['principal_amount'],
                        'interest_amount' => $installment['interest_amount'],
                        'remaining_balance' => $installment['remaining_balance'],
                        'status' => 'pending'
                    ]);
                }
            } else {
                $loan->update(['status' => 'paid']);
            }

            DB::commit();
            return back()->with('success', 'Abono a capital procesado y plan recalculado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el abono: ' . $e->getMessage());
        }
    }
}
