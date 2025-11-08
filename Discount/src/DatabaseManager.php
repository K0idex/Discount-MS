<?php
/**
 * Database Manager
 * Handles all database operations for the Discount System
 */

class DatabaseManager 
{
    private $pdo;
    private $config;
    
    public function __construct($config) 
    {
        $this->config = $config;
        $this->connect();
        $this->createTables();
        $this->seedSampleData();
    }
    
    private function connect() 
    {
        $dbPath = $this->config['database']['path'];
        $this->pdo = new PDO("sqlite:{$dbPath}");
        
        foreach ($this->config['database']['options'] as $option => $value) {
            $this->pdo->setAttribute($option, $value);
        }
    }
    
    private function createTables() 
    {
        // Create stores table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS stores (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create tickets table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS tickets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                card_number TEXT NOT NULL,
                store_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (store_id) REFERENCES stores(id)
            )
        ");
        
        // Create discounts table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS discounts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ticket_id INTEGER,
                store_id INTEGER,
                card_number TEXT NOT NULL,
                discount_percentage DECIMAL(5,2) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (ticket_id) REFERENCES tickets(id),
                FOREIGN KEY (store_id) REFERENCES stores(id)
            )
        ");
    }
    
    private function seedSampleData() 
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM stores");
        if ($stmt->fetchColumn() == 0) {
            $this->pdo->exec("
                INSERT INTO stores (name) VALUES 
                ('Electronics Store'),
                ('Fashion Boutique'),
                ('Grocery Mart')
            ");
            
            $this->pdo->exec("
                INSERT INTO tickets (card_number, store_id) VALUES 
                ('CARD001', 1),
                ('CARD002', 2),
                ('CARD003', 1),
                ('CARD004', 3)
            ");
        }
    }
    
    public function findTicketByCard($cardNumber) 
    {
        $cardSearch = $cardNumber;
        if (is_numeric($cardNumber)) {
            $cardSearch = 'CARD' . str_pad($cardNumber, 3, '0', STR_PAD_LEFT);
        }
        
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE card_number = ? OR card_number = ?");
        $stmt->execute([$cardNumber, $cardSearch]);
        return $stmt->fetch();
    }
    
    public function addDiscount($ticketId, $storeId, $cardNumber, $percentage) 
    {
        $stmt = $this->pdo->prepare("INSERT INTO discounts (ticket_id, store_id, card_number, discount_percentage) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$ticketId, $storeId, $cardNumber, $percentage]);
    }
    
    public function getTicketTotalDiscount($ticketId) 
    {
        $stmt = $this->pdo->prepare("SELECT SUM(discount_percentage) as total_discount FROM discounts WHERE ticket_id = ?");
        $stmt->execute([$ticketId]);
        return $stmt->fetchColumn() ?: 0;
    }
    
    public function getRecentDiscounts($limit = 20) 
    {
        $stmt = $this->pdo->query("
            SELECT d.*, s.name as store_name, t.card_number as ticket_card
            FROM discounts d 
            JOIN stores s ON d.store_id = s.id 
            JOIN tickets t ON d.ticket_id = t.id 
            ORDER BY d.id ASC 
            LIMIT {$limit}
        ");
        return $stmt->fetchAll();
    }
    
    public function getTicketSummaries() 
    {
        $stmt = $this->pdo->query("
            SELECT t.id, t.card_number, s.name as store_name, 
                   COUNT(d.id) as discount_count, 
                   SUM(d.discount_percentage) as total_discount
            FROM tickets t 
            JOIN stores s ON t.store_id = s.id 
            LEFT JOIN discounts d ON t.id = d.ticket_id 
            GROUP BY t.id, t.card_number, s.name
            HAVING COUNT(d.id) > 0
            ORDER BY t.id ASC
        ");
        return $stmt->fetchAll();
    }
    
    public function getAvailableTickets() 
    {
        $stmt = $this->pdo->query("
            SELECT t.*, s.name as store_name 
            FROM tickets t 
            JOIN stores s ON t.store_id = s.id 
            ORDER BY t.id ASC
        ");
        return $stmt->fetchAll();
    }
    
    public function clearDatabase() 
    {
        // Clear all discounts (this is usually what you want to clear)
        $this->pdo->exec("DELETE FROM discounts");
        
        // Reset the auto-increment counter for discounts
        $this->pdo->exec("DELETE FROM sqlite_sequence WHERE name='discounts'");
        
        return true;
    }
    
    public function clearAllData() 
    {
        // Clear all data from all tables
        $this->pdo->exec("DELETE FROM discounts");
        $this->pdo->exec("DELETE FROM tickets");
        $this->pdo->exec("DELETE FROM stores");
        
        // Reset all auto-increment counters
        $this->pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('discounts', 'tickets', 'stores')");
        
        // Re-seed the sample data
        $this->seedSampleData();
        
        return true;
    }
}