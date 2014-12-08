Eye4webZfcUserPm
=======

Introduction
------------


Installation
------------
#### With composer

1. Add this project composer.json:

    ```json
    "require": {
        "eye4web/eye4web-zfc-user-pm": "dev-master"
    }
    ```

2. Now tell composer to download the module by running the command:

    ```bash
    $ php composer.phar update
    ```

3. Enable it in your `application.config.php` file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'Eye4web\ZfcUser\Pm'
        ),
        // ...
    );
    ```

4. Copy config/eye4web.zfcuser.pm.global.php.dist to config/autoload/eye4web.zfcuser.pm.global.php
