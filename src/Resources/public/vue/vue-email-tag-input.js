/*
 * This file is part of Be Email.
 *
 * (c) Marko Cupic 2021 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/be_email
 */

'use strict';

class VueEmailTagInput {
  constructor(el, value) {
    this.el = el;
    this.value = value;
    this.init();
  }

  init() {
    let app = new Vue({
      el: '#' + this.el,
      data: {
        value: this.value,
        arrRecipients: [],
        arrSuggestions: [],
        intFocus: -1,
      },
      created: function () {
        this.arrRecipients = this.value.split(',');

        // Do not submit form when pressing the Enter key
        document.addEventListener("DOMContentLoaded", function () {
          ['save', 'saveNback'].forEach(el => {
            let saveBtn = document.getElementById(el);
            if (saveBtn) {
              saveBtn.setAttribute('type', 'button');
              saveBtn.addEventListener("mouseenter", function () {
                saveBtn.removeAttribute('type');
              });
              saveBtn.addEventListener("mouseout", function () {
                saveBtn.setAttribute('type', 'button');
              });
            }
          })
        });
      },
      watch: {
        value: function (val) {
          this.arrRecipients = val.split(',');
        },
        intFocus(val, oldVal) {
          let self = this;
          window.setTimeout(function () {
            if (self.arrSuggestions.length) {
              let listItems = self.$el.querySelectorAll('.esl li button');
              listItems.forEach(el => el.classList.remove('has-focus'));

              if (!listItems[val] && val < 0) {
                self.intFocus = 0;
              } else if (!listItems[val] && val > 0) {
                self.intFocus = oldVal;
              } else if (listItems[val]) {
                listItems[val].classList.add('has-focus');
              }
            } else {
              self.intFocus = -1;
            }
          }, 10);

        }
      },
      methods: {

        closeSuggestList: function closeSuggestList() {
          this.onBlur();
        },
        onBlur: function onBlur() {
          let self = this;
          window.setTimeout(function () {
            self.arrSuggestions = [];
            self.intFocus = -1;
          }, 100);
        },
        handleKeypress: function runAutocomplete(e) {
          let self = this;
          if (e.key === 'ArrowDown') {
            self.intFocus++;

          } else if (e.key === 'ArrowUp') {
            self.intFocus--;
          } else if (e.key === 'Enter') {

            let elFocus = document.querySelector('.has-focus');
            if (elFocus) {
              this.selectAddress(elFocus.getAttribute('data-email'), self.intFocus);
              this.arrSuggestions = [];
            }
            return false;
          } else {
            this.value = this.value.replace(';', ',');
            this.arrRecipients = this.value.split(',');
            this.getAddresses();
          }
        },
        getAddresses: function getAddresses() {

          let self = this;
          if (!self.arrRecipients.length) {
            self.intFocus = -1;
            return null;
          }

          let strEmail = self.arrRecipients[self.arrRecipients.length - 1];
          if (strEmail.length < 3) {
            self.intFocus = -1;
            this.arrSuggestions = [];
            return null;
          }

          new Request.JSON({
            url: window.location.href,
            onSuccess: function (json) {
              self.arrSuggestions = json['emailList'];
              if (json['emailList'].length) {
                self.intFocus = 0;
              } else {
                self.intFocus = -1;
              }
            }
          }).post({
            'action': 'loadEmailList',
            'pattern': strEmail,
            'REQUEST_TOKEN': Contao.request_token
          });
        },
        selectAddress: function selectAddress(email, index) {
          // remove last item
          this.arrRecipients.pop();
          this.value = this.arrRecipients.join(',') + ',' + email;
          // Remove first character if it is comma
          this.value = this.value.replace(/^,/, "");
          this.value = this.value.replace(/[\s]+/, "");

          this.arrSuggestions = [];
        }
      }
    })
  }
}
