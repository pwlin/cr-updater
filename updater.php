<?php
/**
 * Chromium Updater
 * This script downloads and unpacks the latest Chromium continuous build for Windows and Linux
 * @uses curl
 * @uses unzip
 * You can also run this script directly from github:
 * curl https://raw.githubusercontent.com/pwlin/cr-updater/master/updater.php | php
 * Or the tinyurl of it:
 * curl -L https://tinyurl.com/cr-updater | php
 * On Linux (Debian), Chromium itself needs the following shared libraries:
 * libxss1
 * libnss3
 * libgconf-2-4
 * apt-get -y install --no-install-recommends libxss1 libnss3 libgconf-2-4
 * [Linux] Note 1:
 * If you get the following error:
 * error while loading shared libraries: libudev.so.0: cannot open shared object file: No such file or directory
 * Do:
 * sudo ln -s /lib/x86_64-linux-gnu/libudev.so.1.3.5 /usr/lib/libudev.so.0
 * Or:
 * sudo ln -s /lib/i386-linux-gnu/libudev.so.1.3.5 /usr/lib/libudev.so.0
 * [Linux] Note 2:
 * Run Chromium with the following argument to disable setuid errors:
 * --disable-setuid-sandbox (not recommended) 
 *
 *
 * The MIT License (MIT)

 * Copyright (c) 2015 pwlin05@gmail.com - https://github.com/pwlin/cr-updater

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');
define('VER', '0.3');
define('DIR_SEP', DIRECTORY_SEPARATOR);
define('INIT_DIR', __DIR__);
define('WIN_BIN_DIR', realpath(INIT_DIR . DIR_SEP . 'win-bin'));

class CrUpdater {
	
	private $prefs = array();
	private $osType = '';
	private $prefsFile = '';
	private $userName = '';
	private $tempDir = '';
	private $lastChangeRetryTimes = 0; 
	
	public function init() {
		date_default_timezone_set('UTC');
		$this->setTitle('Chromium Updater [v' . VER . ']');
		$this->prnt('Starting Chromium Updater [v' . VER . ']...');
		$this->setOSType();
		$this->setUserName();
		$this->setTempDir();
		$this->prnt('Parsing preferences...');
		$this->initPrefs();
		$this->prnt('Fetching Chromium LAST_CHANGE file from:' . PHP_EOL . $this->prefs['base_download_url'] . '/LAST_CHANGE');
		$lastChange = $this->fetchLastChangeFile();
		if ($lastChange != $this->prefs['last_change']) {
			$this->prnt('You currently have Chromium build: '. $this->prefs['last_change'] . PHP_EOL . 'Downloading latest version of Chromium build: ' . $lastChange);
			$this->prefs['last_change'] = $lastChange;
			$this->downloadNewBuild($this->prefs['last_change']);
			$this->deleteOldInstalledDir();
			$this->unzipDownloadedFile();
			$this->onBeforeEnd();
			$this->onEnd();
            $this->prnt('Done. Exiting...');
		} else {
			$this->prnt('You already have the latest Chromium version - build: '. $lastChange . PHP_EOL . 'No need to download.');
			$this->cleanUpTempDirectory();
			$this->prnt('Done. Exiting...');
		}
    }
	
	private function setOSType() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || strtoupper(substr(PHP_OS, 0, 6)) === 'CYGWIN') {
			$this->osType = 'win32';
		} elseif (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
			$this->osType = 'linux';
		} elseif (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN') {
			$this->osType = 'mac';
		} else {
			die("\nUnknown OS\n");
		}
	}
	
	private function setUserName() {
		$username = '';
		if ($this->osType === 'win32') {
			$username = exec('echo %USERNAME%');
		} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
			$username = `whoami`;
		}
		$username = trim($username, "\n");
		$this->userName = $username;
	}
	
	private function setTempDir() {
		$this->tempDir = sys_get_temp_dir() . DIR_SEP . 'cr-updater-' . $this->userName;
		$this->mkdir($this->tempDir);
	}
	
	private function prnt($msg) {
		echo(PHP_EOL . '[' . date('Y-m-d H:i:s T', strtotime('now')) . '] ' . PHP_EOL . $msg . PHP_EOL);
	}
	
	public function pause() {
		if ($this->osType === 'win32') {
			echo(PHP_EOL . 'Press any key to continue . . .' . PHP_EOL);
			exec('pause');
		} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
			echo(PHP_EOL);
			system('read -r -p "Press any key to continue . . ." key');
			echo(PHP_EOL);
		}
	}
	
	private function setTitle($msg) {
		if ($this->osType === 'win32') {
			system('TITLE ' . $msg);
		} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
			// @TODO
		}
	}
	
	private function mkdir($path) {
		if (!is_dir($path)) {
			if ($this->osType === 'win32') {
				system('md "' . $path . '"');
			} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
				system('mkdir -p "' . $path . '"');
			}	
		}	
	}
	
	private function rmdir($path) {
		if (is_dir($path)) {
			if ($this->osType === 'win32') {
				system('rd /s/q "' . $path . '"');
			} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
				system('rm -r "' . $path . '"');
			}
		}
	}
	
	private function rm($path) {
		if (is_file($path)) {
			if ($this->osType === 'win32') {
				system('del /F /Q "' . $path . '"');
			} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
				system('rm "' . $path . '"');
			}
		}	
	}
	
	private function unzip($file, $dest) {
		$unzip = '';
		$unzip_prefix = '';
		if ($this->osType === 'linux' || $this->osType === 'mac') {
			$unzip = 'unzip ';
			$unzip_prefix = ' /dev/null';
		}
		system($unzip . '"' .  $file . '" -d "' . $dest . '" > '. $unzip_prefix);
	}
	
	private function unzipWith7Zip($file, $o = null) {
		$o = $o === null ? '-o"' . $this->tempDir . '"' : '-o"' . $o . '"';
		system('"' . WIN_BIN_DIR . DIR_SEP . '7z.exe" x -y "' . $file . '" ' . $o);	
	}
	
	private function curl($url, $saveTo, $silent = true) {
		$params = ' -A "' . $this->prefs['user_agent'] . '" ';
		$params .= $silent === true ? '-s -ss -L' : '-L';
		$curl = '';
		if ($this->osType === 'win32') {
			$curl = '"' . WIN_BIN_DIR . DIR_SEP . 'curl.exe" ';
		} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
			$curl = 'curl ';
		}
		//$this->prnt($curl . ' ' . $params . ' "' . $url . '" -o "' . $saveTo . '"');
		system($curl . ' ' . $params . ' "' . $url . '" -o "' . $saveTo . '"');
	}
	
	private function setPrefsFile() {
		if ($this->osType === 'win32') {
			$this->prefsFile = INIT_DIR . DIR_SEP . 'prefs.ini';
		} elseif ($this->osType === 'linux') {
			$configFolder = DIR_SEP . 'home' . DIR_SEP . $this->userName . DIR_SEP . '.config' . DIR_SEP . 'cr-updater';
			$this->mkdir($configFolder);
			$this->prefsFile = $configFolder . DIR_SEP . 'prefs.ini';
		} elseif ($this->osType === 'mac') {
			$configFolder = DIR_SEP . 'Users' . DIR_SEP . $this->userName . DIR_SEP . '.config' . DIR_SEP . 'cr-updater'; 
			$this->mkdir($configFolder);
			$this->prefsFile = $configFolder . DIR_SEP . 'prefs.ini';
		}
	}
	
	private function defaultUnpackDir() {
		$unpackDir = '';
		if ($this->osType === 'win32') {
			$unpackDir = INIT_DIR;
		} elseif ($this->osType === 'linux') {
			$unpackDir = DIR_SEP . 'home' . DIR_SEP . $this->userName . DIR_SEP . 'programs' . DIR_SEP . 'ChromiumNightly';
			$this->mkdir($unpackDir);
		} elseif ($this->osType === 'mac') {
			$unpackDir = DIR_SEP . 'Applications' . DIR_SEP . 'ChromiumNightly';
			$this->mkdir($unpackDir);
		}
		return $unpackDir;
	}
	
	private function defaultBaseDownloadUrl() {
		//$url = 'https://commondatastorage.googleapis.com/chromium-browser-continuous';
		$url = 'https://storage.googleapis.com/chromium-browser-continuous';
		if ($this->osType === 'win32') {
			$url .= '/Win';
		} elseif ($this->osType === 'linux') {
			$url .= '/Linux';
		} elseif ($this->osType === 'mac') {
			$url .= '/Mac';
		}
		return $url;
	}
	
	private function defaultUserAgent() {
		$ua = '';
		if ($this->osType === 'win32') {
			$ua = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36';
		} elseif ($this->osType === 'linux') {
			$ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36';
		} elseif ($this->osType === 'mac') {
			$ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36';
		}
		return $ua;
	}
	
	
	private function initPrefs() {
		$this->setPrefsFile();
		if (!is_file($this->prefsFile)) {
			file_put_contents($this->prefsFile, '');
		}
		$this->prefs = parse_ini_file($this->prefsFile);
		$default_prefs = array(
				'last_change' => '0',
				'unpack_dir' => $this->defaultUnpackDir(),
				'base_download_url' => $this->defaultBaseDownloadUrl(),
				'user_agent' => $this->defaultUserAgent(),
		);
		$this->prefs['last_change'] = !empty($this->prefs['last_change']) ? $this->prefs['last_change'] : $default_prefs['last_change'];
		$this->prefs['unpack_dir'] = !empty($this->prefs['unpack_dir']) && is_dir($this->prefs['unpack_dir']) ? $this->prefs['unpack_dir'] : $default_prefs['unpack_dir'];
		$this->prefs['base_download_url'] = !empty($this->prefs['base_download_url']) ? $this->prefs['base_download_url'] : $default_prefs['base_download_url'];
		$this->prefs['user_agent'] = !empty($this->prefs['user_agent']) ? $this->prefs['user_agent'] : $default_prefs['user_agent'];
	}
	
	private function fetchLastChangeFile() {
		$saveTo = $this->tempDir . DIR_SEP . 'LAST_CHANGE';
		$this->rm($saveTo);
		$this->curl($this->prefs['base_download_url'] . '/LAST_CHANGE', $saveTo, true);
		if (is_file($saveTo)) {
			$lastChange = trim(file_get_contents($saveTo));
			return $lastChange;
		} else {
			$this->prnt('Sorry but I canot get the LAST_CHANGE file at:' . PHP_EOL . $this->prefs['base_download_url'] . '/LAST_CHANGE' . PHP_EOL . 'Exiting...');
			exit(1);
		}
	}
	
	private function downloadNewBuild($last_change) {
		$downloadUrl = '';
		$saveTo = '';
		if ($this->osType === 'win32') {
			$downloadUrl = $this->prefs['base_download_url'] . '/' . $last_change. '/mini_installer.exe';
			$saveTo = $this->tempDir . DIR_SEP . 'mini_installer.exe';
		} elseif ($this->osType === 'linux' || $this->osType === 'mac') {
			$downloadUrl = $this->prefs['base_download_url'] . '/' . $last_change. '/chrome-' . $this->osType . '.zip';
			$saveTo = $this->tempDir . DIR_SEP . 'chrome-' . $this->osType . '.zip';
		}
		$this->rm($saveTo);
		$this->prnt('Downloading new build: ' . PHP_EOL . $downloadUrl . PHP_EOL . 'Saving to: ' . PHP_EOL . $saveTo . PHP_EOL);
		$this->curl($downloadUrl, $saveTo, false);
		if (!is_file($saveTo) || filesize($saveTo) < 1 ) {
			$this->prnt('Sorry but something went wrong while downloading:' . PHP_EOL . $downloadUrl . PHP_EOL . 'Exiting...');
			exit(1);
		}
	}
	
	private function deleteOldInstalledDir() {
		$installedDir = '';
		if ($this->osType === 'win32') {
			$installedDir = $this->prefs['unpack_dir'] . DIR_SEP . 'Chrome-bin';
		} elseif  ($this->osType === 'linux' || $this->osType === 'mac') {
			$installedDir = $this->prefs['unpack_dir'] . DIR_SEP . 'chrome-' . $this->osType;
		}		
		if (is_dir($installedDir)) {
			$this->prnt('Deleting old directory: ' . PHP_EOL . $installedDir . PHP_EOL);
			$this->rmdir($installedDir);
			sleep(2);
		}
	}
	
	private function unzipDownloadedFile() {
		if ($this->osType === 'win32') {
			$miniInstallerFile = $this->tempDir . DIR_SEP . 'mini_installer.exe';
			$chrome7zFile = $this->tempDir . DIR_SEP . 'chrome.7z';
			$this->rm($chrome7zFile);
			$this->prnt('Unzipping ' . $miniInstallerFile . PHP_EOL);
			$this->unzipWith7Zip($miniInstallerFile);
			$this->prnt('Unzipping ' . $chrome7zFile . PHP_EOL);
			$this->unzipWith7Zip($chrome7zFile, $this->prefs['unpack_dir']);
		} elseif  ($this->osType === 'linux' || $this->osType === 'mac') {
			$zipFile = $this->tempDir . DIR_SEP . 'chrome-' . $this->osType . '.zip';
			$unzipToDir = $this->prefs['unpack_dir'];
			$this->prnt('Unzipping: ' . PHP_EOL . $zipFile . PHP_EOL . 'to' . PHP_EOL . $unzipToDir);
			$this->unzip($zipFile, $unzipToDir);
			$this->rm($zipFile);
		}		
	}
	
	private function onBeforeEnd() {
		if ($this->osType === 'mac') {
			$this->rmdir($this->prefs['unpack_dir'] . DIR_SEP . 'chrome-' . $this->osType . DIR_SEP . 'pnacl');
		}
	}
	
	private function onEnd() {
		$this->cleanUpTempDirectory();
		$this->savePrefsToFile();
	}	
	
	private function cleanUpTempDirectory() {
		$this->prnt('Cleaning up the temporary files...');
		$this->rmdir($this->tempDir);
		sleep(2);
	}
	
	private function savePrefsToFile() {
		$prefs_txt = '';
		$prefs_keys = array_keys($this->prefs);
		$prefs_values = array_values($this->prefs);
		for ($i = 0; $i <= count($this->prefs) - 1; $i++) {
			$prefs_txt .= '' . $prefs_keys[$i] . '="' . $prefs_values[$i] . '"' . PHP_EOL;
		}
		file_put_contents($this->prefsFile, $prefs_txt);
	}

}

$updater = new CrUpdater();
$updater->init();
$updater->pause();

