@echo off
setlocal

for /f "delims=" %%a in (version.txt) do (
	set "version=%%a"
)

echo VERSION
echo %version%

set "outpath=.\trunk\%version%"
rmdir /s /q %outpath%\
mkdir %outpath%\

copy readme.txt %outpath%\
xcopy resources\ %outpath%\ /e 
xcopy src\*.php %outpath%\

cd %outpath%

set "zipfile=..\..\release\ganohrs-addtect-%version%.zip"
del %zipfile%

tar -a -c -f %zipfile% *

set "basefile=..\..\release\ganohrs-addtect.zip"
del %basefile%

copy %zipfile% %basefile%

endlocal
pause
echo on
