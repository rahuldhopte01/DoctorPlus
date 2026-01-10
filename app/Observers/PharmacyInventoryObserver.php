<?php

namespace App\Observers;

use App\Models\PharmacyInventory;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PharmacyInventoryObserver
{
    /**
     * Handle the PharmacyInventory "created" event.
     */
    public function created(PharmacyInventory $pharmacyInventory): void
    {
        $this->checkLowStock($pharmacyInventory);
    }

    /**
     * Handle the PharmacyInventory "updated" event.
     */
    public function updated(PharmacyInventory $pharmacyInventory): void
    {
        // Check if quantity or low_stock_threshold changed
        if ($pharmacyInventory->isDirty(['quantity', 'low_stock_threshold'])) {
            $this->checkLowStock($pharmacyInventory);
        }
    }

    /**
     * Handle the PharmacyInventory "deleted" event.
     */
    public function deleted(PharmacyInventory $pharmacyInventory): void
    {
        //
    }

    /**
     * Handle the PharmacyInventory "restored" event.
     */
    public function restored(PharmacyInventory $pharmacyInventory): void
    {
        //
    }

    /**
     * Handle the PharmacyInventory "force deleted" event.
     */
    public function forceDeleted(PharmacyInventory $pharmacyInventory): void
    {
        //
    }

    /**
     * Check if inventory is low stock and send notifications.
     */
    protected function checkLowStock(PharmacyInventory $inventory): void
    {
        // Update stock status first
        $inventory->updateStockStatus();

        // Check if quantity is at or below threshold
        if ($inventory->quantity <= $inventory->low_stock_threshold && $inventory->quantity > 0) {
            $this->sendLowStockNotifications($inventory);
        }
    }

    /**
     * Send low stock notifications to pharmacy admin and super admin.
     */
    protected function sendLowStockNotifications(PharmacyInventory $inventory): void
    {
        try {
            $pharmacy = $inventory->pharmacy;
            $medicine = $inventory->medicine;
            $brand = $inventory->brand;

            if (!$pharmacy || !$medicine || !$brand) {
                return;
            }

            $medicineName = $medicine->name . ($brand->brand_name ? ' - ' . $brand->brand_name : '');
            $title = 'Low Stock Alert';
            $message = "Medicine '{$medicineName}' is running low. Current quantity: {$inventory->quantity}, Threshold: {$inventory->low_stock_threshold}";

            // Notify pharmacy admin/owner
            if ($pharmacy->owner_user_id) {
                Notification::create([
                    'user_id' => $pharmacy->owner_user_id,
                    'pharmacy_id' => $pharmacy->id,
                    'pharmacy_inventory_id' => $inventory->id,
                    'title' => $title,
                    'message' => $message,
                    'user_type' => 'pharmacy',
                    'notification_type' => Notification::TYPE_LOW_STOCK,
                ]);
            }

            // Notify super admin users
            $superAdmins = User::role('super admin')->get();
            foreach ($superAdmins as $admin) {
                // Check if notification already exists for this admin and inventory item
                $exists = Notification::where('user_id', $admin->id)
                    ->where('pharmacy_inventory_id', $inventory->id)
                    ->where('notification_type', Notification::TYPE_LOW_STOCK)
                    ->where('created_at', '>=', now()->subHour()) // Avoid duplicate notifications within 1 hour
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'pharmacy_id' => $pharmacy->id,
                        'pharmacy_inventory_id' => $inventory->id,
                        'title' => $title . ' - ' . $pharmacy->name,
                        'message' => $message . " at pharmacy: {$pharmacy->name}",
                        'user_type' => 'admin',
                        'notification_type' => Notification::TYPE_LOW_STOCK,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error sending low stock notifications: ' . $e->getMessage());
        }
    }
}
