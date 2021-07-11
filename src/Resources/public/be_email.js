/**
 * Backend Email Web Plugin for Contao
 * Copyright (c) 20012-2019 Marko Cupic
 * @package be_email
 * @author Marko Cupic m.cupic@gmx.ch, 2012-2019
 * @link https://github.com/markocupic/be_email
 * @license MIT
 */
window.addEvent('domready', function () {

    const inputTo = document.querySelectorAll("#ctrl_recipientsTo,#ctrl_recipientsCc,#ctrl_recipientsBcc");
    if(null !== inputTo)
    {
        let disable;

        inputTo.forEach(element => {
            element.addEventListener("focus", function(event) {
                disable = true;
                disableBtn();
            });
            element.addEventListener("blur", function(event) {
                disable = false;
                disableBtn();
            });
        });

        function disableBtn()
        {
            let btn = document.getElementById('save');
            if(btn)
            {
                if(disable)
                {
                    btn.setAttribute('disabled',true);
                }else{
                    btn.removeAttribute('disabled');
                }
            }
        }

    }




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
                if (document.id(selector) !== null) {
                    var icon = new Element('img', {
                        'class': 'open-address-book-icon',
                        'role': 'button',
                        'src': '/bundles/markocupicbeemail/phone-book.svg',
                        'data-input-field': 'ctrl_recipientsTo',
                        'title': self.lang['add_recipients'],
                        'onclick': 'ContaoBeEmail.openAddressBook(\'' + selector + '\')'
                    });

                    // Add other event to icon
                    icon.addEvent('click', function () {

                    });

                    var inputField = document.id(selector);
                    icon.inject(inputField, 'before');
                }
            });

        },

        /**
         * Open the address book
         * This function is triggered, when the icon has been clicked
         * @param selector
         */
        openAddressBook: function (selector) {
            var self = this;
            if (self.addressBook !== null) {
                // Set target input field (to, cc or bcc)
                self.inputTarget = selector;

                // Open modal on click
                var modalWidth = window.innerWidth < 900 ? Math.floor(0.9 * window.innerWidth) : 900;
                Backend.openModalWindow(modalWidth, self.lang['address_book'], self.addressBook);

                document.id('simple-modal').addClass('contao-be-email-modal');

                // Set focus to the search input field
                document.id('ctrlSearchForName').focus();

                // Handle tab visibility
                $$('#contaoBeEmailAddressBook .tabgroup > div').each(function (el) {
                    el.setStyle('display', 'none');
                });
                $$('#contaoBeEmailAddressBook .tabgroup > div')[0].setStyle('display', 'block');

                // Add active class to first child
                $$('#contaoBeEmailAddressBook .tabs a')[0].addClass('active');
            }
        },

        /**
         * Filter rows
         * @param inputText
         */
        filterName: function (inputText) {

            var queryText = inputText.value;

            ['userBox', 'memberBox'].each(function (idSelector) {

                // Set timout to not overload the system
                window.setTimeout(function () {
                    if (document.id(idSelector)) {
                        if (inputText.value == queryText) {

                            if (queryText == '') {
                                var i = 0;
                                $$('#' + idSelector + ' tr').each(function (el) {
                                    el.setStyle('display', 'block');
                                    i++;
                                });
                                $$('#' + idSelector + ' .rowCount').set('text', i);
                                return;
                            }

                            var i = 0;
                            $$('#' + idSelector + ' tr').each(function (el) {
                                var dataName = el.getProperty('data-name');
                                var regExp = new RegExp('' + inputText.value, 'gi');
                                var res = dataName.match(regExp);
                                if (res === null) {
                                    el.setStyle('display', 'none');
                                } else {
                                    el.setStyle('display', 'block');
                                    i++;
                                }
                            });
                            $$('#' + idSelector + ' .rowCount').set('text', i);
                        }
                    }
                }, 500);

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
