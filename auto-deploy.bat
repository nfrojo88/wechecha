@echo off
setlocal enabledelayedexpansion

echo ===================================================
echo 🚀 Construct-Pro ERP Auto Push & Deploy Started
echo Repository: https://github.com/nfrojo88/wechecha.git
echo Watching: C:\ERP\Constraction\construct-pro-erp
echo Press Ctrl+C to stop
echo ===================================================
echo.

cd /d "C:\ERP\Constraction\construct-pro-erp"

:loop
git status --porcelain > temp_git_status.txt
set HAS_CHANGES=0
for /f "usebackq delims=" %%A in ("temp_git_status.txt") do (
    set HAS_CHANGES=1
)
del temp_git_status.txt >nul 2>&1

if %HAS_CHANGES%==1 (
    echo.
    echo ---------------------------------------------------
    echo 📦 Changes detected! Pushing to GitHub...
    set TIMESTAMP=%date% %time%
    git add .
    git commit -m "Auto-commit & push: !TIMESTAMP!"
    git push origin main
    if !errorlevel! equ 0 (
        echo ✅ Successfully pushed to https://github.com/nfrojo88/wechecha.git
    ) else (
        echo ❌ Git push failed! Will retry on next change.
    )
    echo ---------------------------------------------------
    echo.
)

timeout /t 5 /nobreak >nul
goto loop
