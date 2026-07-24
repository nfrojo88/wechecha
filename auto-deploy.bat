@echo off
chcp 65001 > nul
setlocal

set REPO_PATH=C:\ERP\Constraction\construct-pro-erp
set LOG_FILE=%REPO_PATH%\deploy-log.txt
set BRANCH=main

echo ============================================
echo  Auto-Deploy Started
echo  Repo  : https://github.com/nfrojo88/wechecha
echo  Branch: %BRANCH%
echo  Log   : %LOG_FILE%
echo  Press Ctrl+C to stop
echo ============================================
echo.

cd /d %REPO_PATH%
if errorlevel 1 (
    echo [ERROR] Cannot find project folder: %REPO_PATH%
    pause
    exit /b 1
)

echo [%date% %time%] Auto-deploy started >> "%LOG_FILE%"

:loop
timeout /t 10 /nobreak > nul

:: Stage all changes
git add .

:: Check if there are staged changes
git diff --cached --quiet
if errorlevel 1 (
    echo [%date% %time%] ===== Changes detected =====
    echo [%date% %time%] Changes detected >> "%LOG_FILE%"

    git commit -m "Auto deploy: %date% %time%" >> "%LOG_FILE%" 2>&1
    if errorlevel 1 (
        echo [ERROR] Commit failed - check %LOG_FILE%
        echo [%date% %time%] ERROR: Commit failed >> "%LOG_FILE%"
        goto loop
    )
    echo [%date% %time%] Committed OK

    echo [%date% %time%] Pushing to GitHub...
    git push origin %BRANCH% >> "%LOG_FILE%" 2>&1
    if errorlevel 1 (
        echo [ERROR] Push FAILED - check %LOG_FILE%
        echo [%date% %time%] ERROR: Push failed >> "%LOG_FILE%"
        goto loop
    )

    echo [OK] Pushed to GitHub at %time%
    echo [%date% %time%] Pushed OK to GitHub >> "%LOG_FILE%"
    echo.
) else (
    echo [%time%] Watching... (no changes)
)

goto loop
