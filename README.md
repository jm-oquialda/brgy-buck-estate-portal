# 🏛 Barangay Buck Estate – Online Portal

A modern web portal for **Barangay Buck Estate, Alfonso, Cavite** built with **PHP + MySQL**.

---

## 📋 Features

| Module | Residents | Admin |
|--------|-----------|-------|
| Register / Login | ✅ | ✅ |
| Announcements | View | Post / Edit / Delete |
| Document Requests | Submit / Track | Approve / Deny / Remarks |
| Blotter Reports | File / Track | Review / Assign Case No. |
| Financial Assistance | Apply / Track | Approve / Deny |
| Profile | Edit info / Change password | — |
| Resident Management | — | View / Activate / Deactivate |

---

## 🛠 Tech Stack

- **Backend:** PHP 8.1+
- **Database:** MySQL (InfinityFree hosting)
- **Frontend:** Vanilla CSS + JS (no frameworks)
- **Fonts:** Google Fonts – Poppins + Inter
- **Deployment:** GitHub Actions → FTP → InfinityFree

---

## ⚡ Setup Instructions

### 1. Create MySQL Database on InfinityFree

1. Log into your InfinityFree account
2. Go to **MySQL Databases** in the control panel
3. Create a new database (note down the credentials)
4. In **phpMyAdmin**, import the `sql/schema.sql` file
   - This creates all tables and seeds the default admin account + sample data

### 2. Configure Database Connection

Open `config/config.php` and fill in your InfinityFree MySQL credentials:

```php
define('DB_HOST', 'sql101.infinityfree.com');  // Your MySQL host
define('DB_NAME', 'if0_XXXXXXXX_buckestate');  // Your database name
define('DB_USER', 'if0_XXXXXXXX');             // Your database username
define('DB_PASS', 'your_password_here');       // Your database password
```

You can find these credentials in **InfinityFree Control Panel → MySQL Databases**.

### 3. Deploy to InfinityFree

#### Option A: GitHub Actions (Recommended - Automatic Deployment)

1. **Set up GitHub Secrets** in your repository settings:
   - `FTP_HOSTNAME` - Your InfinityFree FTP hostname
   - `FTP_USERNAME` - Your FTP username
   - `FTP_PASSWORD` - Your FTP password
   - `FTP_PORT` - FTP port (usually `21`)

2. **Push to main branch** - GitHub Actions will automatically deploy via FTP!

The workflow file (`.github/workflows/deploy.yml`) is already configured.

#### Option B: Manual FTP Upload

1. Use an FTP client (FileZilla, WinSCP, etc.)
2. Connect to your InfinityFree FTP
3. Upload all files to `/htdocs/` directory
4. Done!

### 4. Default Admin Login

| Field | Value |
|-------|-------|
| Email | `admin@buckestate.gov.ph` |
| Password | `password` |

> 🔴 **Change this immediately after first login** via the Admin panel or directly in the database.

---

## 📁 File Structure

```
buck-estate-portal/
├── index.php                    ← Home page
├── about.php                    ← About the barangay
├── announcements.php            ← All announcements
├── announcement.php             ← Single announcement view
├── contact.php                  ← Contact form + Emergency hotlines
│
├── auth/
│   ├── login.php                ← Login
│   ├── register.php             ← Registration
│   └── logout.php               ← Logout
│
├── resident/
│   ├── dashboard.php            ← Resident dashboard (3-tab)
│   ├── document-request-new.php ← Submit document request
│   ├── document-request.php     ← View single request
│   ├── blotter-new.php          ← File blotter report
│   ├── blotter.php              ← View single blotter
│   ├── financial-new.php        ← Apply for financial assistance
│   ├── financial.php            ← View single application
│   └── profile.php              ← Edit profile + change password
│
├── admin/
│   ├── dashboard.php            ← Admin dashboard with stats
│   ├── announcements.php        ← Manage announcements
│   ├── document-requests.php    ← Manage document requests
│   ├── blotter-reports.php      ← Manage blotter reports
│   ├── financial.php            ← Manage financial assistance
│   └── residents.php            ← Manage registered residents
│
├── includes/
│   ├── header.php               ← Navigation + HTML head
│   ├── footer.php               ← Footer + scripts
│   ├── db.php                   ← PDO database connection
│   └── functions.php            ← Auth, flash, CSRF, helpers
│
├── config/
│   └── config.php               ← ⚙ Site + DB configuration
│
├── assets/
│   ├── css/style.css            ← Main stylesheet
│   ├── js/main.js               ← Navigation, tabs, confirm dialogs
│   └── img/                     ← Images and logos
│
├── sql/
│   └── schema.sql               ← Run this in InfinityFree phpMyAdmin
│
└── .github/workflows/
    └── deploy.yml               ← GitHub Actions FTP deployment
```

