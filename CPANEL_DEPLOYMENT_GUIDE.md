# cPanel Deployment & MySQL Import Guide

This document guides you through deploying the **Facial Recognition Attendance Management System** to a cPanel shared hosting or VPS environment.

---

## 1. Import MySQL Database to cPanel

### Step 1: Create a Database & User in cPanel
1. Log in to your **cPanel**.
2. Click on **MySQL® Databases** (or **MySQL Database Wizard**).
3. Create a new database name, for example: `username_face_db`.
4. Create a new MySQL user, for example: `username_face_user` with a strong password.
5. In the **Add User To Database** section, select the user and database, and grant **ALL PRIVILEGES**.

### Step 2: Import the SQL File
1. In cPanel, open **phpMyAdmin**.
2. In the left sidebar, click on your newly created database (e.g., `username_face_db`).
3. Click the **Import** tab at the top.
4. Click **Choose File** and select:
   ```
   database/cpanel_import.sql
   ```
5. Click the **Import** button at the bottom of the page.
6. All 14 tables, optimized settings (with `min_sharpness = 3`), and default user accounts will be created immediately without any permission errors.

---

## 2. Upload Project Files to cPanel

1. Compress the project folder into a `.zip` archive (you can exclude the local `.git` or scratch files).
2. In cPanel, open **File Manager**.
3. Upload and extract your files into:
   - For primary domain: `public_html` (or create a subdirectory like `public_html/attendance`).
4. Ensure `.htaccess` files are uploaded (make sure **"Show Hidden Files (dotfiles)"** is checked in cPanel File Manager Settings).

---

## 3. Configure `.env` for Production

In your project root directory on cPanel, edit the `.env` file (or copy `.env.example` to `.env`):

```ini
APP_NAME="Face Attendance System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# cPanel MySQL Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourcpanel_face_db
DB_USERNAME=yourcpanel_face_user
DB_PASSWORD=your_cpanel_db_password

SESSION_LIFETIME=120
MAX_UPLOAD_SIZE=5242880
ALLOWED_EXTENSIONS=pdf,jpg,jpeg,png
```

---

## 4. Default Login Credentials

| Role | Username | Email | Password | Display Name |
|---|---|---|---|---|
| **Administrator** | `admin` | `admin@school.edu` | `admin123` | Administrator |
| **Faculty Teacher** | `teacher1` | `alice.teacher@school.edu` | `teacher123` | Alice Teacher |
| **Faculty Teacher 2**| `teacher2` | `bob.smith@school.edu` | `teacher123` | Dr. Bob Smith |
| **Student** | `student1` | `john.doe@student.school.edu` | `student123` | John Doe |
| **Student** | `student2` | `jane.roe@student.school.edu` | `student123` | Jane Roe |
| **Student** | `student3` | `michael.brown@student.school.edu` | `student123` | Michael Brown |
| **Student (Personal)** | `junyue` | `junyue@student.school.edu` | `student123` | Jun Yue (STU-2026-004) |

*(Note: You can log in using either the **Username** or the **Email** address. Passwords can be changed anytime in the user profile menu).*

---

## 5. Security & Folder Permissions

Ensure the upload directory has write permissions on Linux/cPanel:
```bash
chmod -R 755 public/uploads
```
All neural network weights are loaded locally from `public/assets/models/` without requiring external CDN access.
