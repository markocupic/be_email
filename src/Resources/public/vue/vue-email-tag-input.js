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
      },
      created: function () {
        let self = this;
        this.arrRecipients = this.value.split(',');

        // Do not submit form when pressing the Enter key
        let saveBtn = document.getElementById('save');
        if (saveBtn) {
          saveBtn.setAttribute('disabled', true);
          saveBtn.addEventListener('mouseover', function () {
            this.removeAttribute('disabled');
          });
          saveBtn.addEventListener('mouseout', function () {
            this.setAttribute('disabled', true);
          });
        }
      },
      watch: {
        value: function (val, oldVal) {
          this.arrRecipients = val.split(',');
        }
      },
      methods: {

        onBlur: function onBlur() {
          self = this;
          let btn = document.getElementById('save');
          if (btn) {
            btn.removeAttribute('disabled');
            window.setTimeout(function () {
              self.arrSuggestions = [];
            }, 200);
          }
        },
        runAutocomplete: function runAutocomplete(e) {
          if (e.key === 'Enter') {
            if (this.arrSuggestions.length) {
              let arrTemp = this.arrSuggestions.shift();
              this.selectAddress(arrTemp['email']);
              this.arrSuggestions = [];
            }
          } else {
            //this.value = this.value.replace(' ', '');
            this.value = this.value.replace(';', ',');
            this.arrRecipients = this.value.split(',');
            this.getAddresses();
          }
        },
        getAddresses: function getAddresses() {
          let self = this;
          if (!self.arrRecipients.length) {
            return null;
          }

          let strEmail = self.arrRecipients[self.arrRecipients.length - 1];
          if (strEmail.length < 3) {
            this.arrSuggestions = [];
            return null;
          }

          new Request.JSON({
            url: window.location.href,
            onSuccess: function (json, txt) {
              self.arrSuggestions = json.emailList;
            }
          }).post({
            'action': 'loadEmailList',
            'pattern': strEmail,
            'REQUEST_TOKEN': Contao.request_token
          });
        },
        selectAddress: function selectAddress(email) {
          // remove last item
          this.arrRecipients.pop();
          this.value = this.arrRecipients.join(',') + ',' + email;
          // Remove first character if it is comma
          this.value = this.value.replace(/^,/, "");

          this.arrSuggestions = [];
        }
      }
    })
  }
}
