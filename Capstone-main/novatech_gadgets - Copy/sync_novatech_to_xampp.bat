@echo off
setlocal

set "SRC=%~dp0novatech_gadgets"
set "DEST=C:\xampp\htdocs\novatech_gadgets"

echo Syncing NovaTech Gadgets to XAMPP localhost...
echo Source: %SRC%
echo Target: %DEST%

if not exist "%SRC%" (
  echo Source folder not found.
  pause
  exit /b 1
)

if not exist "C:\xampp\htdocs" (
  echo C:\xampp\htdocs was not found. Please install or start XAMPP first.
  pause
  exit /b 1
)

robocopy "%SRC%" "%DEST%" /E /R:2 /W:1
set "ROBOCOPY_EXIT=%ERRORLEVEL%"

if %ROBOCOPY_EXIT% LEQ 7 (
  echo.
  echo Done. Refresh http://localhost/novatech_gadgets/products.php
  echo If product data is old, import novatech_gadgets\database\update_real_products.sql in phpMyAdmin.
  pause
  exit /b 0
)

echo.
echo Copy failed. Robocopy exit code: %ROBOCOPY_EXIT%
pause
exit /b %ROBOCOPY_EXIT%
