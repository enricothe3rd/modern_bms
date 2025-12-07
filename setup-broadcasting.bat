@echo off
echo =========================================
echo Laravel Broadcasting Setup Script
echo =========================================
echo.
echo Choose your broadcasting driver:
echo 1) Pusher (Cloud - Easiest)
echo 2) Laravel Reverb (Self-hosted - Free)
echo.
set /p choice="Enter choice [1-2]: "

if "%choice%"=="1" (
    echo.
    echo Setting up Pusher...
    echo.
    
    REM Install Pusher PHP SDK
    echo Installing Pusher PHP SDK...
    call composer require pusher/pusher-php-server
    
    REM Install JS dependencies
    echo Installing Laravel Echo and Pusher JS...
    call npm install --save-dev laravel-echo pusher-js
    
    echo.
    echo [32mPusher packages installed![0m
    echo.
    echo Next steps:
    echo 1. Sign up at https://dashboard.pusher.com/
    echo 2. Create a new app and get your credentials
    echo 3. Add to your .env file:
    echo.
    echo    BROADCAST_DRIVER=pusher
    echo    PUSHER_APP_ID=your_app_id
    echo    PUSHER_APP_KEY=your_app_key
    echo    PUSHER_APP_SECRET=your_app_secret
    echo    PUSHER_APP_CLUSTER=mt1
    echo    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    echo    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    echo.
    echo 4. Uncomment Echo setup in resources/js/bootstrap.js
    echo 5. Run: npm run build
    echo 6. Start queue worker: php artisan queue:work
    
) else if "%choice%"=="2" (
    echo.
    echo Setting up Laravel Reverb...
    echo.
    
    REM Install broadcasting
    call php artisan install:broadcasting
    
    echo.
    echo [32mReverb installed![0m
    echo.
    echo Next steps:
    echo 1. Check your .env file for REVERB_* settings
    echo 2. Run: npm run build
    echo 3. Start Reverb server: php artisan reverb:start
    echo 4. Start queue worker: php artisan queue:work
    
) else (
    echo Invalid choice. Exiting.
    exit /b 1
)

echo.
echo =========================================
echo Setup complete! Follow the next steps above.
echo =========================================
pause
