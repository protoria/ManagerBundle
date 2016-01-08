Manager Bundle
========================
Installation
------------

Add the bundle to your `composer.json`:

    composer require igdr/manager-bundle

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
