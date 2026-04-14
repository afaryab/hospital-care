<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\BankTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankPaymentReportController extends Controller
{
    public function pending(Request $request): Response
    {
        $bankAccountId = $request->input('bank_account_id');
        $from = Carbon::parse($request->input('date_from', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $until = Carbon::parse($request->input('date_to', now()->endOfMonth()->format('Y-m-d')))->endOfDay();
        $generated_at = now();

        $query = BankTransaction::query()
            ->with('bankAccount')
            ->whereNull('linked_transaction_id')
            ->whereBetween('transaction_date', [$from->format('Y-m-d'), $until->format('Y-m-d')]);

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $total = $transactions->sum(fn ($t) => (float) ($t->credit ?? 0));

        $pdf = Pdf::loadView('pdfs.reports.bank-payments-pending', compact(
            'transactions',
            'total',
            'from',
            'until',
            'generated_at',
        ))->setPaper('a4');

        return $pdf->stream('pending-bank-payments.pdf');
    }

    public function received(Request $request): Response
    {
        $bankAccountId = $request->input('bank_account_id');
        $from = Carbon::parse($request->input('date_from', now()->startOfMonth()->format('Y-m-d')))->startOfDay();
        $until = Carbon::parse($request->input('date_to', now()->endOfMonth()->format('Y-m-d')))->endOfDay();
        $generated_at = now();

        $query = BankTransaction::query()
            ->with(['bankAccount', 'linkedTransaction.patient'])
            ->whereNotNull('linked_transaction_id')
            ->whereBetween('transaction_date', [$from->format('Y-m-d'), $until->format('Y-m-d')]);

        if ($bankAccountId) {
            $query->where('bank_account_id', $bankAccountId);
        }

        $transactions = $query->orderBy('transaction_date')->get();

        $total = $transactions->sum(fn ($t) => (float) ($t->credit ?? 0));

        $pdf = Pdf::loadView('pdfs.reports.bank-payments-received', compact(
            'transactions',
            'total',
            'from',
            'until',
            'generated_at',
        ))->setPaper('a4');

        return $pdf->stream('received-bank-payments.pdf');
    }
}
