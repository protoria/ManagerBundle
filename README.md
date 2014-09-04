Igdr Project Office core
========================
Installation
------------

Add the bundle to your `composer.json`:

    "repositories": [
        {
            "type": "git",
            "url": "git@gitlab.ciklum.net:po-ciklum/core.git"
        }
    ],

    "ciklum/core-bundle" : "dev-master"

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
