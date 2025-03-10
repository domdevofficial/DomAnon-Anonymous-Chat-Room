<?php
/**
 * Copyright 2025 DomDev
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<?php  
session_start();

// Admin credentials
$password = "admin123"; // Change this to your admin password

// Load API Token
$api_token_file = "api_token.txt";
if (file_exists($api_token_file)) {
    $api_token = trim(file_get_contents($api_token_file));
} else {
    $api_token = bin2hex(random_bytes(16)); 
    file_put_contents($api_token_file, $api_token);
}

// Load Banned IPs
$banned_ips_file = "banned_ips.txt";
$banned_ips = file_exists($banned_ips_file) ? file($banned_ips_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
// Unban User by IP
if (isset($_POST['unban_user'])) {
    $unban_ip = trim(htmlspecialchars($_POST['unban_ip']));

    if (file_exists("banned_ips.txt")) {
        $banned_ips = file("banned_ips.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $updated_ips = array_diff($banned_ips, [$unban_ip]); // Remove the selected IP
        file_put_contents("banned_ips.txt", implode("\n", $updated_ips) . "\n"); // Save the new list
    }
}
// Handle Admin Login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['admin'] = true;
    } else {
        echo "<script>alert('Incorrect password!');</script>";
    }
}

// Check Admin Access
if (!isset($_SESSION['admin']) && (!isset($_GET['api']) || $_GET['api'] !== $api_token)) { ?>
<form method="POST">
    <input type="password" name="password" placeholder="Enter admin password" required>
    <button type="submit" name="login">Login</button>
</form>
<?php 
    exit;
}

// Generate a new API token
if (isset($_POST['generate_api_token'])) {
    $new_api_token = bin2hex(random_bytes(16));
    file_put_contents($api_token_file, $new_api_token);
    $api_token = $new_api_token;
}

// Send Message
if (isset($_POST['send_message'])) {
    $message = htmlspecialchars($_POST['message']);
    $message = "[Admin]: <span style='color:yellow;'>$message</span>";
    file_put_contents("saves.txt", $message . "\n", FILE_APPEND);
}

// Ban User by IP
if (isset($_POST['ban_user'])) {
    $banned_ip = htmlspecialchars($_POST['ban_ip']);
    if (!in_array($banned_ip, $banned_ips)) {
        file_put_contents($banned_ips_file, $banned_ip . "\n", FILE_APPEND);
        $banned_ips[] = $banned_ip;
    }
}

// Edit Messages
if (isset($_POST['edit_message'])) {
    $old_msg = trim(htmlspecialchars($_POST['old_message']));
    $new_msg = trim(htmlspecialchars($_POST['new_message']));
    $data = file("saves.txt");

    $updated_data = [];
    foreach ($data as $line) {
        if (strpos($line, $old_msg) !== false) {
            $updated_data[] = str_replace($old_msg, $new_msg, $line);
        } else {
            $updated_data[] = $line;
        }
    }
    file_put_contents("saves.txt", implode("", $updated_data));
}

// Delete All Messages
if (isset($_POST['delete_all_messages'])) {
    file_put_contents("saves.txt", ""); // Clears chat messages only
}

// API: Get Live Messages
if (isset($_GET['api']) && $_GET['api'] === $api_token && isset($_GET['saves'])) {
    echo file_exists("saves.txt") ? nl2br(file_get_contents("saves.txt")) : "No messages yet.";
    exit;
}

// API: Get Full Logs
if (isset($_GET['api']) && $_GET['api'] === $api_token && isset($_GET['logs'])) {
    echo file_exists("logs.txt") ? nl2br(file_get_contents("logs.txt")) : "No logs available.";
    exit;
}

// API: Get Banned Users
if (isset($_GET['api']) && $_GET['api'] === $api_token && isset($_GET['banned'])) {
    echo file_exists($banned_ips_file) ? nl2br(file_get_contents($banned_ips_file)) : "No banned users.";
    exit;
}
// API: Unban User
if (isset($_GET['api']) && $_GET['api'] === $api_token && isset($_GET['unban'])) {
    $unban_ip = trim($_GET['unban']);
    if (!empty($unban_ip)) {
        $banned_ips = file_exists($banned_ips_file) ? file($banned_ips_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $new_banned_ips = array_filter($banned_ips, fn($ip) => $ip !== $unban_ip);
        file_put_contents($banned_ips_file, implode("\n", $new_banned_ips) . "\n");
        echo "Unbanned: $unban_ip";
    } else {
        echo "Error: No IP provided.";
    }
    exit;
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomAnon Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #222; color: white; }
        .admin-container { max-width: 600px; margin: auto; padding: 20px; background: #333; border-radius: 10px; }
        .admin-button { padding: 12px; background: #ff69b4; color: white; border: none; cursor: pointer; border-radius: 5px; width: 100%; }
        .admin-button:hover { background: #ff1493; }
        input, button { width: 100%; padding: 10px; margin: 10px 0; font-size: 16px; }
        .log-box { text-align: left; background: #111; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto; }
        .danger-section { background: #800000; padding: 10px; margin-top: 20px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="admin-container">
    <h2>Admin Panel</h2>

    <h3>📢 Send Message</h3>
    <form method="POST">
        <input type="text" name="message" placeholder="Send a message as Admin" required>
        <button type="submit" name="send_message" class="admin-button">Send</button>
    </form>

    <h3>📡 Message Feed (Live Updates)</h3>
    <div class="log-box" id="messageFeed">Loading...</div>

    <h3>📜 Logs</h3>
    <div class="log-box" id="logs">Loading...</div>

    <h3>✏️ Edit Message</h3>
    <form method="POST">
        <input type="text" name="old_message" placeholder="Old Message" required>
        <input type="text" name="new_message" placeholder="New Message" required>
        <button type="submit" name="edit_message" class="admin-button">Edit</button>
    </form>

    <h3>🔨 Ban User</h3>
    <form method="POST">
        <input type="text" name="ban_ip" placeholder="Enter IP to ban" required>
        <button type="submit" name="ban_user" class="admin-button">Ban User</button>
    </form>

<h3>🚫 Banned Users</h3>
<div class="log-box">
    <?php 
    if (empty($banned_ips)) {
        echo "No banned users";
    } else {
        foreach ($banned_ips as $banned_ip) {
            echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='unban_ip' value='$banned_ip'>
                $banned_ip <button type='submit' name='unban_user' style='color:red;'>[Delete]</button>
            </form><br>";
        }
    }
    ?>
</div>
<script>
function editBan(ip) {
    let banElement = document.getElementById("ban_" + ip);
    let banText = banElement.querySelector(".ban-text").innerText;

    // Replace with an input field
    banElement.innerHTML = `
        <input type="text" id="edit_ip_${ip}" value="${banText}">
        <button onclick="saveBan('${ip}')">Save</button>
        <button onclick="location.reload()">Cancel</button>
    `;
}

function saveBan(oldIp) {
    let newIp = document.getElementById("edit_ip_" + oldIp).value;

    let form = new FormData();
    form.append("old_ip", oldIp);
    form.append("new_ip", newIp);
    form.append("save_edit", "1");

    fetch("admin.php", { method: "POST", body: form })
        .then(() => location.reload()); // Refresh after saving
}

function deleteBan(ip) {
    let form = new FormData();
    form.append("delete_ip", ip);

    fetch("admin.php", { method: "POST", body: form })
        .then(() => location.reload()); // Refresh after deleting
}
</script>
    </div>

    <div class="danger-section">
        <h3>⚠ Danger Zone</h3>
        <form method="POST">
            <button type="submit" name="generate_api_token" class="admin-button">Generate New API Token</button>
        </form>
        <p><strong>API Token:</strong> <?= $api_token ?></p>
        <form method="POST">
            <button type="submit" name="delete_all_messages" class="admin-button">Delete All Messages</button>
        </form>
    </div>

    <form method="POST">
        <button type="submit" name="logout" class="admin-button">Logout</button>
    </form>
</div>

<script>
function updateFeed() {
    fetch("admin.php?api=<?= $api_token ?>&saves")
        .then(response => response.text())
        .then(data => document.getElementById("messageFeed").innerHTML = data);
    
    fetch("admin.php?api=<?= $api_token ?>&logs")
        .then(response => response.text())
        .then(data => document.getElementById("logs").innerHTML = data);
}

setInterval(updateFeed, 5000);
updateFeed();
</script>
</body>
</html>