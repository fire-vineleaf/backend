@echo off
:ole

php job_camp_ressources.php
php job_upgrade.php
php job_leaderboards.php

rem wait
PING 1.1.1.1 -n 1 -w 1000 >NUL

goto ole