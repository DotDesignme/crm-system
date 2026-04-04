# 🚀 OpenCRM: Modern Glassmorphism CRM System

A professional, fast, and feature-rich CRM system built with Laravel 13, featuring a stunning **Glassmorphism UI** and advanced Lead lifecycle management.

## 🌟 Key Features

* **Glassmorphism UI:** Sophisticated user interface with frosted glass effects and responsive design.
* **Lead Lifecycle:** Seamlessly track leads from "New" to "Won" or "Lost" with reason tracking.
* **Duplicate Prevention:** Intelligent checks to ensure lead data remains clean and unique.
* **Deal Pipeline:** Integrated sales tracking with revenue forecasting.
* **Role-Based Access Control (RBAC):** Precise permissions for employees and departments.
* **Dynamic Branding:** Full control over logo, favicon, and system colors from the admin panel.
* **Full Arabic Support:** Native RTL support and deep localization for the Middle East market.

## 🛠️ Technical Stack

* **Backend:** Laravel 13 (PHP 8.3/8.4)
* **Frontend:** Blade, Vanilla CSS (Glassmorphism Metrics), Vite
* **Database:** SQLite (default for simplicity) / MySQL / PostgreSQL
* **Deployment:** BASH Automation ready (compatible with aaPanel, VPS, and standard Shared Hosting).

## 🚀 Getting Started (Local Development)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Dotdesignme/crm-system.git
   cd crm-system
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Setup Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Prepare Database:**
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Start Server:**
   ```bash
   php artisan serve
   ```

## 🌍 Deployment

Check out the provided deployment examples in the root directory:
* `auto_deploy.sh.example`
* `server_deploy.sh.example`
* `build_and_zip.sh.example`

Rename these by removing the `.example` extension and configure them with your server details for a 1-click automated deployment.

## 🔒 Security
This version is pre-configured for **Open Source Release**. All sensitive files (like `.env`, `auto_deploy.sh`, and `database.sqlite`) are ignored by Git. Refer to the `.example` files to set up your own environment safely.

---
*OpenCRM - Built to empower modern sales teams.*
