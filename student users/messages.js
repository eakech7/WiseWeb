// Contacts from backend
let contacts = [];

// Load contacts into the sidebar
function loadContacts() {
  const contactsList = document.getElementById('contactsList');
  contactsList.innerHTML = '';

  if (!contacts || contacts.length === 0) {
    contactsList.innerHTML = `<p style="color:white; text-align:center; padding:20px;">No contacts available</p>`;
    return;
  }

  contacts.forEach(contact => {
    const div = document.createElement('div');
    div.className = 'contact-item';
    div.innerHTML = `
      <div class="contact-avatar">${contact.initials || ''}</div>
      <div class="contact-details">
        <strong>${contact.name}</strong>
      </div>
    `;
    div.addEventListener('click', () => selectContact(contact));
    contactsList.appendChild(div);
  });
}

// Select a contact and update chat area
function selectContact(contact) {
  document.getElementById('chatAvatar').textContent = contact.initials || '';
  document.getElementById('chatUserName').textContent = contact.name || '';
  document.getElementById('messagesContainer').innerHTML = ''; // Clear old messages
}

// Filter contacts on search input
document.getElementById('contactSearch').addEventListener('input', function () {
  const query = this.value.toLowerCase();
  const filtered = contacts.filter(c => c.name.toLowerCase().includes(query));
  displayFilteredContacts(filtered);
});

function displayFilteredContacts(filteredContacts) {
  const contactsList = document.getElementById('contactsList');
  contactsList.innerHTML = '';

  if (!filteredContacts || filteredContacts.length === 0) {
    contactsList.innerHTML = `<p style="color:white; text-align:center; padding:20px;">No contacts found</p>`;
    return;
  }

  filteredContacts.forEach(contact => {
    const div = document.createElement('div');
    div.className = 'contact-item';
    div.innerHTML = `
      <div class="contact-avatar">${contact.initials || ''}</div>
      <div class="contact-details">
        <strong>${contact.name}</strong>
      </div>
    `;
    div.addEventListener('click', () => selectContact(contact));
    contactsList.appendChild(div);
  });
}

// Handle sending a message
document.getElementById('messageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const input = document.getElementById('messageInput');
  const message = input.value.trim();
  if (message !== '') {
    appendMessage('You', message, true);
    input.value = '';
  }
});

// Add a new message bubble
function appendMessage(sender, text, isSender) {
  const container = document.getElementById('messagesContainer');
  const msgDiv = document.createElement('div');
  msgDiv.className = 'message-bubble' + (isSender ? ' sender' : '');
  msgDiv.innerHTML = `<strong>${sender}:</strong><p>${text}</p>`;
  container.appendChild(msgDiv);
  container.scrollTop = container.scrollHeight;
}

// Initial load (now expects backend call)
document.addEventListener('DOMContentLoaded', () => {
  
});
