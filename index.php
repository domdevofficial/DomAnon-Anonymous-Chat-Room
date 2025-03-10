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
// Get user IP
$user_ip = $_SERVER['REMOTE_ADDR'];

// Check if IP is banned
$banned_ips_file = "banned_ips.txt";
$banned_ips = file_exists($banned_ips_file) ? file($banned_ips_file, FILE_IGNORE_NEW_LINES) : [];

if (in_array($user_ip, $banned_ips)) {
    die("<h2 style='color:red;text-align:center;'>You have been banned from this chat.</h2>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomAnon - Anonymous Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #228B22;
            color: white;
            transition: background 0.5s ease;
            overflow: hidden;
        }
        #chat {
            width: 90%;
            max-width: 500px;
            height: 400px;
            overflow-y: auto;
            background: #333;
            padding: 10px;
            margin: auto;
            border-radius: 10px;
        }
        .message {
            padding: 8px;
            margin: 5px 0;
            border-radius: 8px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .sent { background: #007BFF; color: white; text-align: right; border: 2px solid blue; }
        .received { background: #555; color: white; text-align: left; border: 2px solid black; }
        .admin { background: #FFD700; color: black; text-align: center; border: 2px solid gold; font-weight: bold; }
        .input-container { display: flex; justify-content: center; margin-top: 10px; }
        #message { flex: 1; padding: 12px; font-size: 16px; border: 2px solid #fff; border-radius: 5px 0 0 5px; background: #444; color: white; outline: none; }
        button { padding: 12px; font-size: 16px; background: #ff4081; color: white; border: none; cursor: pointer; border-radius: 0 5px 5px 0; }
        button:hover { background: #e91e63; }
        .emoji { position: fixed; font-size: 20px; pointer-events: none; animation: fall 3s linear forwards; }
        @keyframes fall { 0% { transform: translateY(-50px); opacity: 1; } 100% { transform: translateY(100vh); opacity: 0; } }
        .color-selector { margin-top: 10px; }
        .color-button { padding: 10px; margin: 5px; border: none; cursor: pointer; }
        .pink { background: pink; color: black; }
        .green { background: #228B22; color: white; }
        .black { background: black; color: white; }
        footer { margin-top: 20px; padding: 10px; background: rgba(0, 0, 0, 0.2); font-size: 14px; }
    </style>
</head>
<body>

    <h2>Welcome to DomAnon!</h2>
    
    <div id="chat"></div>

    <div class="input-container">
        <form id="chatForm" method="POST" action="send.php" style="display: flex; width: 90%; max-width: 500px;">
            <input type="text" id="message" name="message" placeholder="Type a message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <div class="color-selector">
        <button class="color-button pink" onclick="changeColor('pink')">Pink</button>
        <button class="color-button green" onclick="changeColor('green')">Green</button>
        <button class="color-button black" onclick="changeColor('black')">Black</button>
    </div>

    <footer>Powered by: <strong>DomDev</strong></footer>

    <script>
        function fetchMessages() {
            fetch('load.php')
                .then(response => response.text())
                .then(data => { document.getElementById("chat").innerHTML = data; });
        }
        setInterval(fetchMessages, 5000);
        fetchMessages();

        document.addEventListener("DOMContentLoaded", function() {
            let savedColor = localStorage.getItem("chatColor");
            if (savedColor) { document.body.style.background = savedColor; }
        });

        function changeColor(color) {
            if (color === "pink") { document.body.style.background = "pink"; }
            else if (color === "green") { document.body.style.background = "#228B22"; }
            else if (color === "black") { document.body.style.background = "black"; }
            localStorage.setItem("chatColor", document.body.style.background);
        }

        document.getElementById("chatForm").addEventListener("submit", function(event) {
            let message = document.getElementById("message").value.toLowerCase();
            fetch("emoji_replacements.json")
                .then(response => response.json())
                .then(data => {
                    for (let keyword in data) {
                        if (message.includes(keyword)) {
                            rainEmojis(data[keyword]); 
                        }
                    }
                });
        });

        function rainEmojis(emoji) {
            for (let i = 0; i < 20; i++) {
                let e = document.createElement("div");
                e.innerText = emoji;
                e.className = "emoji";
                e.style.left = Math.random() * 100 + "vw";
                e.style.animationDuration = (Math.random() * 1.5 + 1) + "s";
                document.body.appendChild(e);
                setTimeout(() => e.remove(), 3000);
            }
        }
    </script>

</body>
</html>
