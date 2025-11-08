<?php
/**
 * Discount Controller
 * Handles discount application logic
 */

class DiscountController 
{
    private $db;
    private $config;
    
    public function __construct(DatabaseManager $db, $config) 
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    public function applyDiscount($cardNumber, $discountPercentage) 
    {
        // Validate input
        if (empty($cardNumber) || empty($discountPercentage)) {
            return [
                'success' => false,
                'error' => '❌ Please fill in all fields'
            ];
        }
        
        // Validate discount percentage
        if (!in_array($discountPercentage, $this->config['discount']['allowed_percentages'])) {
            return [
                'success' => false,
                'error' => '❌ Invalid discount percentage'
            ];
        }
        
        // Find ticket by card number
        $ticket = $this->db->findTicketByCard($cardNumber);
        if (!$ticket) {
            return [
                'success' => false,
                'error' => "❌ No ticket found with card number '{$cardNumber}'"
            ];
        }
        
        // Calculate existing total discounts
        $existingTotal = $this->db->getTicketTotalDiscount($ticket['id']);
        $newTotal = $existingTotal + $discountPercentage;
        
        // Check if total exceeds maximum allowed
        if ($newTotal > $this->config['discount']['max_total_percentage']) {
            return [
                'success' => false,
                'error' => "❌ Total discount would exceed {$this->config['discount']['max_total_percentage']}%. Current total: {$existingTotal}%"
            ];
        }
        
        // Apply the discount
        if ($this->db->addDiscount($ticket['id'], $ticket['store_id'], $ticket['card_number'], $discountPercentage)) {
            return [
                'success' => true,
                'message' => "✅ Discount of {$discountPercentage}% applied successfully to Ticket #{$ticket['id']} (Card: {$ticket['card_number']})",
                'details' => "Total Accumulated Discount: {$newTotal}%"
            ];
        } else {
            return [
                'success' => false,
                'error' => '❌ Failed to apply discount'
            ];
        }
    }
}