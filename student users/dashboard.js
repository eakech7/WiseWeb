// Navigation functionality
function showSection(sectionName) {
  document.querySelectorAll('.page-section').forEach(section => {
    section.classList.remove('active');
  });

  const section = document.getElementById(sectionName);
  if (section) section.classList.add('active');

  document.querySelectorAll('.nav-links a').forEach(link => {
    link.classList.remove('active');
  });

  const activeLink = document.querySelector(`.nav-links a[data-section="${sectionName}"]`);
  if (activeLink) activeLink.classList.add('active');
}

// Mobile menu toggle
function toggleMobileMenu() {
  const navLinks = document.querySelector('.nav-links');
  if (navLinks) navLinks.classList.toggle('active');
}

// Logout function
function logout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = "login.html";
  }
}

// Safe form handler
function bindForm(id, callback) {
  const form = document.getElementById(id);
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      callback(this);
    });
  }
}

// Attach form handlers (only if forms exist on page)
bindForm('scheduleForm', form => {
  alert('Session scheduled successfully!');
  form.reset();
});

bindForm('matchForm', () => {
  alert('Searching for matches... New suggestions will appear shortly!');
});

bindForm('messageForm', form => {
  alert('Message sent successfully!');
  form.reset();
});

bindForm('reminderForm', form => {
  alert('Reminder set successfully!');
  form.reset();
});

// Navigation event listeners
document.querySelectorAll('.nav-links a[data-section]').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const section = this.getAttribute('data-section');
    showSection(section);
  });
});

// Connect buttons functionality
document.querySelectorAll('.match-card .btn').forEach(button => {
  button.addEventListener('click', function() {
    this.textContent = 'Connected!';
    this.style.background = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
    setTimeout(() => {
      this.textContent = 'Message';
    }, 1500);
  });
});

// Reminder completion
document.querySelectorAll('#reminderList .btn').forEach(button => {
  button.addEventListener('click', function() {
    const listItem = this.closest('li');
    if (listItem) {
      listItem.style.opacity = '0.5';
      listItem.style.textDecoration = 'line-through';
    }
    this.textContent = '✓ Done';
    this.style.background = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
  });
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
  const navLinks = document.querySelector('.nav-links');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (navLinks && mobileMenu && !navLinks.contains(e.target) && !mobileMenu.contains(e.target)) {
    navLinks.classList.remove('active');
  }
});
