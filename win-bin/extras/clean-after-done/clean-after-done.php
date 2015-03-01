<?php


$prefFileDir = realpath('..\\..\\..\\');
$winBinDir = realpath('..\\..\\');

$prefs = parse_ini_file($prefFileDir . DIRECTORY_SEPARATOR . 'prefs.ini');

$chromeExePath = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . 'chrome.exe';
$chromeExeVer = exec($winBinDir . DIRECTORY_SEPARATOR . 'filever.exe /B /D /A ' . $chromeExePath);
$chromeExeVer = explode(' ', $chromeExeVer);
$chromeExeVer = $chromeExeVer[9];

$targetDir = $prefs['unpack_dir'] . DIRECTORY_SEPARATOR . 'Chrome-bin' . DIRECTORY_SEPARATOR . $chromeExeVer;

$localeDir = $targetDir . DIRECTORY_SEPARATOR . 'Locales';
if (is_dir($localeDir)) {
	$allowedLocales = array('en-US.pak');
	foreach (glob($localeDir . DIRECTORY_SEPARATOR . "*.pak") as $filename) {
		if (!in_array(basename($filename), $allowedLocales)) {
			echo(PHP_EOL . 'Deleting Locale: ' . basename($filename) . PHP_EOL);
			system('del /F /Q "' . $filename . '"');
		}					
	}
	unset($filename);
}

$visualElementsDir = $targetDir . DIRECTORY_SEPARATOR . 'VisualElements';
if (is_dir($visualElementsDir)) {
	echo(PHP_EOL . 'Deleting useless VisualElements...' . PHP_EOL);
	system('rd /s/q "' . $visualElementsDir . '"');
}

if (is_file($targetDir . DIRECTORY_SEPARATOR . 'secondarytile.png')) {
	echo(PHP_EOL . 'Deleting useless secondarytile.png...' . PHP_EOL);
	system('del /F /Q "' . $targetDir . DIRECTORY_SEPARATOR . 'secondarytile.png' . '"');
}

$allowedManifestFiles = array($chromeExeVer . '.manifest');
foreach (glob($targetDir . DIRECTORY_SEPARATOR . "*.manifest") as $filename) {
	if (!in_array(basename($filename), $allowedManifestFiles)) {
		echo(PHP_EOL . 'Deleting Manifest: ' . basename($filename) . PHP_EOL);
		system('del /F /Q "' . $filename . '"');
	}
}
unset($filename);

echo(PHP_EOL . 'Done. Exiting...' . PHP_EOL . PHP_EOL);



