<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @copyright  Marko Cupic 2012
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    be_email
 * @license    GNU/LGPL
 */

/**
 * Register the classes
 */
ClassLoader::addClasses(array(
	// Modules
	//'Contao\MyClassname' => 'system/modules/be_email/modules/MyClass.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array(
	'be_email_popup'      => 'system/modules/be_email/templates',
));		
