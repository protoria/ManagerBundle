Manager Bundle
========================
Installation
------------

Add the bundle to your `composer.json`:

    "igdr/manager-bundle" : "1.0"

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
