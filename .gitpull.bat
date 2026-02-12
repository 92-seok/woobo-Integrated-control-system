@echo off

cd /D "%~dp0"

git stash --include-untracked
echo %ERRORLEVEL%

git fetch origin
echo %ERRORLEVEL%

git reset --hard origin/master
echo %ERRORLEVEL%

git checkout -B master origin/master
echo %ERRORLEVEL%

git clean -f -d
echo %ERRORLEVEL%

git pull --rebase
echo %ERRORLEVEL%

git stash pop
echo %ERRORLEVEL%

echo bat ok gitpull

exit /B 0