<?php

/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

namespace Markocupic\BeEmail;

use Markocupic\BeEmail\Model\BeEmailModel;

// Backend modules
$GLOBALS['BE_MOD']['email']['tl_be_email'] = array
(
	'tables' => array('tl_be_email'),
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_be_email'] = BeEmailModel::class;


if (TL_MODE === 'BE' && isset($_GET['do']) && $_GET['do'] === 'tl_be_email')
{
	$GLOBALS['TL_CSS'][] = 'bundles/markocupicbeemail/stylesheet.css|static';

    /**
     * Awesomplete
     */
	$GLOBALS['TL_JAVASCRIPT'][] = 'assets/contao-component-awesomplete/js/awesomplete.min.js';
	$GLOBALS['TL_CSS'][]        = 'assets/contao-component-awesomplete/css/awesomplete.css';

    /**
     * Load plugin js & css
     */
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/markocupicbeemail/be_email_listing.js|static';
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/markocupicbeemail/be_email_listing_autocomplete.js|static';

	if ($_GET['act'] === 'edit')
	{
		// Add Javascript
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/markocupicbeemail/be_email.js|static';
	}

	// HOOKS
	$GLOBALS['TL_HOOKS']['executePreActions'][] = array(BeEmail::class, 'executePreActions');
}
