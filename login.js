// Login page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    // Form validation
    loginForm.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Email validation
        if (!emailInput.value || !isValidEmail(emailInput.value)) {
            showError(emailInput, 'Please enter a valid email address');
            isValid = false;
        } else {
            removeError(emailInput);
        }
        
        // Password validation
        if (!passwordInput.value || passwordInput.value.length < 6) {
            showError(passwordInput, 'Password must be at least 6 characters');
            isValid = false;
        } else {
            removeError(passwordInput);
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    // Real-time validation
    emailInput.addEventListener('blur', function() {
        if (this.value && !isValidEmail(this.value)) {
            showError(this, 'Please enter a valid email address');
        } else {
            removeError(this);
        }
    });
    
    passwordInput.addEventListener('blur', function() {
        if (this.value && this.value.length < 6) {
            showError(this, 'Password must be at least 6 characters');
        } else {
            removeError(this);
        }
    });
    
    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        let errorElement = formGroup.querySelector('.error-message');
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            formGroup.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        input.style.borderColor = '#dc2626';
    }
    
    function removeError(input) {
        const formGroup = input.closest('.form-group');
        const errorElement = formGroup.querySelector('.error-message');
        
        if (errorElement) {
            errorElement.remove();
        }
        input.style.borderColor = '#d1d5db';
    }
    
    // Add loading state to form submission
    loginForm.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        submitBtn.disabled = true;
        
        // Re-enable after 3 seconds (in case of error)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});

// Add CSS for error messages
const style = document.createElement('style');
style.textContent = `
    .error-message {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .error-message::before {
        content: "⚠";
        font-size: 14px;
    }
    
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style); 