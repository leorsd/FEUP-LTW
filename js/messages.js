document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch('/api/chats.php?user_id=' + CURRENT_USER_ID);
    if (!response.ok) throw new Error('Failed to fetch chats');
    const chats = await response.json();

    const chatsList = document.getElementById('chats-list');
    chatsList.innerHTML = '';

    if (chats.length === 0) {
      const empty = document.createElement('div');
      empty.className = 'no-chats';
      empty.textContent = 'No messages yet.';
      chatsList.appendChild(empty);
      return;
    }

    chats.forEach(chat => {
      const li = document.createElement('li');
      li.className = 'chat-item';

      let time = '';
      if (chat.last_message_time) {
        const msgDate = new Date(chat.last_message_time);
        const now = new Date();
        const isSameDay =
          msgDate.getFullYear() === now.getFullYear() &&
          msgDate.getMonth() === now.getMonth() &&
          msgDate.getDate() === now.getDate();

        if (isSameDay) {
          time = msgDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        } else {
          time = msgDate.toLocaleDateString([], { year: '2-digit', month: '2-digit', day: '2-digit' });
        }
      }

      li.innerHTML = `
        <div class="chat-main">
          <div class="chat-username">${chat.other_username}</div>
          <div class="chat-last-message">${chat.last_message || '<em>No messages yet</em>'}</div>
        </div>
        <div class="chat-last-time">${time}</div>
      `;
      li.addEventListener('click', () => {
        window.location.href = `/pages/chat.php?user=${chat.other_user_id}`;
      });
      chatsList.appendChild(li);
    });
  } catch (error) {
    console.error(error);
    const chatsList = document.getElementById('chats-list');
    chatsList.innerHTML = '<div class="chat-error">Could not load chats.</div>';
  }
});