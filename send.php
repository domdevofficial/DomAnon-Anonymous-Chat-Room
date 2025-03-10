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

$username = $_SESSION['username'];
$ip = $_SERVER['REMOTE_ADDR'];
$message = trim($_POST['message'] ?? '');

// Load banned IPs
$banned_ips = file_exists("banned.txt") ? file("banned.txt", FILE_IGNORE_NEW_LINES) : [];
if (in_array($ip, $banned_ips)) {
    die("You are banned from DomAnon.");
}

// Message filtering
$bad_words = ["badword1", "badword2", "badword3"];
$message = str_ireplace($bad_words, "***", $message);

// Load emoji replacements
$emoji_replacements = [];
if (file_exists("emoji_replacements.json")) {
    $emoji_replacements = json_decode(file_get_contents("emoji_replacements.json"), true);
}

// Apply emoji replacements in text
foreach ($emoji_replacements as $word => $emoji) {
    if (stripos($message, $word) !== false) {
        $message .= " " . $emoji;
    }
}

if (!empty($message)) {
    $log = "[" . date("Y-m-d H:i:s") . "] $username: $message\n";

    // Save to chat log
    file_put_contents("saves.txt", $log, FILE_APPEND | LOCK_EX);

    // Hidden logging
    file_put_contents("logs.txt", "[IP: $ip] $log", FILE_APPEND | LOCK_EX);
}

header("Location: index.php");
exit;
?>