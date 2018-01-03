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

// Add CSS
$GLOBALS['TL_CSS'][] = 'system/modules/be_email/assets/stylesheet.css';

if (TL_MODE == 'BE' && $_GET['do'] == 'tl_be_email')
{
    // http://leaverou.github.io/awesomplete
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/Autocompleter/awesomplete.js';
    $GLOBALS['TL_CSS'][]        = 'system/modules/be_email/assets/Autocompleter/awesomplete.css';

    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email_listing.js';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email_listing_autocomplete.js';
    if ($_GET['act'] == 'edit')
    {
        // Add Javascript
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/be_email/assets/be_email.js';
    }

    // HOOKS
    $GLOBALS['TL_HOOKS']['executePreActions'][] = array('Markocupic\BeEmail\BeEmail', 'executePreActions');

}



