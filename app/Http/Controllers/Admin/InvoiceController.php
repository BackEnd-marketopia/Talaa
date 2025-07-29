<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Spatie\Browsershot\Browsershot;

class InvoiceController extends Controller
{
    public function show(Order $order)
    {
        if (session()->has('lang'))
            app()->setLocale(session()->get('lang'));

        $order = Order::with(['user', 'address', 'orderDetails.product'])->findOrFail($order->id);

        $html = view('orders.invoice', compact('order'))->render();

        $pdfContent = Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->pdf();

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=invoice_order_{$order->id}.pdf",
        ]);
    }
}
