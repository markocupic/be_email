window.addEvent('domready', function () {
    var objUri = new URI(window.location.href);
    // to
    if (document.getElementById('ctrl_recipientsTo') !== null) {
        var addAddressIconTo = new Element('img', {
            id: 'addAddressIconTo',
            src: '/../system/modules/be_email/assets/add_address.png'
        });
        var ctrl_recipientsTo = document.id('ctrl_recipientsTo');
        addAddressIconTo.inject(ctrl_recipientsTo, 'before');
    }

    // cc
    if (document.getElementById('ctrl_recipientsCc') !== null) {
        var addAddressIconCc = new Element('img', {
            id: 'addAddressIconCc',
            src: '/../system/modules/be_email/assets/add_address.png'
        });
        var ctrl_recipientsCc = document.id('ctrl_recipientsCc');
        addAddressIconCc.inject(ctrl_recipientsCc, 'before');
    }

    // bcc
    if (document.getElementById('ctrl_recipientsBcc') !== null) {
        var addAddressIconBcc = new Element('img', {
            id: 'addAddressIconBcc',
            src: '/../system/modules/be_email/assets/add_address.png'
        });
        var ctrl_recipientsBcc = document.id('ctrl_recipientsBcc');
        addAddressIconBcc.inject(ctrl_recipientsBcc, 'before');
    }
    if (addAddressIconTo) {
        addAddressIconTo.addEvent('click', function (event) {
            // url param popup=true is important, otherwise contao will redirect you to the address popup, when sending the email (Contao referer)
            popup('/contao/main.php?do=tl_be_email&popup=true&mode=addAddresses&dest=to&id=' + objUri.getData('id') + '&pid=' + objUri.getData('pid') + '&rt=' + objUri.getData('rt') + '&ref=' + objUri.getData('ref'));
        });
    }
    if (addAddressIconCc) {
        addAddressIconCc.addEvent('click', function (event) {
            // url param popup=true is important, otherwise contao will redirect you to the address popup, when sending the email (Contao referer)
            popup('/contao/main.php?do=tl_be_email&popup=true&mode=addAddresses&dest=cc&id=' + objUri.getData('id') + '&pid=' + objUri.getData('pid') + '&rt=' + objUri.getData('rt') + '&ref=' + objUri.getData('ref'));
        });
    }
    if (addAddressIconBcc) {
        addAddressIconBcc.addEvent('click', function (event) {
            // url param popup=true is important, otherwise contao will redirect you to the address popup, when sending the email (Contao referer)
            popup('/contao/main.php?do=tl_be_email&popup=true&mode=addAddresses&dest=bcc&id=' + objUri.getData('id') + '&pid=' + objUri.getData('pid') + '&rt=' + objUri.getData('rt') + '&ref=' + objUri.getData('ref'));
        });
    }
});

function popup(URL) {
    openWindow(URL, '', '850', '400', 'yes', 'center');
}


function openWindow(mypage, myname, w, h, scroll, pos) {
    var win = null;
    if (pos == "random") {
        LeftPosition = (screen.width) ? Math.floor(Math.random() * (screen.width - w)) : 100;
        TopPosition = (screen.height) ? Math.floor(Math.random() * ((screen.height - h) - 75)) : 100;
    }
    if (pos == "center") {
        LeftPosition = (screen.width) ? (screen.width - w) / 2 : 100;
        TopPosition = (screen.height) ? (screen.height - h) / 2 : 100;
    }
    else if ((pos != "center" && pos != "random") || pos == null) {
        LeftPosition = 0;
        TopPosition = 20
    }
    settings = 'width=' + w + ',height=' + h + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=' + scroll + ',location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes';
    win = window.open(mypage, myname, settings);
}




