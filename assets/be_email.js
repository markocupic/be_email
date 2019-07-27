window.addEvent('domready', function () {

    // Get class instance
    ContaoBeEmail = new ContaoBeEmail();

    // Load language file
    new Request.JSON({
        url: window.location.href,
        onSuccess: function (json, txt) {
            ContaoBeEmail.addressBook = json.content;
            ContaoBeEmail.lang = json.lang;
            ContaoBeEmail.init();
        }
    }).post({
        'action': 'loadData',
        'REQUEST_TOKEN': Contao.request_token
    });
});


/**
 * ContaoBackendEmail
 * @type {Type}
 */
ContaoBeEmail = new Class(
    {
        /**
         * addressBook (html)
         */
        addressBook: null,

        /**
         * language data/labels
         */
        lang: null,

        /**
         * target input field
         * can be ctrl_recipientsTo, ctrl_recipientsCc or ctrl_recipientsBcc
         */
        inputTarget: null,

        /**
         * called on domready
         */
        init: function () {
            var self = this;

            // Insert @.icon before to-, cc- and bcc input fields
            ['ctrl_recipientsTo', 'ctrl_recipientsCc', 'ctrl_recipientsBcc'].each(function (selector) {
                if (document.getElementById(selector) !== null) {
                    var icon = new Element('img', {
                        'class': 'open-address-book-icon',
                        'role': 'button',
                        'src': '/../system/modules/be_email/assets/phone-book.svg',
                        'data-input-field': 'ctrl_recipientsTo',
                        'title': self.lang['add_recipients']
                    });

                    // Add event to icon
                    icon.addEvent('click', function () {
                        self.inputTarget = selector;
                    });

                    // Add other event to icon
                    icon.addEvent('click', function () {
                        if (self.addressBook !== null) {
                            // Open modal on click
                            var modalWidth = window.innerWidth < 900 ? Math.floor(0.9 * window.innerWidth) : 900;
                            Backend.openModalWindow(modalWidth, self.lang['address_book'], self.addressBook);
                            document.id('simple-modal').addClass('contao-be-email-modal');

                            // Handle tab visibility
                            $$('#contaoBeEmailAddressBook .tabgroup > div').each(function (el) {
                                el.setStyle('display', 'none');
                            });
                            $$('#contaoBeEmailAddressBook .tabgroup > div')[0].setStyle('display', 'block');

                            // Add active class to first child
                            $$('#contaoBeEmailAddressBook .tabs a')[0].addClass('active');
                        }
                    });

                    var inputField = document.id(selector);
                    icon.inject(inputField, 'before');
                }
            });

        },

        /**
         * Filter rows
         * @param inputText
         */
        filterName: function (inputText) {

            var arrLists = ['userBox', 'memberBox'];
            arrLists.each(function (idSelector) {
                if (document.id(idSelector)) {
                    if (inputText.value == '') {
                        $$('#' + idSelector + ' tr').each(function (el) {
                            el.setStyle('display', 'block');
                        });
                        return;
                    }

                    $$('#' + idSelector + ' tr').each(function (el) {
                        var dataName = el.getProperty('data-name');
                        var regExp = new RegExp('' + inputText.value, 'gi');
                        var res = dataName.match(regExp);
                        if (res === null) {
                            el.setStyle('display', 'none');
                        } else {
                            el.setStyle('display', 'block');
                        }
                    });
                }
            });
        },

        /**
         *
         * @param email
         * @param elButton
         */
        sendmail: function (email, elButton) {
            var self = this;
            el_form = document.id('tl_be_email');
            var addrInput = el_form[self.inputTarget];
            if (addrInput) {
                if (email) {
                    addrInput.value = email + '; ' + addrInput.value;
                }
                else {
                    console.log('Es wurde für diesen Eintrag keine E-Mail-Adresse hinterlegt.');
                }
            }
            else {
                console.log('Das Adressbuch funktioniert nur beim Schreiben einer E-Mail. ("to" fehlt)!');
            }
            // Remove button
            var remElement = (elButton.parentNode).removeChild(elButton);
        },
        
        /**
         *
         * @param el
         * @returns {boolean}
         */
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
    }
);
