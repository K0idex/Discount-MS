<?php
/**
 * Discount Management System
 * Professional Entry Point
 * 
 * @author JosephK
 * @version 1.0.0
 */

session_start();

// Load configuration
$appConfig = require_once __DIR__ . '/../config/app.php';
$dbConfig = require_once __DIR__ . '/../config/database.php';

// Load classes
require_once __DIR__ . '/../src/DatabaseManager.php';
require_once __DIR__ . '/../src/DiscountController.php';

// Initialize application
$db = new DatabaseManager($dbConfig);
$discountController = new DiscountController($db, $appConfig);

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'apply_discount') {
        $cardNumber = $_POST['card_number'] ?? '';
        $discountPercentage = (int)($_POST['discount_percentage'] ?? 0);
        
        $result = $discountController->applyDiscount($cardNumber, $discountPercentage);
        
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
            if (isset($result['details'])) {
                $_SESSION['success_message'] .= '<br>' . $result['details'];
            }
        } else {
            $_SESSION['error_message'] = $result['error'];
        }
        
        // Redirect to prevent form resubmission on refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif ($_POST['action'] === 'clear_discounts') {
        $db->clearDatabase();
        $_SESSION['success_message'] = '🗑️ All discount data cleared successfully!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif ($_POST['action'] === 'clear_all') {
        $db->clearAllData();
        $_SESSION['success_message'] = '🔄 Database completely reset with fresh sample data!';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get messages from session and clear them
