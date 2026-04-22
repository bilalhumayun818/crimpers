<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'user', 'fbrLog']);

        // Apply date range filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                ->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                        ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply combined search filter (Invoice # or Customer)
        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->search);
            $invoiceSearch = ltrim($searchTerm, '#');

            $query->where(function ($q) use ($searchTerm, $invoiceSearch) {
                $q->where('invoice_no', 'like', '%' . $invoiceSearch . '%')
                    ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('customer', function ($cq) use ($searchTerm) {
                        $cq->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Apply amount range filters
        if ($request->filled('min_amount')) {
            $query->where('payable_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('payable_amount', '<=', $request->max_amount);
        }

        $invoices = $query->latest()->paginate(20)->withQueryString();

        // Calculate summary statistics for filtered results
        $totalSales = $query->sum('payable_amount');
        $totalInvoices = $query->count();

        // Calculate period invoices (for the current period if period filter is applied)
        $periodQuery = Invoice::query();
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $periodQuery->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $periodQuery->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $periodQuery->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $periodQuery->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $periodQuery->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $periodQuery->whereMonth('created_at', Carbon::now()->subMonth()->month)
                        ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $periodQuery->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $periodQuery->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        } else {
            // If no period filter, show today's count
            $periodQuery->whereDate('created_at', Carbon::today());
        }
        $periodInvoices = $periodQuery->count();

        return view('invoices.index', compact('invoices', 'totalSales', 'totalInvoices', 'periodInvoices'));
    }

    /**
     * USER DEMAND: "make pagee for that view page where it show sales history propelry"
     * Redirecting all review actions to the new 'history-detail' administrative page.
     * Thermal ticket logic (pos.ticket) is no longer utilized for viewing.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.itemizable', 'fbrLog', 'customer', 'user']);
        return view('invoices.history-detail', compact('invoice'));
    }

    public function historyShow(Invoice $invoice)
    {
        $invoice->load(['items.itemizable', 'fbrLog', 'customer', 'user']);
        return view('invoices.history-detail', compact('invoice'));
    }

    /**
     * Returns the thermal receipt (pos.ticket) for printing via POS payment page iframe.
     */
    public function ticket(Invoice $invoice)
    {
        $invoice->load(['items.itemizable', 'fbrLog', 'customer', 'user']);
        return view('pos.ticket', compact('invoice'));
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['customer', 'user']);

        // Apply same filters as index method
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereDate('created_at', '>=', $request->date_from)
                ->whereDate('created_at', '<=', $request->date_to);
        }

        // Apply period filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                        ->whereYear('created_at', Carbon::now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
            }
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply combined search filter (Invoice # or Customer)
        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->search);
            $invoiceSearch = ltrim($searchTerm, '#');

            $query->where(function ($q) use ($searchTerm, $invoiceSearch) {
                $q->where('invoice_no', 'like', '%' . $invoiceSearch . '%')
                    ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('customer', function ($cq) use ($searchTerm) {
                        $cq->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Apply amount range filters
        if ($request->filled('min_amount')) {
            $query->where('payable_amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('payable_amount', '<=', $request->max_amount);
        }

        $invoices = $query->latest()->get();

        if ($request->format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('invoices.export-pdf', compact('invoices'));
            return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
        }

        // CSV Export
        $filename = 'sales-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Invoice #',
                'Date',
                'Customer',
                'Total Amount',
                'Payment Method',
                'Status',
                'Items'
            ]);

            // CSV data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->created_at->format('Y-m-d H:i:s'),
                    $invoice->customer->name ?? $invoice->customer_name ?? 'Walk-in Customer',
                    number_format($invoice->payable_amount, 2),
                    $invoice->payment_method,
                    $invoice->status,
                    $invoice->items->count() . ' items'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
