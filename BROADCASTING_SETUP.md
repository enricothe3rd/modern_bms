# Real-Time Broadcasting Setup Guide

This application uses Laravel Broadcasting for real-time updates. You can use either **Pusher** (cloud service) or **Laravel Reverb** (self-hosted).

## Option 1: Using Pusher (Easiest - Cloud Service)

### Step 1: Install Pusher PHP SDK
```bash
composer require pusher/pusher-php-server
```

### Step 2: Install Laravel Echo and Pusher JS
```bash
npm install --save-dev laravel-echo pusher-js
```

### Step 3: Configure .env
Add these to your `.env` file:
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

Get your credentials from: https://dashboard.pusher.com/

### Step 4: Update config/broadcasting.php
Ensure Pusher is configured (should be default):
```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
    ],
],
```

### Step 5: Initialize Laravel Echo
In `resources/js/bootstrap.js`, uncomment and configure:
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
```

### Step 6: Add to .env
```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Step 7: Build assets
```bash
npm run build
```

---

## Option 2: Using Laravel Reverb (Self-Hosted - Free)

### Step 1: Install Reverb
```bash
php artisan install:broadcasting
```

### Step 2: Configure .env
```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Step 3: Start Reverb Server
```bash
php artisan reverb:start
```

Keep this running in a separate terminal.

### Step 4: Build assets
```bash
npm run build
```

---

## Testing the Setup

1. Open the Obligation Requests page in two browser windows
2. Create a new obligation in one window
3. The other window should automatically show the new obligation without refreshing

## Troubleshooting

### Broadcasting not working?
1. Check if queue worker is running: `php artisan queue:work`
2. Check browser console for errors
3. Verify .env credentials are correct
4. For Reverb: Ensure `php artisan reverb:start` is running

### Still using polling?
The view has been updated to use Laravel Echo. If Echo is not available, it falls back to polling.

## Current Implementation

The real-time feature is already implemented in:
- `app/Events/ObligationRequestCreated.php` - Event that broadcasts
- `app/Services/ObligationRequestService.php` - Dispatches event on create
- `resources/views/obligation-requests/index.blade.php` - Listens for broadcasts

Just follow the setup steps above to enable it!
