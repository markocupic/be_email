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
    const app = new Vue({
      el: '#' + this.el,
      data: {
        /**
         * Stores the value for new inputs
         */
        valueNew: '',

        /**
         * The value array
         */
        arrValues: [],

        /**
         * The values as a string
         */
        value: this.value,

        /**
         * The suggestion array
         */
        arrSuggest: [],

        /**
         * Store the index of the focused element in the
         * suggestion list
         */
        intFocus: -1,
      },
      /**
       * Initialize app
       */
      created: function () {
        // Remove empty values
        this.arrValues = this.value.split(',').filter(el => {
          return el != null && el != '';
        });

        // Do not submit form when pressing the Enter key
        document.addEventListener("DOMContentLoaded", function () {
          ['save', 'saveNback'].forEach(el => {
            const saveBtn = document.getElementById(el);
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
        valueNew: function (val) {
          this.getSuggestValuesFromRemote();
        },

        arrValues: function (val) {
          this.value = val.join(',');
        },

        intFocus(val, oldVal) {
          const self = this;
          window.setTimeout(function () {
            if (self.arrSuggest.length) {
              const listItems = self.$el.querySelectorAll('.ti-suggest-list [data-is-focusable="true"]');
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
        /**
         * Push new value to arrValues
         * and clear input field
         * @param event
         */
        pushValue: function pushValue(event) {
          self = this;
          const value = event.target.value;
          if (self.validateEmail(value)) {
            self.arrValues.push(value);
            self.valueNew = '';
          }
        },
        /**
         * Remove a certain tag from the tag container
         * @param event
         */
        removeTag: function removeTag(event) {
          self = this;
          let arrTagClass = event.target.parentElement.className.split(' ');
          arrTagClass = arrTagClass.map(cl => '.' + cl);
          const strClass = arrTagClass.join('');
          const tag = event.target.parentElement;
          const container = tag.parentElement;
          const tagCollection = container.querySelectorAll(strClass);
          const index = Array.prototype.indexOf.call(tagCollection, tag);
          self.removeItemFromIndex(index);
        },
        /**
         * Remove item with a certain index
         * @param index
         */
        removeItemFromIndex: function removeItemFromIndex(index) {
          const self = this;
          if (self.arrValues[index]) {
            self.arrValues.splice(index, 1);
          }
        },
        /**
         * Close suggestion box box on blur
         */
        closeSuggestList: function closeSuggestList() {
          const self = this;
          window.setTimeout(function () {
            self.arrSuggest = [];
            self.intFocus = -1;
          }, 100);
        },
        /**
         * Close suggestion box box on blur
         */
        onBlur: function onBlur() {
          this.closeSuggestList();
        },
        /**
         * Handle keypress events
         * @param event
         */
        handleKeypress: function runAutocompconste(event) {
          const self = this;
          if (event.key === 'Backspace') {
            if (!self.valueNew.length && self.arrValues.length) {
              self.removeItemFromIndex(self.arrValues.length - 1);
              self.arrSuggest = [];
            }
            return;
          } else if (event.key === 'ArrowDown') {
            self.intFocus++;
            return;
          } else if (event.key === 'ArrowUp') {
            self.intFocus--;
            return;
          } else if (event.key === 'Enter') {
            const elFocus = document.querySelector('[data-is-focusable="true"].has-focus');
            if (elFocus) {
              self.selectAddress(elFocus.getAttribute('data-value'), self.intFocus);
              self.arrSuggest = [];
            }
            return;
          } else {
            //
          }

        },
        /**
         * Get suggestions from remote
         * using Contao executePreActions Hook
         * @returns {null}
         */
        getSuggestValuesFromRemote: function getSuggestValuesFromRemote() {
          const self = this;

          if (self.valueNew.length < 3) {
            self.intFocus = -1;
            self.arrSuggest = [];
            return null;
          }
          // Get data from remote
          new Request.JSON({
            url: window.location.href,
            onSuccess: function (json) {
              self.arrSuggest = json['data'];
              if (json['data'].length) {
                self.intFocus = 0;
              } else {
                self.intFocus = -1;
              }
            }
          }).post({
            'action': 'loadEmailList',
            'pattern': self.valueNew,
            'REQUEST_TOKEN': Contao.request_token
          });
        },
        /**
         * Push new values to arrValues
         * @param value
         * @param index
         */
        selectAddress: function selectAddress(value, index) {
          const self = this;
          self.arrValues.push(value);
          self.valueNew = '';
          self.arrSuggest = [];
        },
        /**
         * Validate email addresses
         * @param email
         * @returns {boolean}
         */
        validateEmail: function validateEmail(value) {
          const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(String(value).toLowerCase());
        }
      }
    })
  }
}
