echo off

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: 관리자 권한으로 실행
:: BatchGotAdmin
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

rem Check for permissions
>nul 2>&1 "%SYSTEMROOT%\system32\icacls.exe" "%SYSTEMROOT%\system32\config\system"

rem If error flag set, we do not have admin.
if '%ERRORLEVEL%' NEQ '0' (
    goto UACPrompt
 ) else (
    goto gotAdmin
)
:UACPrompt
    echo Set UAC = CreateObject^("Shell.Application"^) > "%TEMP%\getadmin.vbs"
    echo UAC.ShellExecute "%~s0", "", "", "runas", 1 >> "%TEMP%\getadmin.vbs"
    "%TEMP%\getadmin.vbs"
    exit /B
:gotAdmin
    if exist "%TEMP%\getadmin.vbs" (
        del "%TEMP%\getadmin.vbs"
    )
    pushd "%CD%"
    cd /D "%~dp0"

echo ===============================================================================
echo 관리자 권한으로 실행됩니다

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: Git Config Effective Priority (깃 설정 적용 우선순위 local >>> global >>> system)
:: local(%CD%\.git/config) >>> global(%USERPROFILE%/.gitconfig) >>> system (%PROGRAMFILES%\Git\etc\gitconfig)
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

rem 전역 저장소 설정 보기
rem git config --global --list
rem 현재 프로젝트 폴더 설정 보기
rem git config --local --list
rem 최종적으로 모두 합쳐진 설정 보기
rem git config --list
rem 설정 수정하기
rem git config --local --edit

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: System
:: C:\Program Files\Git\etc\gitconfig
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

rem Git Push 할 때마다 체크섬 검증
rem git config --system transfer.fsckobjects true
rem git config --system fetch.fsckobjects true
rem git config --system receive.fsckobjects true

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: Global
:: ~/.gitconfig
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

echo ===============================================================================
echo Check out Windows CRLF (\r\n), Commit Linux (\n) (개행문자를 받을 땐 윈도우로, 커밋할 땐 리눅스로 처리한다)
git config --global core.autocrlf true
git config --local core.autocrlf true

echo ===============================================================================
echo Case sensitive Filesystems (파일시스템 대소문자를 리눅스처럼 구분한다)
git config --global core.ignorecase false
git config --local core.ignorecase false

echo ===============================================================================
echo Date Format to ISO (YYYY-MM-DD) (git log 등에서 쓰이는 날짜 시간 포맷을 읽기 쉬운 ISO 포맷으로 변경)
git config --global log.date iso
git config --local log.date iso

echo ===============================================================================
echo Use smart diff (Diff 변경점을 라인 단위가 아니라 히스토그램 알고리즘을 이용해 소스 분포별로 묶어서 찾는다)
git config --global diff.algorithm histogram
git config --local diff.algorithm histogram

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: Local
:: .git/config
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

echo ===============================================================================
echo Auto stash Before Rebase, stash pop After Rebase (Pull Rebase 할 때 커밋되지 않고 수정중인 파일 임시 저장)
git config --local rebase.autostash true

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
:: Unused yet or Not used
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::

rem ===============================================================================
rem Pull Rebase instead Merge
rem Pull Merge 대신 Pull Rebase 사용
rem git pull을 실행할 때 브랜치가 상위 브랜치와 다를 경우 실수로 병합 커밋을 생성하는 것을 방지하기 위함
rem pull.rebase true는 매번 git pull --rebase를 실행하는 것과 동일
rem git config --local pull.rebase true

rem ===============================================================================
rem 커밋 후 리베이스 자동화
rem 오래된 커밋을 수정하기 쉽게 만드는 기능.
rem git commit --fixup OLD_COMMIT_ID를 사용하여 커밋하면 git rebase --autosquash main을 실행할 때 자동으로 fixup! 커밋을 대상과 결합.
rem git config --local rebase.autosquash true

rem ===============================================================================
rem Pull 하려는 원격저장소의 브랜치와 로컬저장소의 브랜치가 fast-forward 관계일 때만 Pull을 허용한다
rem git config --local pull.ff only

echo ===============================================================================
echo Git Config 설정이 완료되었습니다

rem
popd

pause
