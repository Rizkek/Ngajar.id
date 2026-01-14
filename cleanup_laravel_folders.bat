@echo off
echo ============================================
echo Cleaning up old LARAVEL_* folders
echo ============================================
echo.
echo This will DELETE the following folders:
echo - LARAVEL_CONFIG
echo - LARAVEL_FILAMENT
echo - LARAVEL_MIGRATIONS
echo - LARAVEL_MODELS
echo - LARAVEL_SEEDERS
echo - LARAVEL_SERVICES
echo - LARAVEL_TEMPLATES
echo.
echo Make sure all files have been moved to Ngajar-id folder!
echo.
pause

echo.
echo Deleting folders...

if exist "LARAVEL_CONFIG" (
    rmdir /S /Q "LARAVEL_CONFIG"
    echo [OK] LARAVEL_CONFIG deleted
)

if exist "LARAVEL_FILAMENT" (
    rmdir /S /Q "LARAVEL_FILAMENT"
    echo [OK] LARAVEL_FILAMENT deleted
)

if exist "LARAVEL_MIGRATIONS" (
    rmdir /S /Q "LARAVEL_MIGRATIONS"
    echo [OK] LARAVEL_MIGRATIONS deleted
)

if exist "LARAVEL_MODELS" (
    rmdir /S /Q "LARAVEL_MODELS"
    echo [OK] LARAVEL_MODELS deleted
)

if exist "LARAVEL_SEEDERS" (
    rmdir /S /Q "LARAVEL_SEEDERS"
    echo [OK] LARAVEL_SEEDERS deleted
)

if exist "LARAVEL_SERVICES" (
    rmdir /S /Q "LARAVEL_SERVICES"
    echo [OK] LARAVEL_SERVICES deleted
)

if exist "LARAVEL_TEMPLATES" (
    rmdir /S /Q "LARAVEL_TEMPLATES"
    echo [OK] LARAVEL_TEMPLATES deleted
)

echo.
echo ============================================
echo Cleanup completed!
echo ============================================
echo.
echo All LARAVEL_* folders have been removed.
echo Your Laravel project is now in: Ngajar-id\
echo.
pause
