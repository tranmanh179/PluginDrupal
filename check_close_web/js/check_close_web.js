var checkChangeValue = false;
jQuery('input').change(function () {
  checkChangeValue = true;
});

window.addEventListener("beforeunload", function (e) {
  var confirmationMessage = "\o/";
  if (checkChangeValue) {
    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
    return confirmationMessage;                            //Webkit, Safari, Chrome
  }
});
