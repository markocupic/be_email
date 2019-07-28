<?php

/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */


// Backend modules
$GLOBALS['BE_MOD']['email']['tl_be_email'] = array
(
    'tables' => array('tl_be_email'),
);

if (TL_MODE == 'BE' && $_GET['do'] == 'tl_be_email')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/be_email/assets/stylesheet.css|static';

    // http://leaverou.github.io/awesomplete
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/Autocompleter/awesomplete.js|static';
    $GLOBALS['TL_CSS'][]        = 'system/modules/be_email/assets/Autocompleter/awesomplete.css|static';

    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email_listing.js|static';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email_listing_autocomplete.js|static';

    if ($_GET['act'] == 'edit')
    {
        // Add Javascript
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email.js|static';
    }

    // HOOKS
    $GLOBALS['TL_HOOKS']['executePreActions'][] = array('Markocupic\BeEmail\BeEmail', 'executePreActions');

}




