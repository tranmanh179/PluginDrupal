// create popup
if (!("Notification" in window)) {
    //alert("This browser does not support desktop notification");
}else if (Notification.permission === "granted") {
    // If it's okay let's create a notification
    //var notification = new Notification("Hi there!");
}else if (Notification.permission !== "denied") {
    var htmlPopup= '<div id="showPopupNotificationFirebase" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Thông báo</h4><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><div class="showMess" id="textAlert">Vui lòng cấp quyền nhận thông báo trên trình duyệt để sử dụng chức năng Notification Firebase</div></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal" onclick="notifyMe();">Xác nhận</button></div></div></div></div>';
	jQuery('body').append( htmlPopup );
	jQuery('#showPopupNotificationFirebase').modal('show');
}

// Your web app's Firebase configuration
var firebaseConfig = {
	apiKey: "AIzaSyCOLLFkSfonySClGqoeAgKJys8xYRiaKzk",
	projectId: "manmo-house",
	messagingSenderId: "794054517344",
	appId: "1:794054517344:web:9768d04f16a6c0f66c5905"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);
