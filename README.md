# 💬 DomAnon - Anonymous Chat Room  

DomAnon is a **PHP-based anonymous chat** that allows users to chat without logging in. It features a **random username generator, admin panel, banned user management, and API integration.**  

## 🌟 Features  

✔️ **No Login Required** – Instant chat access.  
✔️ **Random Usernames** – Each user gets a unique name.  
✔️ **Auto-Delete Messages** – Messages auto-delete after 24 hours.  
✔️ **Dark Mode UI** – A clean, user-friendly design.  
✔️ **Message Logging** – Stores messages for admin review.  
✔️ **Admin Panel (`admin.php`)** – Manage chat, ban users, and send messages.  
✔️ **API Support** – Fetch chat logs, banned users, and more.  

---

## 📌 Installation Guide  

### 1️⃣ Clone the Repository  
```bash
git clone https://github.com/domdevofficial/DomAnon.git
cd DomAnon
```  

### 2️⃣ Set Up the Files  
- No database needed! DomAnon uses `saves.txt` and `banned_ips.txt` for storage.  
- Ensure your server supports **PHP**.  

### 3️⃣ Run DomAnon  
- Place the files in your web server (`htdocs` or `/var/www/html/`).  
- Open `index.php` in your browser to start chatting!  

---

## 🗂️ Project Structure  

```
/DomAnon
│── index.php          # Main chat interface
│── admin.php          # Admin panel (popup-based)
│── saves.txt          # Stores chat messages
│── banned_ips.txt     # Stores banned IPs
│── api_token.txt      # Stores API token
│── README.md          # Documentation
```

---

## 🎯 How to Use  

1️⃣ Open `index.php` to start chatting.  
2️⃣ Messages appear in real-time and delete after 24 hours.  
3️⃣ Admins can manage the chat via `admin.php`.  

---

## 🔗 API Integration  

DomAnon includes an API system for retrieving messages and managing bans.  

### 🔹 Fetch Live Messages  
```bash
GET admin.php?api=YOUR_API_TOKEN&saves
```  

### 🔹 Fetch Full Logs  
```bash
GET admin.php?api=YOUR_API_TOKEN&logs
```  

### 🔹 Fetch Banned Users  
```bash
GET admin.php?api=YOUR_API_TOKEN&banned
```  

### 🔹 Ban a User  
```bash
POST admin.php?api=YOUR_API_TOKEN&ban_ip=USER_IP
```  

### 🔹 Unban a User  
```bash
POST admin.php?api=YOUR_API_TOKEN&unban_ip=USER_IP
```  

---

## ⚠️ Security Notes  

🔒 Keep `api_token.txt` **secure** to prevent unauthorized access.  
🛑 Do **not** expose `admin.php` publicly without protection.  

---

## 🏆 Credits  
Developed with 💙 by **DomDev**  

📌 *DomAnon is a secure and fun way to chat anonymously!*  
