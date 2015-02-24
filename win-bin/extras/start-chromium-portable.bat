@echo off

SET CRFOLDER=Z:\Path\To\Chromium\MainFolder
IF "%1" == "" (
    SET INDEX=%CRFOLDER%\index-portable.html
) ELSE (
    SET INDEX=%1
)

START %CRFOLDER%\App\Chrome-bin\chrome.exe  --user-data-dir="%CRFOLDER%\Data\profile" "%INDEX%"
:: You can also add 
:: --disk-cache-dir="%TEMP%\ChromiumNightlyPortable" 
