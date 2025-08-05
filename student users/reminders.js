// Reminders Management System
class ReminderManager {
  constructor() {
    this.reminders = this.loadReminders();
    this.currentEditId = null;
    this.init();
  }

  init() {
    this.bindEvents();
    this.renderReminders();
    this.updateEmptyState();
  }

  bindEvents() {
    // Modal events
    document.getElementById('addReminderBtn').addEventListener('click', () => this.openModal());
    document.getElementById('closeModal').addEventListener('click', () => this.closeModal());
    document.getElementById('cancelBtn').addEventListener('click', () => this.closeModal());
    document.getElementById('reminderForm').addEventListener('submit', (e) => this.handleSubmit(e));

    // Filter and sort events
    document.getElementById('categoryFilter').addEventListener('change', () => this.renderReminders());
    document.getElementById('sortBy').addEventListener('change', () => this.renderReminders());

    // Close modal on outside click
    document.getElementById('reminderModal').addEventListener('click', (e) => {
      if (e.target.id === 'reminderModal') {
        this.closeModal();
      }
    });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('reminderDate').setAttribute('min', today);
  }

  loadReminders() {
    // Sample data since localStorage/server isn’t used
    return [
      {
        id: 1,
        title: 'Math Class',
        description: 'Calculus II - Room 201',
        date: new Date().toISOString().split('T')[0], // Today
        time: '10:00',
        category: 'class',
        priority: 'medium',
        createdAt: new Date()
      },
      {
        id: 2,
        title: 'Physics Assignment',
        description: 'Complete problems 1-15 from Chapter 8',
        date: this.getDateString(2),
        time: '23:59',
        category: 'assignment',
        priority: 'high',
        createdAt: new Date()
      },
      {
        id: 3,
        title: 'Study Group',
        description: 'Chemistry study session with Sarah and Mike',
        date: this.getDateString(1),
        time: '14:00',
        category: 'study',
        priority: 'medium',
        createdAt: new Date()
      }
    ];
  }

  getDateString(daysFromNow) {
    const date = new Date();
    date.setDate(date.getDate() + daysFromNow);
    return date.toISOString().split('T')[0];
  }

  saveReminders() {
    console.log('Reminders saved:', this.reminders);
  }

  openModal(reminder = null) {
    const modal = document.getElementById('reminderModal');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    
    if (reminder) {
      modalTitle.textContent = 'Edit Reminder';
      submitBtn.textContent = 'Update Reminder';
      this.currentEditId = reminder.id;
      this.fillForm(reminder);
    } else {
      modalTitle.textContent = 'Add New Reminder';
      submitBtn.textContent = 'Add Reminder';
      this.currentEditId = null;
      this.clearForm();
    }
    
    modal.style.display = 'block';
    document.getElementById('reminderTitle').focus();
  }

  closeModal() {
    document.getElementById('reminderModal').style.display = 'none';
    this.clearForm();
    this.currentEditId = null;
  }

  fillForm(reminder) {
    document.getElementById('reminderTitle').value = reminder.title;
    document.getElementById('reminderDescription').value = reminder.description || '';
    document.getElementById('reminderDate').value = reminder.date;
    document.getElementById('reminderTime').value = reminder.time || '';
    document.getElementById('reminderCategory').value = reminder.category;
    document.getElementById('reminderPriority').value = reminder.priority;
  }

  clearForm() {
    document.getElementById('reminderForm').reset();
    document.getElementById('reminderPriority').value = 'medium';
  }

  handleSubmit(e) {
    e.preventDefault();
    
    const formData = {
      title: document.getElementById('reminderTitle').value.trim(),
      description: document.getElementById('reminderDescription').value.trim(),
      date: document.getElementById('reminderDate').value,
      time: document.getElementById('reminderTime').value,
      category: document.getElementById('reminderCategory').value,
      priority: document.getElementById('reminderPriority').value
    };

    if (this.currentEditId) {
      this.updateReminder(this.currentEditId, formData);
    } else {
      this.addReminder(formData);
    }

    this.closeModal();
  }

  addReminder(data) {
    const newReminder = {
      id: Date.now(),
      ...data,
      createdAt: new Date()
    };

    this.reminders.unshift(newReminder);
    this.saveReminders();
    this.renderReminders();
    this.updateEmptyState();
    this.showNotification('Reminder added successfully!', 'success');
  }

  updateReminder(id, data) {
    const index = this.reminders.findIndex(r => r.id === id);
    if (index !== -1) {
      this.reminders[index] = { ...this.reminders[index], ...data };
      this.saveReminders();
      this.renderReminders();
      this.showNotification('Reminder updated successfully!', 'success');
    }
  }

