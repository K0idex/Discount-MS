// Discount System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any interactive features
    console.log('Discount System Loaded');
    
    // Auto-refresh functionality (commented out for development)
    // setTimeout(() => location.reload(), 30000);
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const cardNumber = form.querySelector('[name="card_number"]');
            const discountPercentage = form.querySelector('[name="discount_percentage"]');
            
            if (!cardNumber.value.trim()) {
                alert('Please enter a card number');
                e.preventDefault();
                return false;
            }
            
            if (!discountPercentage.value) {
                alert('Please select a discount percentage');
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Table row hover effects
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});