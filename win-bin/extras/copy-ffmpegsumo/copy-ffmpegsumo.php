<?php

$prefFileDir = realpath('..\\..\\..\\');
$winBinDir = realpath('..\\..\\');

$prefs = parse_ini_file($prefFileDir . DIRECTORY_SEPARATOR . 'prefs.ini');

$chromeExePath = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . 'chrome.exe';
$chromeExeVer = exec('"' . $winBinDir . DIRECTORY_SEPARATOR . 'filever.exe" /B /D /A "' . $chromeExePath . '"');
$chromeExeVer = explode(' ', $chromeExeVer);
$chromeExeVer = $chromeExeVer[9];

$sourceDLL = __DIR__ . DIRECTORY_SEPARATOR . 'ffmpegsumo.dll';
$destDLL = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . $chromeExeVer . DIRECTORY_SEPARATOR . 'ffmpegsumo.dll';

echo(PHP_EOL . 'Copying' . PHP_EOL . $sourceDLL . PHP_EOL . 'to' . PHP_EOL . $destDLL . PHP_EOL);

system('copy /Y "' . $sourceDLL .  '" "' .  $destDLL . '" > NUL');

echo(PHP_EOL . 'Done.' . PHP_EOL . PHP_EOL);



