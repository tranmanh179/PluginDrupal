const messaging = firebase.messaging();

messaging.requestPermission()
.then(function() {
  console.log('Notification permission granted.');
  // TODO(developer): Retrieve an Instance ID token for use with FCM.
  resetUI();
})
.catch(function(err) {
  console.log('Unable to get permission to notify.', err);
});

messaging.onMessage(function(payload) {
    console.log("Message received. ", payload);
    // [START_EXCLUDE]
    // Update the UI to include the received message.
    appendMessage(payload);
    // [END_EXCLUDE]
});

function resetUI() {
    // [START get_token]
    // Get Instance ID token. Initially this makes a network call, once retrieved
    // subsequent calls to getToken will return from cache.
   
    messaging.getToken()
        .then(function(currentToken) {
            console.log("Token Device: "+currentToken);
            if (currentToken) {
                sendTokenToServer(currentToken);
                //updateUIForPushEnabled(currentToken);
            } else {
                // Show permission request.
                //updateUIForPushPermissionRequired();
                console.log('No Instance ID token available. Request permission to generate one.');
                notifyMe();
            }
        })
        .catch(function(err) {
            console.log('An error occurred while retrieving token. ', err);
        });
}

function sendTokenToServer(currentToken) {
    jQuery.ajax({
        method: "POST",
        url: "/notification-firebase-save-token-device",
        data: { token:currentToken }
    }).done(function( msg ) {
        
        if(parseInt(msg)==1){
            //location.reload(); 
        }
    });
}

function notifyMe() {
  // Let's check if the browser supports notifications
  if (!("Notification" in window)) {
    //alert("This browser does not support desktop notification");
  }

  // Let's check whether notification permissions have already been granted
  else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    //var notification = new Notification("Hi there!");
  }

  // Otherwise, we need to ask the user for permission
  else if (Notification.permission !== "denied") {
    Notification.requestPermission().then(function (permission) {
      // If the user accepts, let's create a notification
      if (permission === "granted") {
        //var notification = new Notification("Hi there!");
      }
    });
  }

  // At last, if the user has denied notifications, and you 
  // want to be respectful there is no need to bother them any more.
}