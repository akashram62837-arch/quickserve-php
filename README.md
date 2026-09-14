# QuickServe — PHP 

The Modern Operating System for Local Home Services

QuickServe is a hyper-local, on-demand marketplace connecting households with verified service professionals. Engineered for speed, trust, and scale, it features a multi-portal architecture serving Customers, Service Providers, and Platform Administrators.

```
C:\xampp\htdocs\quickserve\
```

It uses the **exact** database structure and seed data from your original
`quickserve_newphp` SQL dump — no tables, columns, or seed rows were added,
removed, or renamed.

---

## 1. Folder structure

```
quickserve/
├── admin/                 Admin panel (login, dashboard, categories, providers, customers, bookings)
├── provider/               Provider panel (register, login, dashboard, services, bookings, payments, profile)
├── customer/                Customer area (register, login, booking, payments, profile, settings, notifications)
├── config/
│   └── db.php               <-- database credentials live here
├── includes/                 Shared PHP includes (header/footer, dashboard shell, helper functions)
├── assets/
│   ├── css/style.css          Global stylesheet
│   ├── js/main.js              Shared front-end behaviour
│   └── images/                  Category images copied from the original React app
├── database/
│   └── quickserve.sql           The exact SQL dump you provided — import this
├── index.php, about.php, services.php   Public pages
└── .htaccess
```

---

## 2. XAMPP setup (step by step)

1. **Copy the project.** Extract this folder so the path is exactly:
   `C:\xampp\htdocs\quickserve\`

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Create the database.**
   - Open `http://localhost/phpmyadmin`
   - Click **New**, name the database `quickserve_newphp`, collation
     `utf8mb4_general_ci`, click **Create**.

4. **Import the schema + data.**
   - Select the `quickserve_newphp` database
   - Go to the **Import** tab
   - Choose the file `quickserve/database/quickserve.sql`
   - Click **Go**

   You should see 8 tables: `admins`, `bookings`, `categories`, `customers`,
   `notifications`, `payments`, `providers`, `reviews`, `services` — all
   pre-populated with your original seed data.

5. **Check the database config** (only needed if your XAMPP MySQL uses a
   non-default user/password). Open `quickserve/config/db.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'quickserve_newphp');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```
   Default XAMPP MySQL has no root password, so normally you don't need to
   change anything.

6. **Visit the site**: `http://localhost/quickserve/`

That's it — no `composer install`, no build step, no Node required.

---

## 3. Logging in

### Customers
Use the seeded accounts (passwords are the original plain-text seed values —
they are automatically upgraded to secure bcrypt hashes the first time each
one logs in successfully):


Or click **Register** to create a new account.

### Providers


New provider registrations go to `http://localhost/quickserve/provider/register.php`
and require **admin approval** before they can log in (Admin → Providers →
Approve).

### Admin
The dump only contains a bcrypt **hash** for `admin@gmail.com` — the original
plain-text password was never included in the SQL file, so it isn't possible
to know it from the dump alone. To set a password you know, run this on your
own machine (PHP is bundled with XAMPP):

```
C:\xampp\php\php.exe -r "echo password_hash('YourNewPassword', PASSWORD_BCRYPT);"
```

Copy the output hash, then in phpMyAdmin run:

```sql
UPDATE admins SET password = 'PASTE_HASH_HERE' WHERE email = 'admin@gmail.com';
```

Then log in at `http://localhost/quickserve/admin/login.php` with
`admin@gmail.com` and your new password.

---

## 4. What's implemented

- **Authentication & roles**: separate session-based login for customers,
  providers, and admin, with role-guarded pages (`require_customer()`,
  `require_provider()`, `require_admin()` in `includes/functions.php`).
- **Customers**: register/login, browse categories & services (pulled live
  from the DB), book a service (date/time/address), pay (UPI/Card/Cash —
  creates a real row in `payments` and updates `bookings.payment_status`),
  view/cancel bookings, leave a star review after a booking is completed,
  view payment history, edit profile, change password, view notifications.
- **Providers**: register (pending approval), login (blocked until an admin
  approves), dashboard with live stats (bookings, earnings, rating), add
  /edit/delete their own services, accept/reject/complete booking requests,
  view earnings/payment history, edit profile & password.
- **Admin**: login, dashboard with site-wide stats, full CRUD on categories,
  approve/reject/enable/disable/delete providers, enable/disable/delete
  customers, view & change the status of every booking.
- **Security**: PDO prepared statements everywhere (no raw SQL
  concatenation), CSRF tokens on every form, passwords hashed with bcrypt,
  legacy plain-text seed passwords are auto-upgraded on first login,
  `.htaccess` blocking direct access to `config/` and `includes/`.

## 5. Design note

Rather than porting every one of the original React app's ~45 component/CSS
file pairs line-by-line, this build uses one consistent design system
(`assets/css/style.css`) applied across every page — the same blue/slate
palette, typography, card layouts, and dashboard shell the original app used.
The structure, pages, roles, and functionality all match the original app;
the CSS itself was rebuilt rather than transliterated.

## 6. Verified working

This project was tested against a live MySQL database before delivery:
registration, login (including the legacy-password upgrade), browsing,
booking, payment, provider approval, service creation, and admin actions
were all exercised and confirmed working, along with CSRF and
wrong-password rejection.
