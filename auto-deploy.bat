@echo off
echo 🚀 FTP Auto-Deploy Started - Watching for changes...
echo Press Ctrl+C to stop

:: ⚠️ Set your paths and FTP details here ⚠️
:: (Do not add quotes around the paths here)
set WATCH_DIR=C:\ERP\Constraction\construct-pro-erp
set FTP_HOST=wechechaconstruction.et
set FTP_USER=laravel
set FTP_PASS=frowechecha@88
set FTP_DIR=/httpdocs

:: Check if WinSCP is installed
where winscp.com >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ ERROR: winscp.com was not found!
    echo Please download WinSCP from https://winscp.net/eng/download.php
    echo Install it, and make sure to check "Add WinSCP to search path" during installation.
    pause
    exit /b
)

echo 🌐 Connecting to Plesk FTP and monitoring for changes...
echo Any files you save will be instantly uploaded!
echo.

:: WinSCP's keepuptodate command watches the folder and uploads files the moment they change
winscp.com /command ^
    "open ftp://%FTP_USER%:%FTP_PASS%@%FTP_HOST%/" ^
    "keepuptodate ""%WATCH_DIR%"" ""%FTP_DIR%""" ^
    "exit"

pause
