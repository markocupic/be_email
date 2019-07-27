window.addEvent('domready', function () {

    // Get class instance
    ContaoBeEmailListingAutocomplete = new ContaoBeEmailListingAutocomplete();
    ContaoBeEmailListingAutocomplete.init();

    $$('[id*="ctrl_recipients"]').addEvent('keyup', function (event) {
        ContaoBeEmailListingAutocomplete.cleanAddressString(this);
    });

});


/**
 * ContaoBeEmailListingAutocomplete
 * @type {Type}
 */
ContaoBeEmailListingAutocomplete = new Class(
    {
        /**
         * string
         */
        emailString: null,

        /**
         * Called on domready
         */
        init: function () {
            $$('[id*="ctrl_recipients"]').addClass('awesomplete');
            this.loadEmailAddresses();
        },

        /**
         * Load email addresses from server
         */
        loadEmailAddresses: function () {
            var self = this;
            // Load addresses
            new Request.JSON({
                url: window.location.href,
                onSuccess: function (json, txt) {

                    self.emailString = json.emailString;
                    $$('input.awesomplete').each(function (el) {
                        el.setProperty('data-multiple', 'true');
                        el.setProperty('data-list', self.emailString);

                        var input = document.getElementById(el.id);

                        // Move cursor to the end of input
                        input.addEventListener('awesomplete-selectcomplete', function (e) {
                            self.moveCursorToEnd(input);
                        });

                        awesomplete = new Awesomplete(input,
                            {
                                filter: function (text, input) {
                                    return Awesomplete.FILTER_CONTAINS(text, input.match(/[^;]*$/)[0]);
                                },

                                item: function (text, input) {
                                    return Awesomplete.ITEM(text, input.match(/[^;]*$/)[0]);
                                },

                                replace: function (text) {
                                    var before = this.input.value.match(/^.+;\s*|/)[0];
                                    this.input.value = before + text + ";";
                                }

                            }
                        );


                    });
                }
            }).post({
                'action': 'loadEmailAddresses',
                'REQUEST_TOKEN': Contao.request_token
            });
        },

        /**
         * Clean String
         * @param el
         * @returns {*}
         */
        cleanAddressString: function (el) {
            var str = String.from(el.get('value'));
            str = str.replace(' ', '').replace(' ', '').replace(' ', '').replace(',', ';').replace(',', ';');
            el.set('value', str);
            return el;

        },

        /**
         * Move cursor to the end
         * @param el
         */
        moveCursorToEnd: function (el) {

            el = this.cleanAddressString(el);

            // Scroll to the very end of the input field
            var value = el.get('value');
            el.focus();
            el.scrollLeft = el.scrollWidth;
        }
    }
);

