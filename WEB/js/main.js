(function () {
    // Dark Mode Toggle Functionality
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    if (darkModeToggle) {
        const applyDarkMode = (isDarkMode) => {
            document.body.classList.toggle('dark-mode', isDarkMode);
            localStorage.setItem('darkMode', isDarkMode);
            darkModeToggle.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
        };

        // Toggle dark mode on button click
        darkModeToggle.addEventListener('click', () => {
            const isDarkMode = !document.body.classList.contains('dark-mode');
            applyDarkMode(isDarkMode);
        });

        // Apply dark mode from localStorage on page load
        const isDarkModeSaved = localStorage.getItem('darkMode') === 'true';
        applyDarkMode(isDarkModeSaved);
    }

    // Highlight Active Navigation Link
    function highlightActiveNavLink() {
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('nav a').forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === currentPage);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        highlightActiveNavLink();
    });

    // Show Status Messages (Success or Error)
    function showMessage(message, type = 'success') {
        const messageDiv = document.getElementById('status-message');
        if (!messageDiv) return;

        messageDiv.innerText = message;
        messageDiv.className = type === 'success' ? 'success' : 'error';
        messageDiv.classList.remove('hidden');

        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 3000);
    }

    // Logout Functionality
    window.logout = function () {
        fetch('/backend/logout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin' // Include session cookies
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('You have been logged out.', 'success');
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 1000);
            } else {
                showMessage('Logout failed. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error during logout:', error);
            showMessage('An error occurred. Please try again later.', 'error');
        });
    };
})();