<?php

namespace App\Traits;

use App\Events\Purchase\NewPurchase;
use App\Events\Purchase\ProductDelivered;
use App\Exceptions\RequestException;
use App\Marketplace\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait Purchasable {
    /**
     * Runs purchased procedure - automatically marks as delivered
     */
    public function purchased()
    {
        try {
            DB::beginTransaction();
            
            // Basic validation
            if (!$this->offer || !$this->offer->product) {
                throw new \Exception('Invalid product or offer');
            }
            
            // Calculate total price
            $total_price = $this->offer->price * $this->quantity;
            
            // Force BTC as the only payment method
            $this->coin_name = 'btc';
            
            // Get vendor's payment address for BTC
            $vendorAddress = $this->vendor->user->addresses()
                ->where('coin', 'btc')
                ->latest()
                ->first();
                
            // Set required fields with default values
            $this->address = $vendorAddress ? $vendorAddress->address : 'No BTC payment address available';
            $this->type = $this->type ?? 'normal';
            $this->state = 'delivered';
            $this->to_pay = $total_price; // Direct price assignment
            $this->message = $this->message ?? '';
            $this->delivered_product = $this->delivered_product ?? '';
            
            // Prepare purchase
            if (method_exists($this, 'encryptMessage')) {
                $this->encryptMessage();
            }
            
            // Check if we have enough stock
            if ($this->offer->product->quantity < $this->quantity) {
                throw new RequestException('Not enough stock available');
            }
            
            // Subtract quantity from product
            $this->offer->product->substractQuantity($this->quantity);
            $this->offer->product->save();
            
            // If it's an autodelivery product, set the delivered products
            if($this->offer->product->isAutodelivery() && $this->offer->product->digital){
                try {
                    $productsToDelivery = $this->offer->product->digital->getProducts($this->quantity);
                    $this->delivered_product = implode("\n", $productsToDelivery);
                } catch (\Exception $e) {
                    Log::error("Autodelivery failed: " . $e->getMessage());
                }
            }
            
            // Only save the fields that exist in the database
            $this->save(['id', 'buyer_id', 'vendor_id', 'offer_id', 'message', 
                'quantity', 'coin_name', 'type', 'address', 'state', 'to_pay', 
                'delivered_product', 'updated_at', 'created_at']);
            
            DB::commit();
            
            // Fire events after successful commit
            event(new NewPurchase($this));
            event(new ProductDelivered($this));
            
            // Clear the cart
            Cart::getCart()->clearCart();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Purchase failed: " . $e->getMessage());
            throw new RequestException($e->getMessage() ?: 'Error processing purchase. Please try again.');
        }
    }

    /**
     * Cancel the purchase
     */
    public function cancel()
    {
        try{
            DB::beginTransaction();

            // Restore product stock
            $this->offer->product->quantity += $this->quantity;
            $this->offer->product->save();

            $this->state = 'canceled';
            $this->save();

            DB::commit();
            return true;
        }
        catch (\Exception $e){
            DB::rollBack();
            Log::error("Cancel failed: " . $e->getMessage());
            throw new RequestException('Could not cancel the purchase: ' . $e->getMessage());
        }
    }
}