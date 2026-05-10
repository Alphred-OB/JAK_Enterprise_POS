# JAK POS System

A high-performance, offline-first Point of Sale (POS) and Inventory Management web application designed specifically for retail and wholesale businesses handling fast-moving consumer goods.

## Overview

JAK POS is built using a modern "God-Tier" UI/UX approach. It features an incredibly fast cash register interface, strict role-based access control, cryptographic-level shift tracking, offline-sync capabilities via a Progressive Web App (PWA) Service Worker, and a suite of advanced executive dashboards.

### Core Features

* **God-Tier POS:** Keyboard-first, lightning-fast checkout system with barcode scanning support.
* **Offline Sync Engine:** Native PWA capabilities utilizing IndexedDB and Service Workers. If the internet drops, cashiers keep selling. When Wi-Fi returns, the system automatically syncs data to the master server.
* **Granular Role Control:** Four distinct roles (Admin, Manager, Cashier, Inventory Officer) strictly segregated by custom Laravel Middleware.
* **Theft Prevention:** Immutable audit logs, manager override requirements, and a soft-delete (archival) system that prevents bad actors from scrubbing their history.
* **Advanced Analytics:** Track net profit, lifetime revenue, expenses, and pinpoint cash discrepancies across cashier shifts.

## Tech Stack
* **Backend:** Laravel 13.8, PHP 8.4
* **Frontend:** Blade, Alpine.js, Tailwind CSS (Vite)
* **Database:** MySQL
* **Offline Engine:** IndexedDB + Service Worker (`sw.js`)

## Installation Guide

### Prerequisites
1. PHP 8.2 or higher
2. Composer
3. Node.js (v18+) and npm
4. MySQL Server

### Setup Steps
1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd JAK_POS
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit the `.env` file and configure your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.*

4. **Run Migrations & Seed Database**
   *Note: This will install the baseline roles, categories, and the master admin account.*
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Compile Frontend Assets**
   ```bash
   # For local development
   npm run dev

   # For production deployment
   npm run build
   ```

6. **Link Storage (For logos and product images)**
   ```bash
   php artisan storage:link
   ```

7. **Start the Server**
   ```bash
   php artisan serve
   ```

## Master Admin Credentials
* **Email:** admin@jakpos.com
* **Password:** password
*(Important: Change this immediately upon initial login via the Admin Dashboard).*

## System Architecture Notes

* **Soft Deletes:** Staff members are never hard-deleted to preserve historical sales data. Use the "Restore" functionality in the Admin dashboard if you need to reinstate a fired worker.
* **Shift Reconciliations:** If a cashier's physical drawer has *more* cash than the system expects, it flags an `OVER` discrepancy. This is tracked strictly to prevent un-rung sales.
* **Admin vs. Manager:** Managers handle day-to-day operations (inventory, returns, expenses). Only the Admin can fire staff, backup the database, and change shop branding.

---
*Built with precision and security in mind.*
