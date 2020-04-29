CakePHP PHP PM Bridge
===================

Alpha. Please use at your own risk.

CakePHP Bridge to use with PHP-PM project (https://github.com/php-pm/php-pm).

Requirements
------------

* CakePHP ^3.6
* PHP ^5.6
* phpcgi installed
* php_pcntl extension installed and enabled
* Note there is a branch for CakePHP 4, use `dev-cake4`

Setup
-------------

* Via composer, add to your composer.json

    "cakedc/cakephp-phppm": "dev-master"

Run
---

* Execute the PM via command line
  * For MAX performance

   vendor/bin/ppm --bridge='\CakeDC\PHPPM\Bridges\Cakephp' start --debug 0 --workers 16 --static-directory webroot > /dev/null

  * For development
  
  vendor/bin/ppm --bridge='\CakeDC\PHPPM\Bridges\Cakephp' start --debug 1 --workers 1 --static-directory webroot
    

Testing it
----------

* Try some benchmarks

    ab -n 5000 -c 100 http://127.0.0.1:8080/api/posts

Important notes
-------------
* This plugin bootstraps your application once, so ensure your bootstrap is not dynamic, for example, no 
dynamic routes coming from database based on request params.
* Sessions: we didn't test how the session is interacting now with the bridge. Other bridges had issues with
the session management.

Support
-------

Commercial support is also available, [contact us](https://www.cakedc.com/contact) for more information.

Contributing
------------

This repository follows the [CakeDC Plugin Standard](https://www.cakedc.com/plugin-standard). If you'd like to contribute new features, enhancements or bug fixes to the plugin, please read our [Contribution Guidelines](https://www.cakedc.com/contribution-guidelines) for detailed instructions.

License
-------

Copyright 2018 Cake Development Corporation (CakeDC). All rights reserved.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.
