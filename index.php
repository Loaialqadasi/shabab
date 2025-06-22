<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shabab Lost & Found Assistant</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Accessibility and chatbox improvements */
    #ai-chatbox-container:focus-within {
      outline: 2px solid #007bff;
    }
    #ai-chatbox-messages {
      min-height: 80px;
      max-height: 250px;
      overflow-y: auto;
      padding: 8px;
      background: #f9f9f9;
      border-radius: 6px;
      margin-bottom: 8px;
    }
    .ai-chatbox-msg.user { color: #333; background: #e3f2fd; border-radius: 6px; margin-bottom: 4px; padding: 4px 8px; }
    .ai-chatbox-msg.ai { color: #222; background: #fff3e0; border-radius: 6px; margin-bottom: 4px; padding: 4px 8px; }
    .ai-chatbox-msg.error { color: #fff; background: #e57373; border-radius: 6px; margin-bottom: 4px; padding: 4px 8px; }
    #ai-chatbox-form button[disabled] { opacity: 0.6; cursor: not-allowed; }
    #ai-chatbox-loading { display: none; font-size: 0.95em; color: #888; margin-bottom: 4px; }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
  <h2>Welcome to Shabab Lost & Found Assistant</h2>
  <p class="intro">
    Helping UTM students and staff report and find lost items with ease. Use the buttons above to report or search items.
  </p>

<?php
include 'db.php';

// Fetch all lost items (you can limit if needed)
$lostQuery = "SELECT * FROM lost_items ORDER BY date_lost DESC";
$lostResult = pg_query($conn, $lostQuery);

// Fetch all found items
$foundQuery = "SELECT * FROM found_items ORDER BY date_found DESC";
$foundResult = pg_query($conn, $foundQuery);
?>

<div id="recent-reports">

  <?php if ($lostResult && pg_num_rows($lostResult) > 0): ?>
    <?php while ($row = pg_fetch_assoc($lostResult)): ?>
      <div class="report-card red-box">
        <div class="item-label lost-label" aria-label="Lost item">🆘 LOST</div>
        <h3><?= htmlspecialchars($row['item_name']) ?></h3>
        <?php if (!empty($row['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Image of <?= htmlspecialchars($row['item_name']) ?>" style="width:100%; max-height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px;">
        <?php endif; ?>
        <p><strong>Date:</strong> <?= htmlspecialchars($row['date_lost']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
        <?php if (isset($_SESSION['username']) && isset($row['username']) && $_SESSION['username'] === $row['username']): ?>
          <a href="edit-lost.php?id=<?= $row['id'] ?>" class="header-link" style="margin-top:10px; display:inline-block; margin-right:10px;">Edit</a>
          <form method="POST" action="delete-report.php" onsubmit="return confirm('Are you sure you want to delete this report?');" class="inline-form" style="display:inline;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="type" value="lost">
            <button type="submit" class="delete-button" style="background-color:#ff5722; color:white; border:none; padding:5px 10px; cursor:pointer; border-radius:4px;">Delete</button>
        </form>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>

  <?php if ($foundResult && pg_num_rows($foundResult) > 0): ?>
    <?php while ($row = pg_fetch_assoc($foundResult)): ?>
      <div class="report-card orange-box">
        <div class="item-label found-label" aria-label="Found item">🔍 FOUND</div>
        <h3><?= htmlspecialchars($row['item_name']) ?></h3>
        <?php if (!empty($row['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Image of <?= htmlspecialchars($row['item_name']) ?>" class="report-image">
        <?php endif; ?>
        <p><strong>Date:</strong> <?= htmlspecialchars($row['date_found']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
        <p><?= htmlspecialchars($row['description']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
        <?php if (isset($_SESSION['username']) && isset($row['username']) && $_SESSION['username'] === $row['username']): ?>
          <a href="edit-found.php?id=<?= $row['id'] ?>" class="header-link" style="margin-top:10px; display:inline-block; margin-right:10px;">Edit</a>
          <form method="POST" action="delete-report.php" onsubmit="return confirm('Are you sure you want to delete this report?');" class="inline-form" style="display:inline;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="hidden" name="type" value="found">
            <button type="submit" class="delete-button">Delete</button>
        </form>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>

</div>
<!-- AI Chat Icon Button -->
<button id="ai-chatbox-icon" title="Chat with Shabab AI" aria-label="Open chat with Shabab AI">💬</button>
<!-- AI Chat Box -->
<div id="ai-chatbox-container" style="display:none; flex-direction:column; position:fixed; bottom:30px; right:30px; z-index:9999; background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,0.18); width:340px; max-width:95vw; min-height:220px;">
  <div id="ai-chatbox-header" style="display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#1976d2; color:#fff; border-radius:12px 12px 0 0; font-weight:bold;">
    Ask Shabab AI
    <button id="ai-chatbox-close" title="Close" aria-label="Close chat" style="background:none; border:none; color:#fff; font-size:1.2em; cursor:pointer;">❌</button>
  </div>
  <div id="ai-chatbox-messages" aria-live="polite" aria-atomic="false"></div>
  <div id="ai-chatbox-loading">AI is typing...</div>
  <form id="ai-chatbox-form" autocomplete="off" style="display:flex; gap:6px; padding:10px; border-top:1px solid #eee;">
    <input type="text" id="ai-chatbox-input" placeholder="Type your question..." autocomplete="off" required style="flex:1; padding:7px 10px; border-radius:6px; border:1px solid #ccc;" aria-label="Type your question to Shabab AI" />
    <button type="submit" style="padding:7px 16px; border-radius:6px; background:#1976d2; color:#fff; border:none; font-weight:bold; cursor:pointer;">Send</button>
  </form>
</div>
<script>
// Chatbox open/close logic
const chatboxIcon = document.getElementById('ai-chatbox-icon');
const chatboxContainer = document.getElementById('ai-chatbox-container');
const chatboxClose = document.getElementById('ai-chatbox-close');
const chatboxForm = document.getElementById('ai-chatbox-form');
const chatboxInput = document.getElementById('ai-chatbox-input');
const chatboxMessages = document.getElementById('ai-chatbox-messages');
const chatboxLoading = document.getElementById('ai-chatbox-loading');

chatboxIcon.addEventListener('click', () => {
  chatboxContainer.style.display = 'flex';
  chatboxIcon.style.display = 'none';
  setTimeout(() => { chatboxInput.focus(); }, 200);
});
chatboxClose.addEventListener('click', () => {
  chatboxContainer.style.display = 'none';
  chatboxIcon.style.display = 'block';
});

// Chatbox logic
chatboxForm.addEventListener('submit', async function(e) {
  e.preventDefault();
  const userMsg = chatboxInput.value.trim();
  if (!userMsg) return;
  appendMessage('You', userMsg, 'user');
  chatboxInput.value = '';
  chatboxInput.disabled = true;
  chatboxForm.querySelector('button[type="submit"]').disabled = true;
  chatboxLoading.style.display = 'block';
  try {
    const res = await fetch('chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: userMsg })
    });
    if (!res.ok) throw new Error('Network response was not ok');
    const data = await res.json();
    appendMessage('AI', data.reply || 'Sorry, I could not process your request.', 'ai');
  } catch (err) {
    appendMessage('AI', 'Error connecting to AI.', 'error');
  }
  chatboxInput.disabled = false;
  chatboxForm.querySelector('button[type="submit"]').disabled = false;
  chatboxLoading.style.display = 'none';
  chatboxInput.focus();
});

function appendMessage(sender, text, type) {
  const msgDiv = document.createElement('div');
  msgDiv.className = 'ai-chatbox-msg ' + type;
  msgDiv.innerHTML = `<strong>${sender}:</strong> ${text}`;
  chatboxMessages.appendChild(msgDiv);
  chatboxMessages.scrollTop = chatboxMessages.scrollHeight;
}
</script>

</main>

<?php include 'footer.php'; ?>

</body>
</html>