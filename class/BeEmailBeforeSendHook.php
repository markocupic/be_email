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
