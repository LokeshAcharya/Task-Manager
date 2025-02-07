document.addEventListener('DOMContentLoaded', () => {
    const taskList = document.getElementById('task-list');
    const progressBarFill = document.getElementById('progress-bar-fill');

    // Load tasks on page load
    loadTasks();

    // Handle form submission to add a new task
    document.getElementById('add-task-form').addEventListener('submit', async (event) => {
        event.preventDefault();

        const title = document.getElementById('task-title').value.trim();
        const description = document.getElementById('task-desc').value.trim();
        const priority = document.getElementById('task-priority').value;
        const dueDate = document.getElementById('task-due-date').value;

        if (!title || !description || !dueDate) {
            alert('Please fill in all fields.');
            return;
        }

        try {
            const response = await fetch('../backend/dashboard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, description, priority, due_date: dueDate })
            });

            const result = await response.json();
            if (result.success) {
                alert('Task added successfully!');
                loadTasks(); // Reload tasks after adding
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error adding task:', error);
            alert('An error occurred. Please try again later.');
        }
    });

    // Load tasks from the backend
    async function loadTasks() {
        try {
            const response = await fetch('../backend/dashboard.php');
            const result = await response.json();

            if (result.success) {
                renderTasks(result.tasks);
                updateProgressBar(result.tasks);
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
            alert('An error occurred. Please try again later.');
        }
    }

    // Render tasks in the UI
    function renderTasks(tasks) {
        taskList.innerHTML = '';
        tasks.forEach(task => {
            const taskCard = document.createElement('div');
            taskCard.classList.add('task-card');
            taskCard.innerHTML = `
                <h3>${task.title}</h3>
                <p>${task.description}</p>
                <p><strong>Priority:</strong> ${task.priority}</p>
                <p><strong>Due Date:</strong> ${task.due_date}</p>
                <p><strong>Status:</strong> ${task.status}</p>
                <button onclick="updateTaskStatus(${task.id}, 'completed')">Mark as Completed</button>
            `;
            taskList.appendChild(taskCard);
        });
    }

    // Update progress bar
    function updateProgressBar(tasks) {
        const totalTasks = tasks.length;
        const completedTasks = tasks.filter(task => task.status === 'completed').length;
        const progress = totalTasks > 0 ? (completedTasks / totalTasks) * 100 : 0;
        progressBarFill.style.width = `${progress}%`;
    }

    // Filter tasks based on search and priority
    window.filterTasks = () => {
        const searchQuery = document.getElementById('task-search').value.toLowerCase();
        const priorityFilter = document.getElementById('task-filter-priority').value;

        Array.from(taskList.children).forEach(taskCard => {
            const title = taskCard.querySelector('h3').innerText.toLowerCase();
            const priority = taskCard.querySelector('p strong + span').innerText;

            const matchesSearch = title.includes(searchQuery);
            const matchesPriority = priorityFilter === 'all' || priority === priorityFilter;

            taskCard.style.display = matchesSearch && matchesPriority ? 'block' : 'none';
        });
    };

    // Logout function
    window.logout = () => {
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = '../backend/logout.php';
        }
    };
});