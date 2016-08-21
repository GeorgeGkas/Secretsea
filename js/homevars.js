var enviromentData = getJSON();

var friends_images = enviromentData.FriendsAvatar;
var personalEmail = enviromentData.UserEmail;
var myvar = enviromentData.Contacts;
var friends_to_talk = enviromentData.FriendsIWantToTalk;
var friends_who_want_to_talk_to_you = enviromentData.FriendsReadyToChatWithMe;

function getJSON() {
    var fpNamePath = window.location.href.substring(
        0, window.location.href.lastIndexOf("/") + 1
    ) + 'json/HV-' + readCookie('fp') + '.json';
    var rdata = {};
    $.ajax({
        url: fpNamePath,
        async: false,
        success: function(data) {
            rdata = data;
            //eraseCookie('fp');
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });

    return rdata;
}
