@ECHO OFF

ECHO Replacing Icons ...
ECHO.
CD replace-icons
CALL replace-icons.bat
ECHO.

ECHO Copy ffmpegsumo.dll ...
ECHO.
CD ..\copy-ffmpegsumo
CALL copy-ffmpegsumo.bat
ECHO.

ECHO Clean After Done ...
ECHO.
CD ..\clean-after-done
CALL clean-after-done.bat
ECHO.

CD ..\
pause