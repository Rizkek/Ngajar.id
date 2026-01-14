@echo off
echo ============================================
echo Moving Laravel files to proper structure
echo ============================================

REM Create necessary directories
echo Creating directories...
if not exist "Ngajar-id\app\Filament\Resources" mkdir "Ngajar-id\app\Filament\Resources"
if not exist "Ngajar-id\app\Services" mkdir "Ngajar-id\app\Services"

REM Move CONFIG files
echo.
echo [1/7] Moving CONFIG files...
copy /Y "LARAVEL_CONFIG\filesystems.php" "Ngajar-id\config\filesystems.php"
copy /Y "LARAVEL_CONFIG\.env.example" "Ngajar-id\.env.example"

REM Move FILAMENT resources
echo.
echo [2/7] Moving FILAMENT resources...
copy /Y "LARAVEL_FILAMENT\*.php" "Ngajar-id\app\Filament\Resources\"

REM Move MIGRATIONS
echo.
echo [3/7] Moving MIGRATIONS...
copy /Y "LARAVEL_MIGRATIONS\*.php" "Ngajar-id\database\migrations\"

REM Move MODELS
echo.
echo [4/7] Moving MODELS...
copy /Y "LARAVEL_MODELS\*.php" "Ngajar-id\app\Models\"

REM Move SEEDERS
echo.
echo [5/7] Moving SEEDERS...
copy /Y "LARAVEL_SEEDERS\*.php" "Ngajar-id\database\seeders\"

REM Move SERVICES
echo.
echo [6/7] Moving SERVICES...
copy /Y "LARAVEL_SERVICES\*.php" "Ngajar-id\app\Services\"

REM Move TEMPLATES (note: these are templates, be careful)
echo.
echo [7/7] Processing TEMPLATES...
echo NOTE: Templates will be backed up first
if exist "Ngajar-id\composer.json" copy /Y "Ngajar-id\composer.json" "Ngajar-id\composer.json.backup"
if exist "Ngajar-id\package.json" copy /Y "Ngajar-id\package.json" "Ngajar-id\package.json.backup"
echo Templates backed up. You can manually merge from LARAVEL_TEMPLATES if needed.

echo.
echo ============================================
echo DONE! All files moved successfully
echo ============================================
echo.
echo Next steps:
echo 1. Review the moved files in Ngajar-id folder
echo 2. Delete LARAVEL_* folders if everything looks good
echo 3. Check composer.json and package.json templates if needed
echo.
pause
