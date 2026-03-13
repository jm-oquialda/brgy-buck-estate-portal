# 🏛 Barangay Buck Estate – Online Portal

A formal and modern web portal for **Barangay Buck Estate, Alfonso, Cavite** built with **PHP + Supabase (PostgreSQL)**.

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
- **Database:** Supabase (PostgreSQL via PDO)
- **Frontend:** Vanilla CSS + JS (no frameworks)
- **Fonts:** Google Fonts – Poppins + Inter

---

## ⚡ Setup Instructions

### 1. Create Supabase Project

1. Go to [supabase.com](https://supabase.com) and create a free project.
2. In the Supabase dashboard, go to **SQL Editor**.
3. Open the file `sql/schema.sql` and run the entire contents.
4. This creates all tables and seeds the default admin account + sample data.

### 2. Configure Database Connection

Open `config/config.php` and fill in:

```php
define('DB_HOST', 'db.YOUR_PROJECT_REF.supabase.co');
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');
```

You can find these in **Supabase → Project Settings → Database → Connection string (PHP/PDO)**.

> ⚠️ **Important:** Make sure SSL mode is `require` — the connection already has this configured.

### 3. Deploy to Hosting

You can deploy to any PHP 8.1+ host (e.g., **InfinityFree**, **Hostinger**, **Railway**, **000webhost**).

- Upload all files to your `public_html` or `www` root.
- Make sure the host supports **PHP 8.1+** and the **PDO PostgreSQL** extension (`pdo_pgsql`).

#### To check if pdo_pgsql is available:
Create a file called `phpinfo.php` with:
```php
<?php phpinfo(); ?>
```
Search for `pdo_pgsql` in the output. If missing, contact your hosting provider.

### 4. Default Admin Login

| Field | Value |
|-------|-------|
| Email | `admin@buckestate.gov.ph` |
| Password | `password` |

> 🔴 **Change this immediately after first login** via the Admin panel or directly in Supabase.

---

## 📁 File Structure

```
buck-estate-portal/
├── index.php                    ← Home page
├── about.php                    ← About the barangay
├── announcements.php            ← All announcements
├── announcement.php             ← Single announcement view
├── contact.php                  ← Contact form
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
│   └── img/logo.png             ← ← REPLACE with actual logo
│
└── sql/
    └── schema.sql               ← Run this in Supabase SQL Editor
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

Update officials in Supabase's `officials` table — replace all `[Placeholder]` names with the actual names and positions of elected officials.

Drop your official logo as `assets/img/logo.png` (recommended: 200×200px, PNG with transparent background).

---

## 🔐 Security Notes

- All forms use **CSRF tokens**
- Passwords are hashed with **bcrypt** (`PASSWORD_BCRYPT`)
- All output is escaped with `htmlspecialchars`
- PDO uses **prepared statements** throughout — no raw SQL concatenation
- Supabase connection enforces **SSL (sslmode=require)**

---

## 📌 Notes for the Developer

- The `admin` role is assigned directly in the database. To promote a resident to admin, update `role='admin'` in the `users` table via Supabase.
- The contact form does NOT send emails by default. Uncomment and configure the `mail()` call in `contact.php` after setting up SMTP.
- For production, consider adding **rate limiting** to the login form.

---

*Built for ITS122L – Web Systems and Technologies 2 | Mapua University*  
*Student: Oquialda, Margaret*
