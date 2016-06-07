@ECHO OFF

SET GOOGLE_API_KEY="no"
SET GOOGLE_DEFAULT_CLIENT_ID="no"
SET GOOGLE_DEFAULT_CLIENT_SECRET="no"

SET CRFOLDER=%~dp0
IF "%1" == "" (
    SET INDEX=%CRFOLDER%\index-portable.html
) ELSE (
    SET INDEX=%1
)

:: Extra switches bases on http://peter.sh/experiments/chromium-command-line-switches/
:: --host-rules="MAP * baz, EXCLUDE www.google.com"
:: --ignore-certificate-errors
:: --ignore-gpu-blacklist
:: --incognito
:: --kiosk
START %CRFOLDER%\App\Chrome-bin\chrome.exe ^
--disk-cache-dir="%TEMP%\ChromiumPortable" ^
--user-data-dir="%CRFOLDER%\Data\profile" ^
--disable-backing-store-limit ^
--disable-async-dns ^
--disable-account-consistency ^
--disable-affiliation-based-matching ^
--disable-answers-in-suggest ^
--disable-domain-reliability ^
--disable-breakpad ^
--disable-preconnect ^
--disable-suggestions-service ^
--disable-cloud-import ^
--disable-logging ^
--disable-default-apps ^
--disable-component-cloud-policy ^
--disable-sync ^
--disable-translate ^
--no-default-browser-check ^
--ignore-autocomplete-off-autofill ^
--disable-client-side-phishing-detection ^
--safebrowsing-disable-auto-update ^
--disable-new-avatar-menu ^
"%INDEX%"

