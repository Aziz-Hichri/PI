// Global configuration
const config = {
    baseUrl: window.appConfig?.baseUrl || '',
    minPasswordLength: 6,
    minUsernameLength: 3
};

// Utility functions
function showError(element, message) {
    const errorDiv = document.getElementById(element);
    if (errorDiv) {
        errorDiv.textContent = message;
    }
}

function validateEmail(email) {
    return email.includes('@') && email.includes('.');
}

function validateUsername(username) {
    return /^[a-zA-Z0-9]+$/.test(username);
}

function showSuccessMessage(message) {
    const div = document.createElement('div');
    div.className = 'success-message';
    div.textContent = message;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}

// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    // Login form handling
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!validateEmail(email)) {
                showError('error-message', 'Please enter a valid email address');
                return;
            }
            if (password.length < config.minPasswordLength) {
                showError('error-message', 'Password must be at least 6 characters long');
                return;
            }
            loginForm.submit();
        });
    }

    // Register form handling
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = {
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                confirmPassword: document.getElementById('confirm_password').value
            };

            if (formData.username.length < config.minUsernameLength) {
                showError('error-message', 'Username must be at least 3 characters long');
                return;
            }
            if (!validateUsername(formData.username)) {
                showError('error-message', 'Username can only contain letters and numbers');
                return;
            }
            if (!validateEmail(formData.email)) {
                showError('error-message', 'Please enter a valid email address');
                return;
            }
            if (formData.password.length < config.minPasswordLength) {
                showError('error-message', 'Password must be at least 6 characters long');
                return;
            }
            if (formData.password !== formData.confirmPassword) {
                showError('error-message', 'Passwords do not match');
                return;
            }
            registerForm.submit();
        });
    }

    // Handle favorites
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const recipeId = this.dataset.recipeId;
            try {
                const response = await fetch(`${config.baseUrl}/views/toggle_favorite.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ recipe_id: recipeId })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.classList.toggle('favorited');
                    this.querySelector('.heart-icon').textContent = this.classList.contains('favorited') ? '❤' : '♡';
                    this.querySelector('.favorite-text').textContent = this.classList.contains('favorited') ? 'Saved' : 'Save Recipe';
                    
                    if (data.action === 'added') {
                        showSuccessMessage('Recipe added to favorites!');
                    }
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
            }
        });
    });

    // Ingredient search
    const searchInput = document.getElementById('ingredient-search');
    const suggestionsList = document.getElementById('ingredient-suggestions');
    const selectedList = document.getElementById('selected-ingredients');
    const hiddenInput = document.getElementById('ingredients-hidden');
    const searchForm = document.querySelector('.ingredient-search-form');

    if (searchInput && suggestionsList) {
        let searchTimeout;
        const selectedIngredients = new Set();
        
        // Restore previous search if exists
        if (window.initialIngredients && Array.isArray(window.initialIngredients)) {
            window.initialIngredients.forEach(ingredient => {
                if (ingredient.trim()) {
                    addIngredient({ name: ingredient.trim() });
                }
            });
        }
        
        // Show/hide suggestions list on focus/blur
        searchInput.addEventListener('focus', () => {
            if (suggestionsList.children.length > 0) {
                suggestionsList.classList.add('active');
            }
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) {
                suggestionsList.classList.remove('active');
            }
        });
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionsList.innerHTML = '';
                suggestionsList.classList.remove('active');
                return;
            }

            searchTimeout = setTimeout(async () => {
                try {
                    console.log('Searching with base URL:', config.baseUrl);
                    const response = await fetch(`${config.baseUrl}/views/search_ingredients.php?q=${encodeURIComponent(query)}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const ingredients = await response.json();
                    console.log('Received ingredients:', ingredients);
                    
                    // Display suggestions
                    suggestionsList.innerHTML = '';
                    ingredients.forEach(ingredient => {
                        // Skip if already selected
                        if (selectedIngredients.has(ingredient.name)) return;
                        
                        const item = document.createElement('li');
                        item.className = 'suggestion-item';
                        item.textContent = ingredient.name;
                        item.addEventListener('click', () => {
                            addIngredient(ingredient);
                            searchInput.value = '';
                            suggestionsList.innerHTML = '';
                            suggestionsList.classList.remove('active');
                        });
                        suggestionsList.appendChild(item);
                    });
                    
                    if (suggestionsList.children.length > 0) {
                        suggestionsList.classList.add('active');
                    } else {
                        suggestionsList.classList.remove('active');
                    }
                } catch (error) {
                    console.error('Error searching ingredients:', error);
                }
            }, 300);
        });

        // Add ingredient function
        function addIngredient(ingredient) {
            if (!selectedList) return;
            
            if (selectedIngredients.has(ingredient.name)) return;
            
            const tag = document.createElement('div');
            tag.className = 'ingredient-tag';
            tag.innerHTML = `
                <span>${ingredient.name}</span>
                <button type="button" class="remove-ingredient" aria-label="Remove ${ingredient.name}">×</button>
            `;

            tag.querySelector('.remove-ingredient').addEventListener('click', () => {
                selectedIngredients.delete(ingredient.name);
                tag.remove();
                updateIngredients();
            });

            selectedList.appendChild(tag);
            selectedIngredients.add(ingredient.name);
            updateIngredients();
            console.log('Current ingredients:', Array.from(selectedIngredients));
        }

        // Update hidden input with current ingredients
        function updateIngredients() {
            if (!hiddenInput) return;
            const ingredients = Array.from(selectedIngredients);
            hiddenInput.value = ingredients.join(',');
        }

        // Prevent form submission if no ingredients selected
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                if (selectedIngredients.size === 0) {
                    e.preventDefault();
                    alert('Please select at least one ingredient');
                    return;
                }
                console.log('Submitting search with ingredients:', hiddenInput.value);
            });
        }
    }
});
