<?php

$prefFileDir = realpath('..\\..\\..\\');
$winBinDir = realpath('..\\..\\');

$prefs = parse_ini_file($prefFileDir . DIRECTORY_SEPARATOR . 'prefs.ini');

$chromeExePath = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . 'chrome.exe';
$chromeExeVer = exec('"' . $winBinDir . DIRECTORY_SEPARATOR . 'filever.exe" /B /D /A "' . $chromeExePath . '"');
$chromeExeVer = explode(' ', $chromeExeVer);
$chromeExeVer = $chromeExeVer[9];

$destPak = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . $chromeExeVer . DIRECTORY_SEPARATOR . 'chrome_100_percent.pak';

system('"' . $winBinDir . DIRECTORY_SEPARATOR . 'node.exe" "' . __DIR__ .  DIRECTORY_SEPARATOR . 'main.js" unpack "' . $destPak . '"');

echo(PHP_EOL . 'Done. Exiting...' . PHP_EOL . PHP_EOL);



