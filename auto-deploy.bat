@echo off
echo 🚀 Auto-Deploy Started - Watching for changes...
echo Press Ctrl+C to stop

cd C:\ERP\Constraction\construct-pro-erp

:loop
timeout /t 10 /nobreak > nul
git add .
git diff --cached --quiet
if errorlevel 1 (
    git commit -m "Auto deploy: %date% %time%"
    git push origin main
    echo ✅ Changes deployed at %time%
)
goto loop