$message = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Fetch data for display
$recentDiscounts = $db->getRecentDiscounts($appConfig['display']['recent_discounts_limit']);
$ticketSummaries = $db->getTicketSummaries();
$availableTickets = $db->getAvailableTickets();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $appConfig['app']['name'] ?> v<?= $appConfig['app']['version'] ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                🎫 <?= $appConfig['app']['name'] ?> 
                <small class="text-muted">v<?= $appConfig['app']['version'] ?></small>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <div class="row">
                <!-- Left Column: Main Application -->
                <div class="col-md-8">
                    <h2 class="mb-4">Apply Discount</h2>
                    
                    <!-- Alerts -->
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Discount Form -->
                    <div class="card mb-4 discount-form-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h4 class="card-title text-white mb-2">💳 Apply New Discount</h4>
                                <p class="card-subtitle text-white-50">Select a card and discount percentage to apply</p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="apply_discount">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">💳 Card Number</label>
                                        <input type="text" name="card_number" class="form-control" 
                                               placeholder="Enter card number (e.g., 001)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">💰 Discount Percentage</label>
                                        <select name="discount_percentage" class="form-select" required>
                                            <option value="">Select discount %</option>
                                            <?php foreach ($appConfig['discount']['allowed_percentages'] as $percentage): ?>
                                                <option value="<?= $percentage ?>"><?= $percentage ?>% OFF</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-apply-discount w-100">
                                            ⚡ Apply
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Discounts -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">📊 Recent Discounts Applied</h4>
                        <span class="badge bg-primary rounded-pill px-3 py-2">
                            <?= count($recentDiscounts) ?> transactions
                        </span>
                    </div>
                    
                    <div class="card shadow-sm border-0" style="background: #D5B9B2;">
                        <div class="card-header bg-transparent border-0 pb-0">
                            <div class="row text-center">
                                <div class="col-2"><small class="text-muted fw-bold">ID</small></div>
                                <div class="col-2"><small class="text-muted fw-bold">TICKET</small></div>
                                <div class="col-2"><small class="text-muted fw-bold">CARD</small></div>
                                <div class="col-2"><small class="text-muted fw-bold">STORE</small></div>
                                <div class="col-2"><small class="text-muted fw-bold">DISCOUNT</small></div>
                                <div class="col-2"><small class="text-muted fw-bold">DATE</small></div>
                            </div>
                        </div>
                        <div class="card-body pt-2" style="max-height: 400px; overflow-y: auto;">
                            <?php if (empty($recentDiscounts)): ?>
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <span style="font-size: 2.5rem;">📊</span>
                                        </div>
                                    </div>
                                    <h5 class="text-muted mb-2">No Transactions Yet</h5>
                                    <p class="text-muted small mb-0">Your discount activity will appear here once you start applying discounts</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($recentDiscounts as $index => $discount): ?>
                                <div class="row align-items-center py-2 <?= $index % 2 === 0 ? 'bg-white' : '' ?> rounded mb-1">
                                    <div class="col-2 text-center">
                                        <span class="badge bg-secondary text-white rounded-pill">#<?= $discount['id'] ?></span>
                                    </div>
                                    <div class="col-2 text-center">
                                        <span class="badge bg-primary text-white rounded-pill">#<?= $discount['ticket_id'] ?></span>
                                    </div>
                                    <div class="col-2 text-center">
                                        <span class="badge bg-info text-white rounded-pill">
                                            <?= htmlspecialchars($discount['card_number']) ?>
                                        </span>
                                    </div>
                                    <div class="col-2 text-center">
                                        <small class="text-muted fw-bold">
                                            <?= htmlspecialchars($discount['store_name']) ?>
                                        </small>
                                    </div>
                                    <div class="col-2 text-center">
                                        <span class="badge bg-success rounded-pill fs-6 px-3 py-2">
                                            <?= $discount['discount_percentage'] ?>% OFF
                                        </span>
                                    </div>
                                    <div class="col-2 text-center">
                                        <small class="text-muted fw-bold">
                                            <?= date('M j') ?><br>
                                            <span style="font-size: 0.75rem; font-weight: bold;"><?= date('H:i') ?></span>
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stacked Discounts Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-3 mt-5">
                        <h4 class="mb-0">🎯 Stacked Discounts Summary</h4>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#stackedDiscountsSection" aria-expanded="false" aria-controls="stackedDiscountsSection">
                            <i class="fas fa-chevron-down"></i> Toggle View
                        </button>
                    </div>
                    
                    <div class="collapse" id="stackedDiscountsSection">
                        <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 fs-4"></i>
                                <div>
                                    <strong>Multiple discounts stack up!</strong> Each ticket can have multiple discounts applied that accumulate together.<br>
                                    <small>Maximum total allowed: <span class="badge bg-primary"><?= $appConfig['discount']['max_total_percentage'] ?>%</span></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow-sm border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">💰 Active Stacked Discounts</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ticket ID</th>
                                            <th>Card Number</th>
                                            <th>Store</th>
                                            <th>Individual Discounts</th>
                                            <th>Total Stacked Discount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($ticketSummaries)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <div class="p-3">
                                                        <i class="display-4">🎯</i>
                                                        <p class="mb-0">No stacked discounts yet</p>
                                                        <small>Apply multiple discounts to the same card to see stacking in action!</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($ticketSummaries as $summary): ?>
                                            <tr>
                                                <td><strong class="text-primary">#<?= $summary['id'] ?></strong></td>
                                                <td>
                                                    <span class="badge bg-info text-white rounded-pill">
                                                        <?= htmlspecialchars($summary['card_number']) ?>
                                                    </span>
                                                </td>
                                                <td class="fw-bold"><?= htmlspecialchars($summary['store_name']) ?></td>
                                                <td>
                                                    <span class="badge badge-individual-discounts rounded-pill">
                                                        <?= $summary['discount_count'] ?> discounts
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6 rounded-pill px-3 py-2">
                                                        <?= $summary['total_discount'] ?>% TOTAL
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Information -->
                <div class="col-md-4">
                    <!-- Available Cards -->
                    <div class="info-box">
                        <h5 class="fw-bold">📋 Available Cards</h5>
                        <p class="text-muted">Just enter the card number:</p>
                        <div class="available-tickets">
                            <?php foreach ($availableTickets as $ticket): ?>
                            <div class="mb-2 p-2 border rounded bg-light">
                                <strong class="text-primary">Card: <?= htmlspecialchars(str_replace('CARD', '', $ticket['card_number'])) ?></strong><br>
                                <small class="text-muted">
                                    Ticket #<?= $ticket['id'] ?> • <?= htmlspecialchars($ticket['store_name']) ?><br>
                                    Full card: <?= htmlspecialchars($ticket['card_number']) ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Database Management -->
                    <div class="info-box">
                        <h5 class="fw-bold">🗑️ Database Management</h5>
                        <p class="text-muted small">Clear data when needed:</p>
                        
                        <!-- Clear Discounts Only -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear_discounts">
                            <button type="submit" class="btn btn-warning btn-sm w-100 mb-2" 
                                    onclick="return confirm('Clear all discount data? This will keep cards but remove all applied discounts.')">
                                🗑️ Clear Discounts Only
                            </button>
                        </form>
                        
                        <!-- Clear Everything -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear_all">
                            <button type="submit" class="btn btn-danger btn-sm w-100" 
                                    onclick="return confirm('⚠️ RESET EVERYTHING? This will clear all data and restore sample cards.')">
                                🔄 Reset Everything
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container text-center">
            <small><?= $appConfig['app']['name'] ?> v<?= $appConfig['app']['version'] ?> | Professional Discount Management</small>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
</body>
</html>