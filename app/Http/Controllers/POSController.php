<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Service;
use App\Models\Product;
use App\Models\ServicePackage;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Coupon;
use App\Models\FbrLog;
use Illuminate\Support\Facades\Http;

class POSController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $services = Service::all();
        $products = Product::where('current_stock', '>', 0)->get();
        $packages = ServicePackage::where('is_active', true)->get();
        $popularServices = Service::where('is_popular', true)->get();

        return view('pos.index', compact('categories', 'services', 'products', 'packages', 'popularServices'));
    }

    public function payment()
    {
        return view('pos.payment');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'payment_method' => 'required|string',
                'total_amount' => 'required|numeric',
                'payable_amount' => 'required|numeric',
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'nullable|string|max:255',
                'discount' => 'nullable|numeric',
                'tendered_amount' => 'nullable|numeric',
            ]);

            return DB::transaction(function () use ($request) {
                $invoice = Invoice::create([
                    'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                    'user_id' => Auth::id() ?? 1, // Fallback for dev
                    'customer_id' => $request->customer_id,
                    'customer_name' => $request->customer_name,
                    'total_amount' => $request->total_amount,
                    'tax' => $request->tax ?? 0,
                    'discount' => $request->discount ?? 0,
                    'payable_amount' => $request->payable_amount,
                    'payment_method' => $request->payment_method,
                    'tendered_amount' => $request->tendered_amount ?? $request->payable_amount,
                    'status' => 'paid',
                ]);

                foreach ($request->items as $item) {
                    $subtotal = $item['subtotal'] ?? ($item['price'] * $item['quantity']);

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'itemizable_id' => $item['id'],
                        'itemizable_type' => $item['type'] === 'service' ? Service::class : ($item['type'] === 'package' ? ServicePackage::class : Product::class),
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $subtotal,
                    ]);

                    if ($item['type'] === 'product') {
                        $product = Product::find($item['id']);
                        if ($product) {
                            $product->decrement('current_stock', $item['quantity']);

                            // Track product usage
                            ProductUsage::create([
                                'product_id' => $item['id'],
                                'invoice_id' => $invoice->id,
                                'quantity_used' => $item['quantity'],
                                'usage_date' => now(),
                                'notes' => 'Sold via POS'
                            ]);
                        }
                    } elseif ($item['type'] === 'service') {
                        // Auto-deduct service supplies if configured
                        $this->deductServiceSupplies($item['id'], $item['quantity'], $invoice->id);
                    } elseif ($item['type'] === 'package') {
                        // Handle package services and their supplies
                        $package = ServicePackage::with('services')->find($item['id']);
                        if ($package) {
                            foreach ($package->services as $service) {
                                $this->deductServiceSupplies($service->id, $item['quantity'], $invoice->id);
                            }
                        }
                    }
                }

                // --- FBR Integration Preparation ---
                $totalTax = $request->tax ?? 0;
                $totalItems = count($request->items);
                $taxPerItem = $totalItems > 0 ? round($totalTax / $totalItems, 2) : 0;

                $buyerName = $request->customer_name;
                if (!$buyerName && $request->customer_id) {
                    $customer = Customer::find($request->customer_id);
                    $buyerName = $customer ? $customer->name : 'Walk-in Customer';
                }
                $buyerName = $buyerName ?: 'Walk-in Customer';

                $buyerPhone = null;
                if ($request->customer_id) {
                    $customer = Customer::find($request->customer_id);
                    $buyerPhone = $customer ? $customer->phone : null;
                }
                $buyerPhone = $buyerPhone ?: '0000-0000000';

                $posServiceFee = 1.00; // Fixed FBR POS Service Fee
                $finalPayable = (float) $request->payable_amount + $posServiceFee;

                $fbrPayload = [
                    'InvoiceNumber' => $invoice->invoice_no,
                    'POSID' => (int) env('FBR_POS_ID', 819568),
                    'USIN' => 'USIN-' . strtoupper(Str::random(10)),
                    'DateTime' => now()->format('Y-m-d H:i:s'),
                    'BuyerName' => $buyerName,
                    'BuyerPhoneNumber' => $buyerPhone,
                    'BuyerNTN' => "",
                    'TotalBillAmount' => round($finalPayable, 2),
                    'TotalQuantity' => round((float) collect($request->items)->sum('quantity'), 2),
                    'TotalSaleValue' => round((float) $request->total_amount, 2),
                    'TotalTaxCharged' => round((float) $totalTax, 2),
                    'Discount' => round((float) ($request->discount ?? 0.0), 2),
                    'FurtherTax' => round($posServiceFee, 2),
                    'PaymentMode' => $request->payment_method == 'cash' ? 1 : 2, // 1: Cash, 2: Card
                    'InvoiceType' => 1, // 1: Standard, 2: Return
                    'items' => collect($request->items)->map(function ($item) use ($taxPerItem) {
                        $price = (float) ($item['price'] ?? 0.0);
                        $quantity = (float) ($item['quantity'] ?? 0.0);
                        $subtotal = (float) ($item['subtotal'] ?? ($price * $quantity));

                        return [
                            'ItemCode' => (string) $item['id'],
                            'ItemName' => $item['name'] ?? 'Unknown Item',
                            'Quantity' => round($quantity, 2),
                            'PCTCode' => "11001010", // Services class code
                            'TaxRate' => 5.0, // Fixed strictly to 5% GST on all products/services
                            'SaleValue' => round($price, 2),
                            'Discount' => 0.0,
                            'TaxCharged' => round((float) $taxPerItem, 2),
                            'TotalAmount' => round((float) ($subtotal + $taxPerItem), 2),
                            'FurtherTax' => 0.0,
                            'InvoiceType' => 1,
                            'RefUSIN' => null
                        ];
                    })->toArray(),
                ];

                // --- LIVE FBR API Call ---
                try {
                    $fbrResponse = Http::withoutVerifying()->withHeaders([
                        'Authorization' => 'Bearer ' . env('FBR_AUTH_CODE', '1298b5eb-b252-3d97-8622-a4a69d5bf818'),
                        'Content-Type' => 'application/json'
                    ])->timeout(15)->post(env('FBR_API_URL', 'https://esp.fbr.gov.pk:8244/FBR/v1/api/Live/PostData'), $fbrPayload);

                    $resData = $fbrResponse->json();
                    $isSuccess = isset($resData['Code']) && $resData['Code'] == '100';

                    FbrLog::create([
                        'invoice_id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no,
                        'payload' => $fbrPayload,
                        'status' => $isSuccess ? 'success' : 'failed',
                        'response' => $resData,
                    ]);

                    $apiResponse = [
                        'Code' => $resData['Code'] ?? '500',
                        'Response' => $resData['Response'] ?? 'FBR API Request Failed',
                        'InvoiceNumber' => $invoice->invoice_no,
                        'FBRInvoiceNumber' => $resData['InvoiceNumber'] ?? null,
                        'QRCode' => isset($resData['InvoiceNumber'])
                            ? "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($resData['InvoiceNumber'])
                            : null,
                    ];
                } catch (\Exception $apiEx) {
                    Log::error('FBR API Connection Error: ' . $apiEx->getMessage());

                    $apiResponse = [
                        'Code' => 'ERR',
                        'Response' => 'Could not connect to FBR API',
                        'InvoiceNumber' => $invoice->invoice_no,
                        'FBRInvoiceNumber' => null,
                        'QRCode' => null,
                    ];

                    FbrLog::create([
                        'invoice_id' => $invoice->id,
                        'invoice_no' => $invoice->invoice_no,
                        'payload' => $fbrPayload,
                        'status' => 'failed',
                        'response' => ['error' => $apiEx->getMessage()],
                    ]);
                }
                // -----------------------------------

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice #' . $invoice->invoice_no . ' generated. ' .
                        ($apiResponse['Code'] == '100'
                            ? 'FBR SYNCED: ' . $apiResponse['FBRInvoiceNumber']
                            : 'FBR FAILED: ' . $apiResponse['Response']),
                    'invoice' => $invoice->load('items.itemizable'),
                    'fbr' => $apiResponse
                ]);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->validator->errors()->first(),
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('POS Checkout Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Checkout Failed: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], 500);
        }
    }

    private function deductServiceSupplies($serviceId, $quantity, $invoiceId)
    {
        // Get service supplies that need to be auto-deducted
        $serviceSupplies = \App\Models\ServiceSupply::where('service_id', $serviceId)
            ->where('is_active', true)
            ->with('product')
            ->get();

        foreach ($serviceSupplies as $supply) {
            $product = $supply->product;
            $totalQuantity = $supply->quantity_per_service * $quantity;

            // Check if product tracks inventory and has sufficient stock
            if ($product && $product->track_inventory && $product->current_stock >= $totalQuantity) {
                // Deduct from inventory
                $product->decrement('current_stock', $totalQuantity);

                // Track product usage
                ProductUsage::create([
                    'product_id' => $product->id,
                    'service_id' => $serviceId,
                    'invoice_id' => $invoiceId,
                    'quantity_used' => $totalQuantity,
                    'usage_date' => now(),
                    'notes' => 'Auto-deducted for service performance'
                ]);
            } else {
                // Log insufficient stock or skip deduction
                // You might want to add logging here or notify staff
                Log::warning("Insufficient stock for auto-deduction: Product {$product->name} needs {$totalQuantity} units, has {$product->current_stock}");
            }
        }
    }

    public function checkCoupon(Request $request)
    {
        $code = $request->input('code');
        $totalAmount = $request->input('total_amount', 0);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Coupon not found']);
        }

        if (!$coupon->isValid(null, $totalAmount)) {
            return response()->json(['success' => false, 'message' => 'Coupon is not valid or requirements not met']);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'code' => $coupon->code,
                'type' => $coupon->type, // fixed or percentage
                'value' => $coupon->value,
                'name' => $coupon->name
            ]
        ]);
    }

    public function searchCustomer(Request $request)
    {
        $q = $request->input('q');

        if (empty($q)) {
            return response()->json(['success' => false, 'message' => 'Query is empty']);
        }

        // Search in-memory since 'name' and 'phone' are encrypted at rest in the DB
        $customer = Customer::all()->filter(function ($c) use ($q) {
            $nameMatch = $c->name && stripos($c->name, $q) !== false;
            $phoneMatch = $c->phone && stripos($c->phone, $q) !== false;
            return $nameMatch || $phoneMatch;
        })->first();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        $hasMembership = $customer->membership_status === 'Active';
        // Assume VIP/membership gives 10% discount for demo purposes, unless otherwise specified in your logic
        $discountPercent = 0;
        if ($hasMembership) {
            $discountPercent = stripos($customer->membership_type, 'VIP') !== false ? 15 : 10;
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'membership_type' => $customer->membership_type,
                'membership_status' => $customer->membership_status,
                'discount_percent' => $discountPercent
            ]
        ]);
    }
}
