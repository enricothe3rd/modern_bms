# Real-Time Broadcasting Troubleshooting

## Issue: Only I see updates, other users don't

### Solution Steps:

### 1. **Hard Refresh All Browser Windows**
Other users need to clear their cache and reload:
- **Windows/Linux**: `Ctrl + Shift + R` or `Ctrl + F5`
- **Mac**: `Cmd + Shift + R`

This ensures they get the new JavaScript with Echo configured.

### 2. **Check Browser Console (F12)**
Each user should see these messages:
```
✅ Using Laravel Echo for real-time updates
📡 Connecting to Pusher...
✅ Pusher connected successfully
✅ Successfully subscribed to obligation-requests channel
```

If you see errors, check below.

### 3. **Verify Queue Worker is Running**
The queue worker MUST be running to broadcast events:
```bash
php artisan queue:work
```

Check the terminal - you should see:
```
App\Events\ObligationRequestCreated ........................ DONE
```

### 4. **Check Pusher Dashboard**
Visit: https://dashboard.pusher.com/apps/2087849/debug_console

You should see:
- **Connections**: Number of connected users
- **Messages**: Events being broadcast

### 5. **Common Issues**

#### Issue: "Laravel Echo not available"
**Solution**: User needs to hard refresh (Ctrl + Shift + R)

#### Issue: "Pusher connection error"
**Solution**: Check .env credentials are correct:
```env
PUSHER_APP_KEY=8564aedf27168917d786
PUSHER_APP_CLUSTER=ap1
```

#### Issue: No events in Pusher dashboard
**Solution**: Queue worker not running. Start it:
```bash
php artisan queue:work
```

#### Issue: Events broadcast but not received
**Solution**: 
1. Check browser console for errors
2. Verify VITE variables in .env:
```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```
3. Rebuild assets: `npm run build`
4. Hard refresh browser

### 6. **Testing Checklist**

✅ Queue worker running (`php artisan queue:work`)  
✅ Assets built (`npm run build`)  
✅ All users hard refreshed (Ctrl + Shift + R)  
✅ Browser console shows "Pusher connected successfully"  
✅ Pusher dashboard shows active connections  

### 7. **Quick Test**

1. Open browser console (F12) on both windows
2. Create an obligation in Window 1
3. Check Window 2 console - should see:
   ```
   🔔 New obligation received via broadcast: {id: 123, ...}
   ```
4. New row should appear with yellow highlight

### 8. **Still Not Working?**

Run these commands:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild assets
npm run build

# Restart queue worker
# Stop current worker (Ctrl+C)
php artisan queue:work
```

Then have all users hard refresh their browsers.

### Debug Mode

Add this to your browser console to see detailed logs:
```javascript
window.Echo.connector.pusher.connection.bind('state_change', function(states) {
    console.log('Pusher state:', states.current);
});
```

### Success Indicators

When working correctly, you'll see:
- 🟢 Blue notification: "Real-time updates active"
- 🟢 Console: "Pusher connected successfully"
- 🟢 Pusher dashboard: Multiple connections
- 🟢 New obligations appear instantly in all windows