---

## 🎨 Branding

| Token | Value |
|-------|-------|
| Primary (Navy) | `#1B2A6B` |
| Accent (Rose Pink) | `#C8346B` |
| Heading Font | Poppins |
| Body Font | Inter |

To change the color palette, edit the CSS variables at the top of `assets/css/style.css`.

---

## 🔖 Replacing Placeholder Data

After setup, update the following in `config/config.php`:
- `SITE_CONTACT` – actual barangay hall contact number
- `SITE_EMAIL` – official email address

Update officials in MySQL's `officials` table — replace all `[Placeholder]` names with the actual names and positions of elected officials.

Drop your official logo as `assets/img/logo.svg` or `logo.png` (recommended: 200×200px, transparent background).

---

## 🔐 Security Notes

- All forms use **CSRF tokens**
- Passwords are hashed with **bcrypt** (`PASSWORD_BCRYPT`)
- All output is escaped with `htmlspecialchars`
- PDO uses **prepared statements** throughout — no raw SQL concatenation
- MySQL connection uses standard PDO security best practices

---

## 🌐 Enable HTTPS (Recommended)

InfinityFree provides **FREE SSL certificates**:

1. Log into InfinityFree control panel
2. Go to **SSL Certificates**
3. Install **GoGetSSL** (free option)
4. Wait for activation (up to 72 hours)
5. Add HTTPS redirect to `.htaccess`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

See `HTTPS_SETUP_GUIDE.md` for detailed instructions.

---

## 📌 Notes for Developers

- The `admin` role is assigned directly in the database. To promote a resident to admin, update `role='admin'` in the `users` table.
- The contact form does NOT send emails by default. Configure SMTP or a mail service in `contact.php` to enable email functionality.
- For production, consider adding **rate limiting** to the login form.
- InfinityFree has some limitations (like execution time limits) - keep this in mind for heavy operations.

---

## 🚀 GitHub Actions Auto-Deployment

The repository includes a GitHub Actions workflow that automatically deploys to InfinityFree via FTP whenever you push to the `main` branch.

**Setup:**
1. Add GitHub Secrets (Settings → Secrets → Actions):
   - `FTP_HOSTNAME`
   - `FTP_USERNAME`
   - `FTP_PASSWORD`
   - `FTP_PORT`

2. Push to main branch:
```bash
git add .
git commit -m "Update site"
git push origin main
```

The workflow will automatically:
- Check out your code
- Deploy all files to `/htdocs/` via FTP
- Your site is live! 🎉

**Note:** Database changes must be done manually in InfinityFree's phpMyAdmin.

---

## 📊 Database Schema

The MySQL schema includes:
- `users` - Resident and admin accounts
- `announcements` - Barangay announcements
- `document_requests` - Document request tracking
- `blotter_reports` - Incident reports
- `financial_requests` - Financial assistance applications
- `officials` - Barangay and SK officials

All tables are created with proper foreign keys and constraints.

---

## 🎓 Project Information

**Course:** ITS122L – Web Systems and Technologies 2  
**Institution:** Mapua University  
**Student:** Oquialda, Margaret

---

## 📝 License

This project is for educational purposes.

---

*Built with ❤️ for Barangay Buck Estate, Alfonso, Cavite*
