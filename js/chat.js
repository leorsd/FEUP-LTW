document.addEventListener('DOMContentLoaded', () => {
    const chatHeader = document.getElementById('chat-header');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = chatForm.querySelector('input[name="message_content"]');
    const sendButton = document.getElementById('form-submit-button');

    const params = new URLSearchParams(window.location.search);
    const otherUserId = params.get('user');

    let msg_after_id = 0;
    let firstLoad = true;
    let lastDay = null;

    function scrollChatToBottom(instant = false) {
        if (chatMessages) {
            if (instant) {
                chatMessages.style.scrollBehavior = 'auto';
            } else {
                chatMessages.style.scrollBehavior = 'smooth';
            }
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    const loadChat = () => {
        fetch(`../api/chat_get.php?user1=${CURRENT_USER_ID}&user2=${otherUserId}&msg_after_id=${msg_after_id}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(message => {
                    const messageDate = new Date(message.created_at);

                    // Normalize day for comparison (YYYY-MM-DD)
                    const compareDay = messageDate.getFullYear() + '-' +
                        String(messageDate.getMonth() + 1).padStart(2, '0') + '-' +
                        String(messageDate.getDate()).padStart(2, '0');

                    // Format day for display
                    const dayString = messageDate.toLocaleDateString('en-GB', {
                        year: 'numeric',
                        month: 'long',
                        day: '2-digit',
                    });

                    // If the day has changed, insert a day separator
                    if (lastDay !== compareDay) {
                        lastDay = compareDay;
                        const daySeparator = document.createElement('div');
                        daySeparator.className = 'chat-day-separator';
                        daySeparator.textContent = dayString;
                        chatMessages.appendChild(daySeparator);
                    }

                    const messageElement = document.createElement('div');
                    messageElement.className = 'message';
                    if (message.sender_id === CURRENT_USER_ID) {
                        messageElement.classList.add('sent');
                    } else {
                        messageElement.classList.add('received');
                    }
                    messageElement.innerHTML = `
                        <span class="message-text">${message.content}</span>
                        <span class="message-time">${
                            messageDate.toLocaleTimeString('pt-PT', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                timeZone: 'Europe/Lisbon'
                            })
                        }</span>
                    `;
                    chatMessages.appendChild(messageElement);
                    msg_after_id = message.id;
                });
                scrollChatToBottom(firstLoad);
                firstLoad = false;
            })
            .catch(error => {
                console.error('Error loading chat:', error);
            });
    };

    const loadHeader = () => {
        fetch(`../api/user.php?id=${otherUserId}`)
            .then(response => response.json())
            .then(data => {

                if (!data.profile_picture) {
                    data.profile_picture = "../images/user.jpg";
                } else {
                    data.profile_picture = `../images/cache/${data.profile_picture}`;
                }

                chatHeader.innerHTML = `
                    <img src="${data.profile_picture}" alt="${data.username}'s avatar" class="avatar">
                    <span class="user-name">${data.username}</span>
                `;
            })
            .catch(error => {
                console.error('Error loading user header:', error);
            });
    }

    sendButton.addEventListener('click', (event) => {
        event.preventDefault();
        const messageText = messageInput.value.trim();
        if (messageText) {
            fetch("../api/chat_post.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                sender_id: CURRENT_USER_ID,
                receiver_id: otherUserId,
                content: messageText,
                csrf_token: typeof CSRF_TOKEN !== "undefined" ? CSRF_TOKEN : "",
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                messageInput.value = "";
                loadChat();
              })
              .catch((error) => {
                console.error("Error sending message:", error);
              });
        }
    });

    loadHeader();
    loadChat();
    scrollChatToBottom();
    setInterval(loadChat, 5000); // Refresh chat every 5 seconds
});
