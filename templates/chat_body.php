<?php
declare(strict_types=1);

function draw_chat_body()
{
  ?>
  <main>
    <h2>Chat</h2>
    <div id="chat-container">
      <div id="chat-header">
        <!-- Other user's information will be dynamically loaded here -->
      </div>
      <div id="chat-messages">
        <!-- Messages will be dynamically loaded here -->
      </div>
      <form id="chat-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
        <input name="message_content" id="form-content" placeholder="Type your message here..." required></input>
        <button id="form-submit-button">Send</button>
      </form>
    </div>
  </main>
  <?php
}
?>
