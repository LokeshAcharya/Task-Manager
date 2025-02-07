document.addEventListener('DOMContentLoaded', () => {
    loadUserProfile();
});

// Load User Profile
function loadUserProfile() {
    fetch('/backend/profile.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin' // Include session cookies
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const user = data.user;
            document.getElementById('name').value = user.full_name || '';
            document.getElementById('email').value = user.email || '';
        } else {
            alert(data.message || 'Failed to load profile.');
        }
    })
    .catch(error => {
        console.error('Error loading profile:', error);
        alert('An error occurred while loading the profile.');
    });
}

// Update User Profile
document.getElementById('profile-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const newName = document.getElementById('name').value.trim();
    const newPassword = document.getElementById('new-password').value.trim();

    if (!newName) {
        alert('Name cannot be empty.');
        return;
    }

    const userData = { full_name: newName };
    if (newPassword) {
        if (newPassword.length < 6) {
            alert('Password must be at least 6 characters long.');
            return;
        }
        userData.password = newPassword;
    }

    fetch('/WEB/backend/profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData),
        credentials: 'same-origin' // Include session cookies
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            document.getElementById('new-password').value = '';
        } else {
            alert(data.message || 'Failed to update profile.');
        }
    })
    .catch(error => {
        console.error('Error updating profile:', error);
        alert('An error occurred while updating the profile.');
    });
});