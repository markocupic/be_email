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
        valueNew: '',
        value: this.value,
        arrValues: [],
        arrSuggestions: [],
        intFocus: -1,
      },

      created: function () {
        this.arrValues = this.value.split(',').filter(el => {
          return el != null && el != '';
        });

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
        valueNew: function (val) {
          this.getAddresses();
        },

        arrValues: function (val) {
          this.value = val.join(',');
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

        pushValue: function pushValue(event) {
          self = this;
          let value = event.target.value;
          if (self.validateEmail(value)) {
            self.arrValues.push(value);
            self.valueNew = '';
          }
        },
        removeTag: function removeTag(event)
        {
          self = this;
          let arrTagClass = event.target.parentElement.className.split(' ');
          arrTagClass = arrTagClass.map(cl => '.' + cl);
          let strClass = arrTagClass.join('');
          let tag = event.target.parentElement;
          let container = tag.parentElement;
          let tagCollection = container.querySelectorAll(strClass);
          let index = Array.prototype.indexOf.call(tagCollection, tag);
          //tagCollection[index].remove();
          self.arrValues.splice(index,1);
        },

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
              self.selectAddress(elFocus.getAttribute('data-email'), self.intFocus);
              self.arrSuggestions = [];
            }
          } else {
            //
          }
        },

        getAddresses: function getAddresses() {
          let self = this;
          if (!self.arrValues.length) {
            //self.intFocus = -1;
            //self.arrSuggestions = [];
            //return null;
          }

          if (self.valueNew.length < 3) {
            self.intFocus = -1;
            self.arrSuggestions = [];
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
            'pattern': self.valueNew,
            'REQUEST_TOKEN': Contao.request_token
          });
        },

        selectAddress: function selectAddress(email, index) {
          // Push new item
          this.arrValues.push(email);
          this.valueNew = '';
          this.arrSuggestions = [];
        },
        validateEmail: function validateEmail(email) {
          const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return re.test(String(email).toLowerCase());
        }
      }
    })
  }
}
