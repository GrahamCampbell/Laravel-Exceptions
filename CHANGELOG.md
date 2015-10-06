CHANGE LOG
==========


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
