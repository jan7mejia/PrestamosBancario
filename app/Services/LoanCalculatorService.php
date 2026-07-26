<?php

namespace App\Services;

class LoanCalculatorService
{
    /**
     * Calcula el plan de pagos usando el Sistema Francés (Cuotas fijas).
     */
    public function calculateFrances($amount, $interestRate, $termMonths)
    {
        $monthlyRate = ($interestRate / 100) / 12;
        $installmentAmount = $amount * ($monthlyRate * pow(1 + $monthlyRate, $termMonths)) / (pow(1 + $monthlyRate, $termMonths) - 1);
        
        $plan = [];
        $balance = $amount;
        
        for ($i = 1; $i <= $termMonths; $i++) {
            $interest = $balance * $monthlyRate;
            $principal = $installmentAmount - $interest;
            $balance -= $principal;
            
            $plan[] = [
                'installment_number' => $i,
                'installment_amount' => round($installmentAmount, 2),
                'principal_amount' => round($principal, 2),
                'interest_amount' => round($interest, 2),
                'remaining_balance' => round(abs($balance), 2)
            ];
        }
        
        return $plan;
    }

    /**
     * Calcula el plan de pagos usando el Sistema Alemán (Amortización de capital fija).
     */
    public function calculateAleman($amount, $interestRate, $termMonths)
    {
        $monthlyRate = ($interestRate / 100) / 12;
        $principalAmount = $amount / $termMonths;
        
        $plan = [];
        $balance = $amount;
        
        for ($i = 1; $i <= $termMonths; $i++) {
            $interest = $balance * $monthlyRate;
            $installmentAmount = $principalAmount + $interest;
            $balance -= $principalAmount;
            
            $plan[] = [
                'installment_number' => $i,
                'installment_amount' => round($installmentAmount, 2),
                'principal_amount' => round($principalAmount, 2),
                'interest_amount' => round($interest, 2),
                'remaining_balance' => round(abs($balance), 2)
            ];
        }
        
        return $plan;
    }

    /**
     * Calcula el plan de pagos usando el Sistema Americano (Pago de interés periódico, capital al final).
     */
    public function calculateAmericano($amount, $interestRate, $termMonths)
    {
        $monthlyRate = ($interestRate / 100) / 12;
        $interest = $amount * $monthlyRate;
        
        $plan = [];
        $balance = $amount;
        
        for ($i = 1; $i <= $termMonths; $i++) {
            $principal = ($i == $termMonths) ? $amount : 0;
            $installmentAmount = $interest + $principal;
            $balance -= $principal;
            
            $plan[] = [
                'installment_number' => $i,
                'installment_amount' => round($installmentAmount, 2),
                'principal_amount' => round($principal, 2),
                'interest_amount' => round($interest, 2),
                'remaining_balance' => round(abs($balance), 2)
            ];
        }
        
        return $plan;
    }
}
