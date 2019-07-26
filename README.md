# Contao Backend E-Mail
Contao E-Mail Erweiterung für den Versand von Nachrichten aus dem Contao Backend. In den Contao Einstellungen kann definiert werden, woher das Adressbuch seine Adressen zieht. Entweder tl_member oder tl_user oder aus beiden Tabellen.

## Kompatibilität
Die Version 3.x ist nur zu Contao >=4.4 kompatibel.

Viel Spass mit Contao Backend E-Mail!

## beEmailBeforeSend Hook
Mit dem beEmailBeforeSend-Hook können die beiden Objekte vor dem Versand manipuliert werden. Dazu muss ein kleines Modul geschrieben werden.

In der config.php muss der Hook regsitriert werden.
```php
   // config.php
   // Register hook
   $GLOBALS['TL_HOOKS']['beEmailBeforeSend'][] = array('Vendorname\BeEmailBeforeSendHook', 'myBeEmailBeforeSendHook');
```

Die Klasse könnte ungefähr so aussehen.
```php
<?php

namespace Vendorname;

class BeEmailBeforeSendHook
{
    /**
     * !Important
     * Parameters should be passed by reference
     * @param $objEmail
     * @param $beEmailModel
     */
    public function myBeEmailBeforeSendHook(&$objEmail, &$beEmailModel)
    {
        // f.ex. manipulate sender email address
        $objEmail->from = 'foo@myhost.com';

        // f.ex. manipulate content
        $objEmail->text = 'bla bla!!';
        $objEmail->html = 'bla bla!!';

    }

}

```
