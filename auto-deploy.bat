@echo off
chcp 65001 > nul
echo ============================================
echo  Auto-Deploy Started - Watching for changes
echo  Repo: https://github.com/nfrojo88/wechecha
echo  Press Ctrl+C to stop
echo ============================================
echo.

cd /d C:\ERP\Constraction\construct-pro-erp

:loop
timeout /t 10 /nobreak > nul

git add .

:: Check if there are staged changes
git diff --cached --quiet
if errorlevel 1 (
    echo [%date% %time%] Changes detected - committing...
    git commit -m "Auto deploy: %date% %time%"
    if errorlevel 1 (
        echo [ERROR] Commit failed!
        goto loop
    )

    echo [%date% %time%] Pushing to GitHub...
    git push origin main
    if errorlevel 1 (
        echo [ERROR] Push failed! Check credentials or internet connection.
    ) else (
        echo [OK] Changes pushed successfully at %time%
    )
) 

goto loop
