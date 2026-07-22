<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Panel;
use App\Models\PanelCheque;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PanelPaymentReportController extends Controller
{
    public function pending(Request $request): Response
    {
        $panelId = $request->input('panel_id');
        $timezone = DateHelper::timezone();
        $from = $request->filled('date_from')
            ? Carbon::parse($request->input('date_from'), $timezone)
            : now($timezone)->startOfMonth();
        $until = $request->filled('date_to')
            ? Carbon::parse($request->input('date_to'), $timezone)
            : now($timezone)->endOfMonth();
        $generated_at = now();

        $query = PanelCheque::query()
            ->with(['panel', 'bankAccount'])
            ->where('status', 'pending')
            ->whereBetween('created_at', [DateHelper::dayStartUtc($from), DateHelper::dayEndUtc($until)]);

        if ($panelId) {
            $query->where('panel_id', $panelId);
        }

        $cheques = $query->orderBy('created_at')->get();

        $total = $cheques->sum('amount');

        $panels = Panel::orderBy('name')->get();

        $pdf = Pdf::loadView('pdfs.reports.panel-payments-pending', compact(
            'cheques',
            'total',
            'panels',
            'from',
            'until',
            'generated_at',
        ))->setPaper('a4');

        return $pdf->stream('pending-panel-payments.pdf');
    }
}
