/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */
window.addEvent('domready', function () {

    // Get class instance
    ContaoBeEmailListing = new ContaoBeEmailListing();
    ContaoBeEmailListing.init();


});


/**
 * ContaoBackendEmailListing
 * @type {Type}
 */
ContaoBeEmailListing = new Class(
    {
        init: function () {
            if (String.from(window.location.href).test('do=tl_be_email')) {

                $$('.email-sent').each(function (el) {
                    el.getParents('tr').addClass('row-email-sent');
                });
                $$('.email-not-sent').each(function (el) {
                    el.getParents('tr').addClass('row-email-not-sent');
                });

            }
        }
    }
);
