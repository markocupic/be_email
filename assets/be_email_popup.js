function sendmail(email) {
       var p_opener = opener.parent;
       try {
              if ((!p_opener) || (p_opener.closed)) {
                     alert('Das Mail-Fenster wurde bereits geschlossen!');
                     window.close();
              }


              el_form = p_opener.document.id('tl_be_email');
              if (el_form) {
                     el_form.action = 'contao/main.php?do=tl_be_email&mode=2&pid=' + getParam('pid') + '&act=edit&id=' + getParam('id') + '&rt=' + getParam('rt') + '&ref=' + getParam('ref');

                     if (getParam('dest') == 'to') var addrInput = el_form.recipientsTo;
                     if (getParam('dest') == 'cc') var addrInput = el_form.recipientsCc;
                     if (getParam('dest') == 'bcc') var addrInput = el_form.recipientsBcc;

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
                            window.close();
                     }
              }
              else {
                     alert('Das Adressbuch funktioniert nur beim Schreiben einer E-Mail. (Fenster fehlt)!');
                     window.close();
              }
       }
       catch (e) {
              alert('Das Mail-Fenster wurde bereits geschlossen!');
              window.close();
       }

}

function removeElement(el) {
       var remElement = (el.parentNode).removeChild(el);
}


function getParam(variable) {
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i = 0; i < vars.length; i++) {
              var pair = vars[i].split("=");
              if (pair[0] == variable) {
                     return pair[1];
              }
       }
       return (false);
}