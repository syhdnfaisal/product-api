<?php

namespace App\Services;

use Xendit\Xendit;
use Xendit\Invoice;

class XenditService
{
    public function __construct()
    {
        Xendit::setApiKey(config('xendit.api_key'));
    }

    public function createInvoice($order)
    {
        $params = [
            'external_id' => 'INV-' . $order->id,
            'payer_email' => 'customer@email.com',
            'description' => 'Payment for Order #' . $order->id,
            'amount' => $order->total_price,
            'success_redirect_url' => 'http://localhost:8000/success',
            'failure_redirect_url' => 'http://localhost:8000/failed',
        ];

        return Invoice::create($params);
    }
}
