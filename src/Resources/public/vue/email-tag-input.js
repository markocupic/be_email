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

class EmailTagInput {
  constructor(el, value) {
    this.el = el;
    this.value = value;
    this.init();
  }

  init() {
    new Vue({
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
         * The values as a comma separated string
         */
        value: this.value,

        /**
         * The suggestion array
         */
        arrSuggestions: [],

        /**
         * Store the index of the focused element in the
         * suggestion list
         */
        intFocusedSuggestion: -1,
      },

      /**
       * Initialize app
       */
      created: function () {
        const self = this;
        // Remove empty values
        self.arrValues = self.value.split(',').filter(el => {
          return el != null && el !== '';
        });


        // Do not submit form when pressing the Enter key
        document.addEventListener("DOMContentLoaded", () => {
          ['save', 'saveNback'].forEach(el => {
            const saveBtn = document.getElementById(el);
            if (saveBtn) {
              saveBtn.setAttribute('type', 'button');
              saveBtn.addEventListener("mouseenter", () => saveBtn.removeAttribute('type'));
              saveBtn.addEventListener("mouseout", () => saveBtn.setAttribute('type', 'button'));
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

        intFocusedSuggestion(val, oldVal) {
          const self = this;
          window.setTimeout(() => {
            if (self.arrSuggestions.length) {
              const listItems = self.$el.querySelectorAll('.ti-suggestion-list [data-is-focusable="true"]');
              listItems.forEach(el => el.classList.remove('has-focus'));

              if (!listItems[val] && val < 0) {
                self.intFocusedSuggestion = 0;
              } else if (!listItems[val] && val > 0) {
                self.intFocusedSuggestion = oldVal;
              } else if (listItems[val]) {
                listItems[val].classList.add('has-focus');
              }
            } else {
              self.intFocusedSuggestion = -1;
            }
          }, 10);
        }
      },
      methods: {

        /**
         * Push new value to arrValues
         * and clear input field
         * @param e
         */
        pushValue: function pushValue(e) {
          const self = this;
          const value = e.target.value;
          if (self.validateEmail(value)) {
            self.arrValues.push(value);
            window.setTimeout(() => self.valueNew = '', 10);
          }
        },

        /**
         * Remove a certain tag from the tag container
         * @param e
         */
        removeTag: function removeTag(e) {
          const self = this;
          let arrTagClass = e.target.parentElement.className.split(' ');
          arrTagClass = arrTagClass.map(cl => '.' + cl);
          const strClass = arrTagClass.join('');
          const tag = e.target.parentElement;
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
          window.setTimeout(() => {
            self.arrSuggestions = [];
            self.intFocusedSuggestion = -1;
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
         * @param e
         */
        handleKeypress: function runAutocompconste(e) {
          const self = this;
          if (e.key === 'Backspace') {
            if (!self.valueNew.length && self.arrValues.length) {
              self.removeItemFromIndex(self.arrValues.length - 1);
              self.arrSuggestions = [];
            }
          } else if (e.key === 'ArrowDown') {
            self.intFocusedSuggestion++;
          } else if (e.key === 'ArrowUp') {
            self.intFocusedSuggestion--;
          } else if (e.key === 'Enter') {
            const elFocus = document.querySelector('[data-is-focusable="true"].has-focus');
            if (elFocus) {
              self.selectAddress(elFocus.getAttribute('data-value'));
              self.arrSuggestions = [];
            }
          } else if (e.key === ';' || e.key === ',') {
            const elFocus = document.querySelector('[data-is-focusable="true"].has-focus');
            if (elFocus) {
              self.arrSuggestions = [];
            }
            self.pushValue(e);
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
            self.intFocusedSuggestion = -1;
            self.arrSuggestions = [];
            return null;
          }
          // Get data from remote
          new Request.JSON({
            url: window.location.href,
            onSuccess: json => {
              self.arrSuggestions = json['data'];
              if (json['data'].length) {
                self.intFocusedSuggestion = 0;
              } else {
                self.intFocusedSuggestion = -1;
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
         */
        selectAddress: function selectAddress(value) {
          const self = this;
          self.arrValues.push(value);
          self.valueNew = '';
          self.arrSuggestions = [];
        },

        /**
         * Validate email addresses
         * @param value
         * @returns {boolean}
         */
        validateEmail: function validateEmail(value) {
          const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(String(value).toLowerCase());
        }
      }
    })
  }
}
