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
