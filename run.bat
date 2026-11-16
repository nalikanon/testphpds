@echo off
title Simple Sales Order Management System
echo ==========================================================
echo Starting Simple Sales Order Management System (Laravel)
echo ==========================================================
echo Database: MSSQL (SalesDB)
echo URL: http://127.0.0.1:8000
echo.
echo Opening browser...
start http://127.0.0.1:8000
echo.
echo Server is running. Press Ctrl+C to stop.
echo ==========================================================
php artisan serve --port=8000
pause

