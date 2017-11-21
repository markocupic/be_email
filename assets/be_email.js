window.addEvent('domready', function () {

    // Get class instance
    ContaoBeEmail = new ContaoBeEmail();


    // to
    if (document.getElementById('ctrl_recipientsTo') !== null) {
        var addAddressIconTo = new Element('img', {
            'id': 'addAddressIconTo',
            'class': 'open-address-book-icon',
            'role': 'button',
            'src': '/../system/modules/be_email/assets/email.svg',
            'data-input-field': 'ctrl_recipientsTo'

        });
        var ctrl_recipientsTo = document.id('ctrl_recipientsTo');
        addAddressIconTo.inject(ctrl_recipientsTo, 'before');
    }

    // cc
    if (document.getElementById('ctrl_recipientsCc') !== null) {
        var addAddressIconCc = new Element('img', {
            'id': 'addAddressIconCc',
            'class': 'open-address-book-icon',
            'role': 'button',
            'src': '/../system/modules/be_email/assets/email.svg',
            'data-input-field': 'ctrl_recipientsCc'

        });
        var ctrl_recipientsCc = document.id('ctrl_recipientsCc');
        addAddressIconCc.inject(ctrl_recipientsCc, 'before');
    }

    // bcc
    if (document.getElementById('ctrl_recipientsBcc') !== null) {
        var addAddressIconBcc = new Element('img', {
            'id': 'addAddressIconBcc',
            'class': 'open-address-book-icon',
            'role': 'button',
            'src': '/../system/modules/be_email/assets/email.svg',
            'data-input-field': 'ctrl_recipientsBcc'
        });
        var ctrl_recipientsBcc = document.id('ctrl_recipientsBcc');
        addAddressIconBcc.inject(ctrl_recipientsBcc, 'before');
    }

    var inputFields = [addAddressIconTo, addAddressIconCc, addAddressIconBcc];
    inputFields.each(function (inputField) {
        if (inputField) {
            inputField.addEvent('click', function (event) {
                var icon = this;
                new Request.Contao({
                    url: window.location.href,
                    onSuccess: function (txt, json) {

                        // Open modal on click
                        Backend.openModalWindow(900, 'Adressbuch', json.content);

                        // Handle tab visibility
                        $$('#contaoBeEmailAddressBook .tabgroup > div').each(function (el) {
                            el.setStyle('display', 'none');
                        });
                        $$('#contaoBeEmailAddressBook .tabgroup > div')[0].setStyle('display', 'block');

                        // Add active class to first child
                        $$('#contaoBeEmailAddressBook .tabs a')[0].addClass('active');
                    }
                }).post({
                    'action': 'openBeEmailAddressBook',
                    'formInput': icon.getProperty('data-input-field'),
                    'REQUEST_TOKEN': Contao.request_token
                });
            });
        }
    });
});

/**
 * ContaoBackendEmail
 * @type {Type}
 */
ContaoBeEmail = new Class(
    {
        sendmail: function (email, formInputId, elButton) {
            el_form = document.id('tl_be_email');
            var addrInput = el_form[formInputId];
            if (addrInput) {
                if (email) {
                    addrInput.value = email + '; ' + addrInput.value;
                }
                else {
                    alert('Es wurde für diesen Eintrag keine E-Mail-Adresse hinterlegt.');
                }
            }
            else {
                alert('Das Adressbuch funktioniert nur beim Schreiben einer E-Mail. ("to" fehlt)!');
            }
            // Remove button
            var remElement = (elButton.parentNode).removeChild(elButton);
        },
        tabClick: function (el) {
            var others = el.getParent('li').getSiblings('li').getChildren('a');
            var target = el.getProperty('href');
            others.each(function (act) {
                act.removeClass('active');
            });
            el.addClass('active');

            $$('.tabgroup>div').each(function (elDiv) {
                elDiv.setStyle('display', 'none');
            });
            document.id(target).setStyle('display', 'block');
            return false;
        }
    });


window.addEvent('domready', function () {
});