  deleteReminder(id) {
    if (confirm('Are you sure you want to delete this reminder?')) {
      this.reminders = this.reminders.filter(r => r.id !== id);
      this.saveReminders();
      this.renderReminders();
      this.updateEmptyState();
      this.showNotification('Reminder deleted successfully!', 'success');
    }
  }

  getFilteredAndSortedReminders() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const sortBy = document.getElementById('sortBy').value;

    let filtered = this.reminders;

    if (categoryFilter !== 'all') {
      filtered = filtered.filter(r => r.category === categoryFilter);
    }

    filtered.sort((a, b) => {
      switch (sortBy) {
        case 'date':
          const dateA = new Date(a.date + ' ' + (a.time || '00:00'));
          const dateB = new Date(b.date + ' ' + (b.time || '00:00'));
          return dateA - dateB;
        case 'priority':
          const priorityOrder = { high: 3, medium: 2, low: 1 };
          return priorityOrder[b.priority] - priorityOrder[a.priority];
        case 'category':
          return a.category.localeCompare(b.category);
        default:
          return 0;
      }
    });

    return filtered;
  }

  renderReminders() {
    const container = document.getElementById('remindersContainer');
    const reminders = this.getFilteredAndSortedReminders();

    container.innerHTML = '';

    reminders.forEach(reminder => {
      const reminderElement = this.createReminderElement(reminder);
      container.appendChild(reminderElement);
    });

    this.updateEmptyState();
  }

  createReminderElement(reminder) {
    const div = document.createElement('div');
    div.className = `reminder-card priority-${reminder.priority} ${this.getReminderStatus(reminder)}`;
    
    const timeDisplay = reminder.time ? 
      new Date(`2000-01-01T${reminder.time}`).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 
      'All day';

    const dateDisplay = this.formatDate(reminder.date);
    const statusText = this.getStatusText(reminder);

    div.innerHTML = `
      <div class="reminder-header">
        <h3 class="reminder-title">${this.escapeHtml(reminder.title)}</h3>
        <div class="reminder-actions">
          <button class="btn btn-edit" onclick="reminderManager.openModal(${JSON.stringify(reminder).replace(/"/g, '&quot;')})">
            Edit
          </button>
          <button class="btn btn-danger" onclick="reminderManager.deleteReminder(${reminder.id})">
            Delete
          </button>
        </div>
      </div>
      <div class="reminder-meta">
        <div class="reminder-date">
          📅 ${dateDisplay} ${timeDisplay !== 'All day' ? `at ${timeDisplay}` : ''}
          <span class="status-${this.getReminderStatus(reminder)}">${statusText}</span>
        </div>
        <div class="reminder-category">
          <span class="category-badge ${reminder.category}">${this.capitalizeFirst(reminder.category)}</span>
        </div>
        <div class="reminder-priority">
          <span class="priority-badge ${reminder.priority}">${this.capitalizeFirst(reminder.priority)} Priority</span>
        </div>
      </div>
      ${reminder.description ? `<div class="reminder-description">${this.escapeHtml(reminder.description)}</div>` : ''}
    `;

    return div;
  }

  getReminderStatus(reminder) {
    const today = new Date();
    const reminderDate = new Date(reminder.date);
    
    today.setHours(0, 0, 0, 0);
    reminderDate.setHours(0, 0, 0, 0);

    if (reminderDate < today) {
      return 'overdue';
    } else if (reminderDate.getTime() === today.getTime()) {
      return 'today';
    } else {
      return 'upcoming';
    }
  }

  getStatusText(reminder) {
    const status = this.getReminderStatus(reminder);
    switch (status) {
      case 'overdue':
        return '(Overdue)';
      case 'today':
        return '(Today)';
      default:
        return '';
    }
  }

  formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (date.toDateString() === today.toDateString()) {
      return 'Today';
    } else if (date.toDateString() === tomorrow.toDateString()) {
      return 'Tomorrow';
    } else {
      return date.toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric'
      });
    }
  }

  updateEmptyState() {
    const container = document.getElementById('remindersContainer');
    const emptyState = document.getElementById('emptyState');
    const filtered = this.getFilteredAndSortedReminders();

    if (filtered.length === 0) {
      container.style.display = 'none';
      emptyState.style.display = 'block';
    } else {
      container.style.display = 'grid';
      emptyState.style.display = 'none';
    }
  }

  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      background-color: ${type === 'success' ? '#28a745' : '#007bff'};
      color: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      z-index: 1001;
      animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
      notification.style.transition = 'opacity 0.5s';
      notification.style.opacity = '0';
      setTimeout(() => notification.remove(), 500);
    }, 3000);
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  capitalizeFirst(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
  }
}

// Initialize the reminder manager
const reminderManager = new ReminderManager();
