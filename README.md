# cr-updater
This script will download and unpack the latest [Chromium snapshot build](https://storage.googleapis.com/chromium-browser-snapshots/index.html) for Windows, Linux and Mac.


For Linux
----------
You need `curl` and `unzip` installed:
    
    sudo apt-get -y install --no-install-recommends curl unzip

Locally run:

    php updater.php

You can also run this script directly from github:

    curl https://raw.githubusercontent.com/pwlin/cr-updater/master/updater.php | php

Or the tinyurl of it:

    curl -L https://tinyurl.com/cr-updater | php

Chromium itself needs the following shared libraries:  
libxss1   
libnss3   
libgconf-2-4  

    sudo apt-get -y install --no-install-recommends libxss1 libnss3 libgconf-2-4

Note 1
------
If you get the following error:

    error while loading shared libraries: libudev.so.0: cannot open shared object file: No such file or directory
    
Do:

    sudo ln -s /lib/x86_64-linux-gnu/libudev.so.1.6.4 /usr/lib/libudev.so.0

Or:

    sudo ln -s /lib/i386-linux-gnu/libudev.so.1.6.4 /usr/lib/libudev.so.0
    
Note 2
------
Run Chromium with the following argument to disable setuid errors:

    --disable-setuid-sandbox (not recommended) 
    

For OSX
--------
You need `curl` and `unzip` installed:

    brew install curl unzip

Locally run:

    php updater.php

You can also run this script directly from github:

    curl https://raw.githubusercontent.com/pwlin/cr-updater/master/updater.php | php

Or the tinyurl of it:

    curl -L https://tinyurl.com/cr-updater | php

For Windows
------------
Locally run:

    win-updater.bat

You can also download official Chrome builds for Windows:

    win-updater.bat --product=chrome [--channel=stable|dev|beta] [--unpack-dir=...]


    
