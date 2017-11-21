window.addEvent('domready', function () {
    var objUri = new URI(window.location.href);
    // to
    if (document.getElementById('ctrl_recipientsTo') !== null) {
        var addAddressIconTo = new Element('img', {
            id: 'addAddressIconTo',
            'class': 'open-address-book-icon',
            'role': 'button',
            src: '/../system/modules/be_email/assets/email.svg'
        });
        var ctrl_recipientsTo = document.id('ctrl_recipientsTo');
        addAddressIconTo.inject(ctrl_recipientsTo, 'before');
    }

    // cc
    if (document.getElementById('ctrl_recipientsCc') !== null) {
        var addAddressIconCc = new Element('img', {
            id: 'addAddressIconCc',
            'class': 'open-address-book-icon',
            'role': 'button',
            src: '/../system/modules/be_email/assets/email.svg'
        });
        var ctrl_recipientsCc = document.id('ctrl_recipientsCc');
        addAddressIconCc.inject(ctrl_recipientsCc, 'before');
    }

    // bcc
    if (document.getElementById('ctrl_recipientsBcc') !== null) {
        var addAddressIconBcc = new Element('img', {
            id: 'addAddressIconBcc',
            'class': 'open-address-book-icon',
            'role': 'button',
            src: '/../system/modules/be_email/assets/email.svg'
        });
        var ctrl_recipientsBcc = document.id('ctrl_recipientsBcc');
        addAddressIconBcc.inject(ctrl_recipientsBcc, 'before');
    }
    if (addAddressIconTo) {
        addAddressIconTo.addEvent('click', function (event) {
            new Request.Contao({
                url: window.location.href,
                onSuccess: function (txt, json) {
                    console.log(json);
                    Backend.openModalWindow(900, 'Adressbuch', json.content);
                }
            }).post({
                'action': 'openBeEmailAddressBook',
                'formInput': 'ctrl_recipientsTo',
                'REQUEST_TOKEN': Contao.request_token
            });
        });
    }
    if (addAddressIconCc) {
        addAddressIconCc.addEvent('click', function (event) {
            new Request.Contao({
                url: window.location.href,
                onSuccess: function (txt, json) {
                    console.log(json);
                    Backend.openModalWindow(900, 'Adressbuch', json.content);
                }
            }).post({
                'action': 'openBeEmailAddressBook',
                'formInput': 'ctrl_recipientsCc',
                'REQUEST_TOKEN': Contao.request_token
            });
        });
    }
    if (addAddressIconBcc) {
        addAddressIconBcc.addEvent('click', function (event) {
            new Request.Contao({
                url: window.location.href,
                onSuccess: function (txt, json) {
                    console.log(json);
                    Backend.openModalWindow(900, 'Adressbuch', json.content);
                }
            }).post({
                'action': 'openBeEmailAddressBook',
                'formInput': 'ctrl_recipientsBcc',
                'REQUEST_TOKEN': Contao.request_token
            });
        });
    }
});

/**
 * ContaoBackendEmail
 * @type {Type}
 */
ContaoBeEmail = new Class(
{
    sendmail: function (email, formInputId) {
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
    },
    removeElement: function (el) {
        var remElement = (el.parentNode).removeChild(el);
    }
});


window.addEvent('domready', function () {
    ContaoBeEmail = new ContaoBeEmail();
});





