@echo off

SET CRFOLDER=%~dp0
IF "%1" == "" (
    SET INDEX=%CRFOLDER%\index-portable.html
) ELSE (
    SET INDEX=%1
)

START %CRFOLDER%\App\Chrome-bin\chrome.exe  --user-data-dir="%CRFOLDER%\Data\profile" "%INDEX%"
:: Extra switches bases on http://peter.sh/experiments/chromium-command-line-switches/
:: --disk-cache-dir="%TEMP%\ChromiumNightlyPortable" 
:: --host-rules="MAP * baz, EXCLUDE www.google.com"
:: --ignore-certificate-errors
:: --ignore-gpu-blacklist
:: --incognito
:: --kiosk
:: --disable-backing-store-limit  --disable-async-dns --disable-account-consistency --disable-affiliation-based-matching --disable-answers-in-suggest --disable-domain-reliability --disable-breakpad --disable-preconnect --disable-suggestions-service --disable-cloud-import --disable-logging --disable-default-apps --disable-component-cloud-policy --disable-sync --disable-translate --no-default-browser-check --ignore-autocomplete-off-autofill --disable-client-side-phishing-detection --safebrowsing-disable-auto-update