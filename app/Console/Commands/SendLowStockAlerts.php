<?php

namespace App\Console\Commands;

use App\Mail\LowStockAlertMail;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlerts extends Command
{
    protected $signature = 'products:low-stock-alerts';

    protected $description = 'Send email alerts for products with low stock levels';

    public function handle()
    {
        $lowStockProducts = Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('No low-stock products found.');
            return Command::SUCCESS;
        }

        $adminEmails = Customer::where('is_admin', true)
            ->pluck('email')
            ->toArray();

        if (empty($adminEmails)) {
            $this->warn('No admin email addresses found.');
            return Command::FAILURE;
        }

        // Send in-app notification
        \App\Services\AdminNotificationService::notifyLowStock($lowStockProducts);

        try {
            Mail::to($adminEmails)
                ->send(new LowStockAlertMail($lowStockProducts));

            $this->info("Low stock alert sent for {$lowStockProducts->count()} products to " . count($adminEmails) . " admin(s).");
        } catch (\Exception $e) {
            Log::error('Low stock alert email failed', [
                'product_count' => $lowStockProducts->count(),
                'error' => $e->getMessage(),
            ]);
            $this->error('Failed to send low stock alert: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
