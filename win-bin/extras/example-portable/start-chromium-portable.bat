@ECHO OFF

IF "%PRODUCT%" == "" (
	SET PRODUCT=Chromium
)

SET GOOGLE_API_KEY=no
SET GOOGLE_DEFAULT_CLIENT_ID=no
SET GOOGLE_DEFAULT_CLIENT_SECRET=no

SET CRFOLDER=%~dp0

IF "%1" == "" (
    SET PARAMS="file:///%CRFOLDER%index-portable.html?%PRODUCT%"
) ELSE (
    SET PARAMS=%*
)

:: Extra switches based on http://peter.sh/experiments/chromium-command-line-switches/
:: --host-rules="MAP * baz, EXCLUDE www.google.com"
:: --ignore-certificate-errors
:: --ignore-gpu-blacklist
:: --incognito
:: --kiosk
:: --start-fullscreen
:: --disable-infobars
:: --disable-web-security
:: --user-agent="my UA"
:: --remote-debugging-port=9222
:: --disable-bundled-ppapi-flash
:: --app=http://example.com/

START "" "%CRFOLDER%%PRODUCT%-App\Chrome-bin\chrome.exe" ^
--disk-cache-dir="%TEMP%\%PRODUCT%Portable" ^
--user-data-dir="%CRFOLDER%%PRODUCT%-Data\profile" ^
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
--safebrowsing-disable-download-protection ^
--safebrowsing-disable-extension-blacklist ^
--disable-new-avatar-menu ^
--disable-background-networking ^
--disable-child-account-detection ^
--disable-clear-browsing-data-counters ^
--disable-contextual-search ^
--disable-credit-card-scan ^
--disable-dinosaur-easter-egg ^
--disable-gaia-services ^
--disable-hang-monitor ^
--disable-login-animations ^
--disable-new-profile-management ^
--disable-notifications ^
--dns-prefetch-disable ^
--no-pings ^
--no-network-profile-warning ^
--no-first-run ^
--non-material ^
%PARAMS%
