@echo off
echo Membuat struktur folder Laravel standar...

REM Membuat folder utama Laravel
if not exist app mkdir app
if not exist config mkdir config
if not exist database mkdir database
if not exist public mkdir public
if not exist resources mkdir resources
if not exist routes mkdir routes
if not exist bootstrap mkdir bootstrap
if not exist storage mkdir storage
if not exist tests mkdir tests

REM Membuat subfolder di app
if not exist app\Console mkdir app\Console
if not exist app\Exceptions mkdir app\Exceptions
if not exist app\Http mkdir app\Http
if not exist app\Http\Controllers mkdir app\Http\Controllers
if not exist app\Http\Middleware mkdir app\Http\Middleware
if not exist app\Models mkdir app\Models
if not exist app\Providers mkdir app\Providers
if not exist app\Services mkdir app\Services
if not exist app\Filament mkdir app\Filament
if not exist app\Filament\Resources mkdir app\Filament\Resources

REM Membuat subfolder di database
if not exist database\factories mkdir database\factories
if not exist database\migrations mkdir database\migrations
if not exist database\seeders mkdir database\seeders

REM Membuat subfolder di resources
if not exist resources\css mkdir resources\css
if not exist resources\js mkdir resources\js
if not exist resources\views mkdir resources\views

REM Membuat subfolder di storage
if not exist storage\app mkdir storage\app
if not exist storage\app\public mkdir storage\app\public
if not exist storage\framework mkdir storage\framework
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\logs mkdir storage\logs

REM Membuat subfolder di tests
if not exist tests\Feature mkdir tests\Feature
if not exist tests\Unit mkdir tests\Unit

REM Membuat subfolder di bootstrap
if not exist bootstrap\cache mkdir bootstrap\cache

echo.
echo Memindahkan file dari folder lama...

REM Pindahkan Models
if exist LARAVEL_MODELS xcopy /E /Y LARAVEL_MODELS\*.* app\Models\

REM Pindahkan Filament Resources
if exist LARAVEL_FILAMENT xcopy /E /Y LARAVEL_FILAMENT\*.* app\Filament\Resources\

REM Pindahkan Services
if exist LARAVEL_SERVICES xcopy /E /Y LARAVEL_SERVICES\*.* app\Services\

REM Pindahkan Migrations
if exist LARAVEL_MIGRATIONS xcopy /E /Y LARAVEL_MIGRATIONS\*.* database\migrations\

REM Pindahkan Seeders
if exist LARAVEL_SEEDERS xcopy /E /Y LARAVEL_SEEDERS\*.* database\seeders\

REM Pindahkan Templates ke Views
if exist LARAVEL_TEMPLATES xcopy /E /Y LARAVEL_TEMPLATES\*.* resources\views\

REM Pindahkan Config files
if exist LARAVEL_CONFIG\filesystems.php copy /Y LARAVEL_CONFIG\filesystems.php config\filesystems.php
if exist LARAVEL_CONFIG\.env.example copy /Y LARAVEL_CONFIG\.env.example .env.example

echo.
echo Struktur folder Laravel berhasil dibuat!
echo.
echo Apakah Anda ingin menghapus folder lama? (Y/N)
set /p deleteOld=

if /i "%deleteOld%"=="Y" (
    echo Menghapus folder lama...
    if exist LARAVEL_MODELS rmdir /S /Q LARAVEL_MODELS
    if exist LARAVEL_FILAMENT rmdir /S /Q LARAVEL_FILAMENT
    if exist LARAVEL_SERVICES rmdir /S /Q LARAVEL_SERVICES
    if exist LARAVEL_MIGRATIONS rmdir /S /Q LARAVEL_MIGRATIONS
    if exist LARAVEL_SEEDERS rmdir /S /Q LARAVEL_SEEDERS
    if exist LARAVEL_TEMPLATES rmdir /S /Q LARAVEL_TEMPLATES
    if exist LARAVEL_CONFIG rmdir /S /Q LARAVEL_CONFIG
    echo Folder lama berhasil dihapus!
) else (
    echo Folder lama tidak dihapus.
)

echo.
echo Selesai!
pause
