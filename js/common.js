// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');

    if (menuToggle && nav) {
        // Toggle menu
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (nav.classList.contains('show')) {
                nav.classList.remove('show');
                menuToggle.setAttribute('aria-expanded', 'false');
            } else {
                nav.classList.add('show');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (nav.classList.contains('show') && !nav.contains(event.target) && !menuToggle.contains(event.target)) {
                nav.classList.remove('show');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu when clicking a nav link
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                nav.classList.remove('show');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });

        // Close menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                nav.classList.remove('show');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});

// Form validation helper
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            
            // Create or update error message
            let errorMsg = input.nextElementSibling;
            if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                errorMsg = document.createElement('div');
                errorMsg.classList.add('error-message');
                input.parentNode.insertBefore(errorMsg, input.nextSibling);
            }
            errorMsg.textContent = `${input.getAttribute('placeholder') || input.getAttribute('name')} is required`;
        } else {
            input.classList.remove('error');
            const errorMsg = input.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.remove();
            }
        }
    });

    return isValid;
}

// Add responsive table support
function makeTablesResponsive() {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent);
        const cells = table.querySelectorAll('td');
        
        cells.forEach((cell, index) => {
            cell.setAttribute('data-label', headers[index % headers.length]);
        });
    });
}

// Initialize responsive features
document.addEventListener('DOMContentLoaded', function() {
    makeTablesResponsive();
}); 
