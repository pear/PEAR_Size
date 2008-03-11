@ECHO OFF
SET phpCli=@PHP-BIN@
:: Check existence of php.exe
IF EXIST "%phpCli%" (
  SET doNothing=
) ELSE GOTO :NoPhpCli

:: If called using options, just call phpdoc and end after without pausing.
:: This will allow use where pausing is not wanted.

"%phpCli%" -d include_path="@PEAR-DIR@" "@BIN-DIR@\pearsize" %*
GOTO :EOF
:NoPhpCli
ECHO ** ERROR *****************************************************************
ECHO * Sorry, can't find the php.exe file.
ECHO * You must edit this file to point to your php.exe (CLI version!)
ECHO *    [Currently set to %phpCli%]
ECHO * 
ECHO * NOTE: In PHP 4.2.x the PHP-CLI used to be named php-cli.exe. 
ECHO *       PHP 4.3.x renamed it php.exe but stores it in a subdir 
ECHO *       called /cli/php.exe
ECHO *       E.g. for PHP 4.2 C:\phpdev\php-4.2-Win32\php-cli.exe
ECHO *            for PHP 4.3 C:\phpdev\php-4.3-Win32\cli\php.exe
ECHO **************************************************************************

