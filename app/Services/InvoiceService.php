<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function generateInvoice(Order $order)
    {
        $order->load(['items.item', 'customer']);

        $businessName = Setting::get('profile.business_name', config('business.profile.name', config('app.name')));
        $businessEmail = Setting::get('contact.email', config('business.contact.email', ''));
        $businessPhone = Setting::get('contact.phone', config('business.contact.phone', ''));
        $businessAddress = Setting::get('contact.address', []);

        $data = [
            'order' => $order,
            'businessName' => $businessName,
            'businessEmail' => $businessEmail,
            'businessPhone' => $businessPhone,
            'businessAddress' => $businessAddress,
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf->setPaper('letter');

        return $pdf;
    }

    public function downloadInvoice(Order $order)
    {
        $pdf = $this->generateInvoice($order);
        return $pdf->download("invoice-{$order->id}.pdf");
    }

    public function streamInvoice(Order $order)
    {
        $pdf = $this->generateInvoice($order);
        return $pdf->stream("invoice-{$order->id}.pdf");
    }
}
