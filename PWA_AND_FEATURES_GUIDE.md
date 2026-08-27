# EyeCare Studio — PWA Setup, Mobile/Tablet Compatibility & Bulk Delete Guide

This document details the Progressive Web App (PWA) configuration, tablet/mobile device compatibility (Samsung Tab A9+, iOS iPad), offline caching mechanics, and the Admin Inventory Bulk Select & Delete feature.

---

## 1. PWA & Mobile/Tablet Compatibility Guide

### A. Samsung Tab A9+ 5G & Android Chrome Setup

#### Deep Technical Explanation (HTTPS Requirement)
- Chrome and Android PWA criteria enforce a strict **Secure Context (HTTPS)** requirement.
- When testing on local Wi-Fi IP addresses (e.g., `http://192.168.x.x:8000`), Chrome treats HTTP IP addresses as **untrusted/insecure**. As a result:
  1. `navigator.serviceWorker` registration is blocked by the browser.
  2. `beforeinstallprompt` event does not fire, hiding the automated PWA Install Banner.
  3. Offline caching is bypassed.

#### Testing on Local Wi-Fi (Tablet / Mobile)
1. **Option 1 (Recommended): Ngrok / Tunneling**
   ```bash
   ngrok http 8000
   ```
   Open the HTTPS URL provided by Ngrok on the Samsung Tab. Service Worker and PWA Install Prompt will function automatically.
2. **Option 2: Chrome Unsafe Origin Flag**
   On the Samsung Tab's Chrome browser, navigate to:
   `chrome://flags/#unsafely-treat-insecure-origin-as-secure`
   Enable the flag, add your local IP (e.g. `http://192.168.1.5:8000`), and relaunch Chrome.
3. **Production / Live Deployment**
   On any domain with an active SSL certificate (HTTPS), PWA installation and offline caching will operate 100% seamlessly out-of-the-box.

---

### B. iOS iPad & Safari PWA Compatibility

Apple iOS Safari does **not** support automatic Chrome-style PWA install prompts.

#### How PWA Works on iPad / iOS Safari:
1. Users must manually tap the Safari **Share Button (⎋)** and select **"Add to Home Screen"** ("होम स्क्रीनवर जोडा").
2. Required Apple Meta Tags added to `resources/views/layouts/app.blade.php`:
   ```html
   <meta name="apple-mobile-web-app-capable" content="yes">
   <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
   <meta name="apple-mobile-web-app-title" content="GEM OPTICIANS">
   <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
   <link rel="apple-touch-icon" sizes="192x192" href="/images/icon-192.png">
   <link rel="apple-touch-icon" sizes="512x512" href="/images/icon-512.png">
   ```
3. An automatic iOS Safari instruction toast (`#iosInstallBanner`) guides iPad users to tap **Share ➔ Add to Home Screen**.

---

### C. Service Worker & Caching Mechanics (`public/sw.js`)

- **Cache Version**: `gem-opticals-v7`
- **Network-First for Pages**: Caches visited HTML pages dynamically so previously visited products and categories are accessible offline.
- **Static Pre-caching**: Caches core CSS, JavaScript, icons, and `manifest.json`.
- **API Cache**: Caches `/api/v1/*` json responses for seamless offline catalog browsing.

---

## 2. Admin Inventory Bulk Select & Delete Feature

### Features Implemented
1. **Master Checkbox ("Select All")**: Check/uncheck all visible product rows in `/admin/inventory`.
2. **Dynamic Selection UI**: Highlights selected table rows (`.selected-row`) and displays a real-time `X selected` counter badge in the card header.
3. **Bulk Action Bar**: Displays a red **"Delete Selected"** button (`admin.inventory.bulk-destroy`).
4. **Safety Confirmation**: JavaScript confirmation alert prevents accidental deletions.
5. **Storage Cleanup**: Removes associated product images from `storage/app/public/products` upon deletion.

### Backend Route & Method
- **Route**: `POST /admin/inventory/bulk-destroy` (`admin.inventory.bulk-destroy`)
- **Controller Action**: `App\Http\Controllers\Admin\InventoryController@bulkDestroy`

---

## 3. Database & Automated Testing Verification

- **Automated Tests**: Passed 5/5 tests in `php artisan test`:
  - `AdminInventoryBulkDeleteTest`:
    - `unauthenticated_user_cannot_bulk_delete_inventory` (Passed)
    - `authenticated_admin_can_bulk_delete_selected_products` (Passed)
    - `bulk_delete_requires_ids_array` (Passed)
  - `ExampleTest` (Passed)
  - `Unit/ExampleTest` (Passed)
