CakePHP PHP PM Bridge
=====================

Alpha. Please use at your own risk.
IMPORTANT: Cookies not working at this point, so no sessions or Csrf available

CakePHP Bridge to use with PHP-PM project (https://github.com/php-pm/php-pm).

Requirements
------------

* CakePHP ^5.0
* PHP ^8.0
* phpcgi installed
* php_pcntl extension installed and enabled

Setup
-----

* Via composer, add to your composer.json

    "cakedc/cakephp-phppm": "dev-2.next-cake5"

Run
---

* Execute the PM via command line
  * For MAX performance

   vendor/bin/ppm --bridge='\CakeDC\PHPPM\Bridges\Cakephp' start --debug 0 --workers 9 --logging 0 --static-directory webroot

  * For development

  vendor/bin/ppm --bridge='\CakeDC\PHPPM\Bridges\Cakephp' start --debug 1 --workers 1 --static-directory webroot


Testing it
----------

* Try some benchmarks

    ab -n 5000 -c 100 http://127.0.0.1:8080/api/posts

Important notes
-------------

* Cookies: they are not working properly now, so you won't have sessions OR Csrf properly working, if you
are providing an API this is not something that should bother you too much anyway...
* This plugin bootstraps your application once, so ensure your bootstrap is not dynamic, for example, no
dynamic routes coming from database based on request params.

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
