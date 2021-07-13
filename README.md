![Alt text](https://github.com/markocupic/markocupic/blob/main/logo.png "logo")

# Contao Backend E-Mail
Contao E-Mail Erweiterung für den Versand von Nachrichten aus dem Contao Backend. In den Contao Einstellungen kann definiert werden, woher das Adressbuch seine Adressen zieht. Entweder tl_member oder tl_user oder aus beiden Tabellen.

![Backend](docs/images/app-backend-screenshot.png "backend")

## Kompatibilität
Die Version 3.3 ist nur zu Contao >=4.9 kompatibel.

Viel Spass mit Contao Backend E-Mail!

## beEmailBeforeSend Hook
Mit dem beEmailBeforeSend-Hook können die beiden Objekte vor dem Versand manipuliert werden. Dazu muss eine Contao Hook Klasse geschrieben werden.

Damit der Hook via Annotation registriert wird, muss er in der services.yml registriert werden.

```
# services.yml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        public: true

    Vendorname\App\:
        resource: ../../
        exclude: ../../{DependencyInjection,Resources,Model,Widget}

```

Die Hook-Klasse könnte ungefähr so aussehen. Der Hook erwartet drei Parameter und keinen Rückgabewert. 

```php
<?php

namespace Vendorname\App\Listener\ContaoHooks;

use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("beEmailBeforeSendHook")
 */
class BeEmailBeforeSendHook
{
    /**
     * !!!Important
     * For manipulating data first and second parameter should be passed by reference!
     * @param $objEmail
     * @param $beEmailModel
     * @param $dc
     */
    public function __invoke(&$objEmail, &$beEmailModel, $dc)
    {
        // f.ex. manipulate sender email address
        $objEmail->from = 'foo@myhost.com';

        // f.ex. manipulate content
        $objEmail->text = 'bla bla!!';
        $objEmail->html = 'bla bla!!';
    }

}

```
