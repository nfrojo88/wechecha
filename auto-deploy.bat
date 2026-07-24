@echo off
chcp 65001 > nul
echo ============================================
echo  Auto-Deploy Started - Watching for changes
echo  Repo  : https://github.com/nfrojo88/wechecha
echo  Server: wechechaconstruction.et (Plesk)
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
        goto loop
    )
    echo [OK] Pushed to GitHub at %time%

    echo [%date% %time%] Triggering Plesk deploy on wechechaconstruction.et...
    curl -s -o nul -w "  Server response: %%{http_code}" ^
         -X POST "https://lin6.ethiotelecom.com:8443/modules/git/public/web-hook.php?uuid=f3e098b5-762b-0945-b20c-8433977cd2ba" ^
         --insecure
    echo.
    echo [DONE] Live server updated at %time%
    echo.
)

goto loop
