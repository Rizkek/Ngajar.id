@echo off
echo ============================================
echo Fix Composer Install Error
echo ============================================
echo.

echo Error: "Resource temporarily unavailable"
echo.
echo Kemungkinan penyebab:
echo 1. Windows Defender sedang scanning folder vendor/
echo 2. File locking issue
echo 3. Antivirus blocking
echo.

REM Clear composer cache
echo [1/4] Clearing composer cache...
composer clear-cache
echo.

REM Remove lock file if exists
echo [2/4] Removing composer.lock...
if exist composer.lock (
    del composer.lock
    echo ✓ composer.lock removed
) else (
    echo - composer.lock not found
)
echo.

REM Try clean vendor folder
echo [3/4] Cleaning vendor folder...
if exist vendor (
    echo Removing old vendor folder...
    timeout /t 2 /nobreak >nul
    rmdir /S /Q vendor 2>nul
    if exist vendor (
        echo ⚠ Could not remove vendor folder (may be locked)
        echo Please close any programs accessing this folder
        pause
    ) else (
        echo ✓ vendor folder removed
    )
) else (
    echo - vendor folder not found
)
echo.

REM Retry install with optimizations
echo [4/4] Retrying composer install...
echo.
echo Running: composer install --no-scripts --prefer-dist
echo.
composer install --no-scripts --prefer-dist

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS! Dependencies installed
    echo ============================================
    echo.
    echo Running post-install scripts...
    composer dump-autoload
    echo.
    echo ✓ Done! You can now run: php artisan serve
) else (
    echo.
    echo ============================================
    echo FAILED! Try these manual fixes:
    echo ============================================
    echo.
    echo 1. Disable Windows Defender temporarily
    echo 2. Run as Administrator
    echo 3. Close VSCode or other editors
    echo 4. Try: composer install --ignore-platform-reqs
)

echo.
pause
