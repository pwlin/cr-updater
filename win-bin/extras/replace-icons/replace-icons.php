<?php

$prefFileDir = realpath('..\\..\\..\\');
$winBinDir = realpath('..\\..\\');

$prefs = parse_ini_file($prefFileDir . DIRECTORY_SEPARATOR . 'prefs.ini');

$chromeExePath = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . 'chrome.exe';
$chromeExeVer = exec('"' . $winBinDir . DIRECTORY_SEPARATOR . 'filever.exe" /B /D /A "' . $chromeExePath . '"');
$chromeExeVer = explode(' ', $chromeExeVer);
$chromeExeVer = $chromeExeVer[9];

$srcDir = __DIR__ . DIRECTORY_SEPARATOR;

$destPak = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . $chromeExeVer . DIRECTORY_SEPARATOR . 'chrome_100_percent.pak';

system('"' . $winBinDir . DIRECTORY_SEPARATOR . 'node.exe" "' . $srcDir .  'main.js" replace "' . $destPak . '" 7082 "' . $srcDir . 'icons' . DIRECTORY_SEPARATOR . 'folder-blue1.png"');
system('"' . $winBinDir . DIRECTORY_SEPARATOR . 'node.exe" "' . $srcDir .  'main.js" replace "' . $destPak . '" 5715 "' . $srcDir . 'icons' . DIRECTORY_SEPARATOR . 'default_icon.png"');

echo(PHP_EOL . 'Done.' . PHP_EOL . PHP_EOL);



