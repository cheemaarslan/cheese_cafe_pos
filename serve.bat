@echo off
REM Starts the local dev server with PHP 8.4 deprecation-notice noise silenced.
REM
REM PHP_INI_SCAN_DIR tells PHP to load .ini files from this project's
REM .php-ini-overrides folder IN ADDITION TO the normal WAMP php.ini (it does
REM not replace it), scoped to this one command only. Other projects/sites on
REM this machine are unaffected.
setlocal
set "PHP_INI_SCAN_DIR=%~dp0.php-ini-overrides"
REM --no-reload: without it, Laravel's serve command strips most environment
REM variables (including PHP_INI_SCAN_DIR) before spawning the actual PHP
REM built-in server, since it only passes through a small fixed allowlist.
php artisan serve --no-reload
endlocal
