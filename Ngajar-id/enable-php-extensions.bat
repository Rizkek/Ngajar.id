@echo off
echo ============================================
echo Fix PHP Extensions untuk Laravel
echo ============================================
echo.

SET PHP_INI=C:\Users\mdr\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.5_Microsoft.Winget.Source_8wekyb3d8bbwe\php.ini

echo PHP INI File: %PHP_INI%
echo.

REM Backup php.ini
echo [1/3] Backing up php.ini...
copy "%PHP_INI%" "%PHP_INI%.backup"
echo ✓ Backup created: php.ini.backup
echo.

REM Enable extensions using PowerShell
echo [2/3] Enabling PHP extensions...
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=fileinfo', 'extension=fileinfo' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=pdo_pgsql', 'extension=pdo_pgsql' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=pgsql', 'extension=pgsql' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=curl', 'extension=curl' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=gd', 'extension=gd' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=mbstring', 'extension=mbstring' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=openssl', 'extension=openssl' | Set-Content '%PHP_INI%'"
powershell -Command "(Get-Content '%PHP_INI%') -replace ';extension=zip', 'extension=zip' | Set-Content '%PHP_INI%'"

echo ✓ Extensions enabled:
echo   - fileinfo (required)
echo   - pdo_pgsql (for PostgreSQL)
echo   - pgsql (for PostgreSQL)
echo   - curl (for HTTP requests)
echo   - gd (for image processing)
echo   - mbstring (for string processing)
echo   - openssl (for encryption)
echo   - zip (for archive handling)
echo.

REM Verify
echo [3/3] Verifying extensions...
php -m | findstr /C:"fileinfo" /C:"pdo_pgsql" /C:"pgsql"
echo.

echo ============================================
echo Extensions enabled successfully!
echo ============================================
echo.
echo Next steps:
echo 1. Close this terminal
echo 2. Open a NEW terminal
echo 3. Run: composer install
echo.
pause
