Manager Bundle
========================
Installation
------------

Add the bundle to your `composer.json`:

    "repositories": [
        {
            "type": "git",
            "url": "git@github.com:igdr/ManagerBundle.git"
        }
    ],

    "igdr/manager-bundle" : "dev-master"

and run:

    php composer.phar update

Then add the ManagerBundle to your application kernel:

    // app/IgdrKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Igdr\Bundle\ManagerBundle\IgdrManagerBundle(),
            // ...
        );
    }
