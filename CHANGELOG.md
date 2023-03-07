CHANGE LOG
==========


## V17.0 (07/03/2023)

* Support Laravel 8-10 only
* Support PHP 7.4-8.2 only
* Added additional types


## V16.0 (30/05/2022)

* Dropped PHP < 7.4
* Dropped Laravel < 8


## V15.2 (06/02/2022)

* Added Laravel 9 support
* Support `psr/log` v3


## V15.1.2 (27/01/2022)

* Support `psr/log` v2


## V15.1.1 (21/11/2021)

* Provisional PHP 8.1 support
* Updated package metadata


## V15.1 (07/07/2021)

* Don't let Symfony mess with the JSON encoding options


## V15.0 (04/04/2021)

* Removed Google fonts
* Removed summary field
* Restructured to enable localization
* Made information classes customisable


## V14.1.1 (25/01/2021)

* Fixed PHP 8.0 issue


## V14.1 (14/08/2020)

* Support only Laravel 7 or 8
* Added provisional PHP 8.0 support


## V14.0.2 (14/04/2020)

* Updated funding information


## V14.0.1 (01/03/2020)

* Miscellaneous tweaks


## V14.0 (25/01/2020)

* Support only Laravel 7


## V13.0 (25/01/2020)

* Support only Laravel 6
* Support only PHP 7.2 - 7.4
* Renamed various classes and interfaces
* Work with throwables rather than exceptions


## V12.1 (26/08/2019)

* Added Laravel 6 support


## V12.0 (30/06/2019)

* Transform http foundation exceptions from symfony
* Support the less used status codes and messages
* Require Whoops 2.4 or newer


## V11.3 (03/06/2019)

* Pass the exception has context to the logger
* Added implementation of validation transformer


## V11.2 (10/03/2019)

* Added Laravel 5.8 support


## V11.1 (23/08/2018)

* Added Laravel 5.7 support


## V11.0 (02/03/2018)

* Support PHP 7.1 or 7.2
* Support Laravel 5.5 or 5.6


## V10.1 (02/01/2018)

* Allow symfony 4 dependencies
* Fixed config when inside phar


## V10.0 (06/08/2017)

* Support PHP 7.0, 7.1, 7.2
* Support Laravel 5.3 - 5.5
* Dropped multiple handlers
* Added exception info interface
* Pass through exception to views
* More type hints


## V9.3 (21/01/2017)

* Fixed the html displayer on lumen
* Fixed some edge case crashes


## V9.2 (01/01/2017)

* Added laravel 5.4 support
* Implemented the filter interface correctly


## V9.1 (19/08/2016)

* Added proper support for redirect responses
* Added support for the 402 response code


## V9.0 (05/08/2016)

* Removed http not found from don't report
* Report http not found as a notice by default


## V8.8 (31/07/2016)

* Tweaked the transformers


## V8.7 (21/07/2016)

* Ensure fonts are loaded over https


## V8.6.2 (14/07/2016)

* Moved properties out of the trait to fix L5.3


## V8.6.1 (04/06/2016)

* Corrected logging error supression


## V8.6 (03/06/2016)

* Added a new laravel 5.3 specific handler
* Improvements to the exception handler trait
* Cleaned up the lumen handler


## V8.5 (20/05/2016)

* Improved early crash support


## V8.4 (26/04/2016)

* Added laravel 5.3 support


## V8.3.6 (27/03/2016)

* Fixed broken logging on lumen


## V8.3.5 (27/03/2016)

* Added missing import


## V8.3.4 (27/03/2016)

* Another fix for lumen


## V8.3.3 (27/03/2016)

* Fixed the lumen exception handler


## V8.3.2 (30/01/2016)

* Improved service provider
* Fixed some typos


## V8.3.1 (21/01/2016)

* Fixed with response exceptions


## V8.3 (21/01/2016)

* Deal with model not found exceptions


## V8.2 (15/01/2016)

* Support both whoops ^1.1 and ^2.0


## V8.1 (07/03/2016)

* Added a transformer for laravel's new auth exception


## V8.0 (05/03/2016)

* Pass through info to the user provided error views
* Fixed some typos including a protected property


## V7.0 (11/12/2015)

* Support exceptions with getResponse methods
* Modfied the filters to be passed the request


## V6.1 (09/12/2015)

* Support more client error codes


## V6.0 (22/11/2015)

* Made whoops optional


## V5.1 (14/11/2015)

* Added laravel 5.2 support
* Improved environment detection
* Improvements to id generation


## V5.0 (06/10/2015)

* Improved lumen support
* Updated interfaces so we can filter by code
* Added view displayer to mimic laravel


## V4.0 (25/07/2015)

* Associated uuids with exceptions
* Added default displayer config
* Resolve all the config earlier
* Made the html displayer responsive
* Added exception levels
* Allowed access to exceptions before transformation


## V3.2 (06/07/2015)

* Added exception transformers


## V3.1 (26/06/2015)

* Official lumen support
* Code cleanup


## V3.0.1 (01/06/2015)

* Make sure we're always creating an illuminate response


## V3.0 (28/05/2015)

* Drop support for laravel 5.0
* Improved the error info class
* Return empty body and no content type if we can't match a displayer
* Conformed to json api standards
* Use laravel's new accepts method


## V2.0.1 (26/05/2015)

* Fixed logger resolution in the exception handler


## V2.0 (21/05/2015)

* Support both laravel 5.0 and 5.1
* Removed dependence on views in the html displayer
* Replaced the info trait with an exception info class
* Made exception displayers really configurable
* Correctly deal with content types by default
* Renamed some displayer classes
* Displayer classes now return responses


## V1.0 (04/02/2015)

* Initial release
