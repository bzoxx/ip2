document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signup-form');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const birthdate = document.getElementById('birthdate');
    const gender = document.getElementById('gender');
    const submitButton = form.querySelector('button[type="submit"]');

    // Initially disable submit button
    submitButton.disabled = true;

    // Create feedback elements
    const fields = [username, email, password, birthdate, gender];
    fields.forEach(field => {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'validation-feedback';
        feedbackDiv.id = `${field.id}-feedback`;
        field.parentNode.insertBefore(feedbackDiv, field.nextSibling);
    });

    // Function to check if all fields are valid
    function checkFormValidity() {
        const isUsernameValid = username.value.length >= 3;
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value);
        const isPasswordValid = password.value.length >= 6;
        const isGenderValid = gender.value !== '';
        
        let isBirthdateValid = false;
        if (birthdate.value) {
            const selectedDate = new Date(birthdate.value);
            const today = new Date();
            const age = today.getFullYear() - selectedDate.getFullYear();
            isBirthdateValid = age >= 18;
        }

        const isFormValid = isUsernameValid && isEmailValid && isPasswordValid && isBirthdateValid && isGenderValid;
        submitButton.disabled = !isFormValid;
        return isFormValid;
    }

    // Username validation
    username.addEventListener('input', function() {
        const feedback = document.getElementById('username-feedback');
        if (this.value.length < 3) {
            feedback.textContent = 'Username must be at least 3 characters long';
            feedback.className = 'validation-feedback invalid';
            this.setCustomValidity('Username must be at least 3 characters long');
        } else {
            feedback.textContent = '✓';
            feedback.className = 'validation-feedback valid';
            this.setCustomValidity('');
        }
        checkFormValidity();
    });

    // Email validation
    email.addEventListener('input', function() {
        const feedback = document.getElementById('email-feedback');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(this.value)) {
            feedback.textContent = 'Please enter a valid email address';
            feedback.className = 'validation-feedback invalid';
            this.setCustomValidity('Please enter a valid email address');
        } else {
            feedback.textContent = '✓';
            feedback.className = 'validation-feedback valid';
            this.setCustomValidity('');
        }
        checkFormValidity();
    });

    // Password validation
    password.addEventListener('input', function() {
        const feedback = document.getElementById('password-feedback');
        if (this.value.length < 6) {
            feedback.textContent = 'Password must be at least 6 characters long';
            feedback.className = 'validation-feedback invalid';
            this.setCustomValidity('Password must be at least 6 characters long');
        } else {
            feedback.textContent = '✓';
            feedback.className = 'validation-feedback valid';
            this.setCustomValidity('');
        }
        checkFormValidity();
    });

    // Gender validation
    gender.addEventListener('change', function() {
        const feedback = document.getElementById('gender-feedback');
        if (!this.value) {
            feedback.textContent = 'Please select your gender';
            feedback.className = 'validation-feedback invalid';
            this.setCustomValidity('Please select your gender');
        } else {
            feedback.textContent = '✓';
            feedback.className = 'validation-feedback valid';
            this.setCustomValidity('');
        }
        checkFormValidity();
    });

    // Birthdate validation
    birthdate.addEventListener('input', function() {
        const feedback = document.getElementById('birthdate-feedback');
        if (!this.value) {
            feedback.textContent = 'Please select your birth date';
            feedback.className = 'validation-feedback invalid';
            this.setCustomValidity('Please select your birth date');
        } else {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const age = today.getFullYear() - selectedDate.getFullYear();
            
            if (age < 18) {
                feedback.textContent = 'You must be at least 18 years old';
                feedback.className = 'validation-feedback invalid';
                this.setCustomValidity('You must be at least 18 years old');
            } else {
                feedback.textContent = '✓';
                feedback.className = 'validation-feedback valid';
                this.setCustomValidity('');
            }
        }
        checkFormValidity();
    });

    // Form submission handling
    form.addEventListener('submit', function(event) {
        if (!checkFormValidity()) {
            event.preventDefault();
            alert('Please fix all validation errors before submitting.');
        }
    });

    // Initial validation check
    fields.forEach(field => field.dispatchEvent(new Event('input')));
    gender.dispatchEvent(new Event('change'));
}); 
