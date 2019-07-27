<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @copyright  Marko Cupic 2017
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
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




