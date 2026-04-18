// Library Management System - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips and popovers (Bootstrap)
    initializeBootstrap();
    
    // Handle alerts auto-close
    autoCloseAlerts();
});

/**
 * Initialize Bootstrap components
 */
function initializeBootstrap() {
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

/**
 * Auto-close alerts after 5 seconds
 */
function autoCloseAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

/**
 * Confirm action with sweet callback
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

/**
 * Show loading spinner
 */
function showLoader(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="spinner"></div>';
    }
}

/**
 * Hide loading spinner
 */
function hideLoader(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '';
    }
}

/**
 * Sanitize HTML
 */
function sanitizeHTML(html) {
    const temp = document.createElement('div');
    temp.textContent = html;
    return temp.innerHTML;
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone
 */
function validatePhone(phone) {
    const re = /^\d{3}-\d{3}-\d{4}$|^\d{10}$/;
    return re.test(phone);
}

/**
 * Get URL parameter
 */
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

/**
 * Format date (YYYY-MM-DD)
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format date and time (YYYY-MM-DD HH:MM)
 */
function formatDateTime(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Get days until date
 */
function getDaysUntilDate(dateString) {
    const today = new Date();
    const targetDate = new Date(dateString);
    const timeDiff = targetDate - today;
    const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
    return daysDiff;
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    const badges = {
        'active': '<span class="badge bg-success">Active</span>',
        'inactive': '<span class="badge bg-secondary">Inactive</span>',
        'returned': '<span class="badge bg-info">Returned</span>',
        'overdue': '<span class="badge bg-danger">Overdue</span>',
        'pending': '<span class="badge bg-warning">Pending</span>',
        'completed': '<span class="badge bg-success">Completed</span>'
    };
    
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}

/**
 * AJAX helper
 */
function makeRequest(url, method = 'GET', data = null, callback = null) {
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json())
    .then(result => {
        if (callback) callback(result);
    })
    .catch(error => {
        console.error('Error:', error);
        if (callback) callback({error: error.message});
    });
}

/**
 * Search with debounce
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Log message (development only)
 */
function log(message) {
    if (typeof console !== 'undefined') {
        console.log('[LMS] ' + message);
    }
}
