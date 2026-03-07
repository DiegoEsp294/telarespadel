@echo off
:: Ejecutar como Administrador

set SCRIPT_PATH=c:\xampp\htdocs\torneos-telares-padel\scripts\backup_bd.bat
set TASK_NAME=BackupTelarePadel

:: Registrar tarea: lunes y viernes a las 8:00am
schtasks /create ^
    /tn "%TASK_NAME%" ^
    /tr "cmd /c \"%SCRIPT_PATH%\" >> C:\backups\telares-padel\backup.log 2>&1" ^
    /sc WEEKLY ^
    /d MON,FRI ^
    /st 08:00 ^
    /ru SYSTEM ^
    /f

if %ERRORLEVEL% == 0 (
    echo Tarea programada registrada correctamente.
    echo Corre: lunes y viernes a las 08:00.
    echo Log en: C:\backups\telares-padel\backup.log
) else (
    echo ERROR al registrar la tarea. Ejecutar como Administrador.
)

pause
