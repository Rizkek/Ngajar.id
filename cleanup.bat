@echo off
echo Cleaning up old PHP files and folders...

REM Delete PHP files
del /F /Q "Login.php" 2>nul
del /F /Q "Register.php" 2>nul
del /F /Q "TentangKami.php" 2>nul
del /F /Q "donasi.php" 2>nul
del /F /Q "form_tmbh_kelas.php" 2>nul
del /F /Q "hapus_kelas.php" 2>nul
del /F /Q "index.php" 2>nul
del /F /Q "login_proses.php" 2>nul
del /F /Q "proses_edit_kelas.php" 2>nul
del /F /Q "proses_tambah_kelas.php" 2>nul
del /F /Q "unauthorized.php" 2>nul
del /F /Q "ngajar_id.sql" 2>nul

REM Delete old folders
rmdir /S /Q "Admin" 2>nul
rmdir /S /Q "Murid" 2>nul
rmdir /S /Q "Pengajar" 2>nul
rmdir /S /Q "Includes" 2>nul
rmdir /S /Q "js" 2>nul
rmdir /S /Q "uploads" 2>nul
rmdir /S /Q "vendor" 2>nul
rmdir /S /Q "src" 2>nul
rmdir /S /Q "img" 2>nul
rmdir /S /Q ".git copy" 2>nul

echo Cleanup completed!
echo.
dir /B
