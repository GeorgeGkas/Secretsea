//"use strict";
var find_xmlHttp = create_xmlHttpRequestObject();
var add_xmlHttp = create_xmlHttpRequestObject();
var updatedb_xmlHttp = create_xmlHttpRequestObject();
var getOnlineState_xmlHttp = create_xmlHttpRequestObject();
var background_colors = ['#2EABA2', 'rgb(75, 171, 46)', 'rgb(46, 171, 109)', '#6F2EAB', '#AB372E', '#2E99AB', '#AB2E51', '#A4AB2E'];
var OnlineUsers = [];
var KeyValueOnlineUsers = [];
var woking_dir = getWorkingDir();

var isMessaging = false;

var MsgFileName = null;
var MsgFriend = null;
var MsgIdx = 0;
window.lastRecieved = 0;

var prepareToSendMessage = function(event, input) {
    var d = new Date();
    if (event.which === 13) {
        emojis_isShown = false;
        $('.spawn-emonicons').hide();
        //Disable textbox to prevent multiple submit
        $(input).attr("disabled", "disabled");
        var msg = $(input).val();
        $(input).val('');
        var time = d.toDateString();
        var hours = d.getHours();
        if (hours < 10) hours = '0' + hours;
        time = time + ' ' + d.getHours() + ':' + d.getMinutes();
        var response = SendMessage(msg, time);
        if (response !== 'MsgSent' && response !== "MsgEmpty") {
            $('.top-msg').showMsg({
                msg: result['We couldn\'t send your message']
            });
        } else {
            $(input).removeAttr("disabled");
        }
    }
}


function SendMessage(msg, time) {
    var res = null;
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "Message=" + msg + "&PostTime=" + time + "&FriendEmail=" + MsgFriend + "&f=u",
        async: false,
        success: function(data) {
            data = JSON.parse(data);
            if (data['State'] == true) {
                res = data['Msg'];
            } else {
                $('.top-msg').showMsg({
                    msg: result['msg']
                });
            }


        }
    });
    return res;
}



function ShowNewMessages(res, curr, end) {
    // build the front end
    var newdiv1 = "<div class=\"message\"> <div class=\"own-avatar-message\" style=\"background-image: url(" + res[curr][4].replace(/\\\//g, "/") + ");\"></div><div class=\"own-message2\"><div class=\"own-message\"><p>" + escapeHtml(res[curr][3]) + "</p></div><div class=\"message-timestamp\"> " + res[curr][1] + " • " + res[curr][2] + "</div></div> </div>";


    $(newdiv1).insertAfter($(".message").last());
    var height = 0;
    $('.message').each(function(i, value) {
        height += parseInt($(this).height());
    });

    height += '';

    $('div').animate({
        scrollTop: height
    });

    // check if there is another messsage or checkagain for new messages             
    curr++;
    if (curr <= end) ShowNewMessages(res, curr, end);
    else {
        setTimeout(function() {
            GetNewMesssages()
        }, 3000);
    }
}

function GetNewMesssages() {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + MsgFriend + "&LastMsgId=" + window.lastRecieved + "&f=w",
        dataType: 'json',
        async: false,
        success: function(data) {
            // check error state
            if (data[0] != "Error") {
                var start = 0;
                window.lastRecieved = data[data.length - 1];
                if (typeof window.lastRecieved === "undefined" || window.lastRecieved == "undefined") window.lastRecieved = 0;
                if (typeof data[0] !== "number") {
                    var end = data.length - 1; // minus one for the nummber
                    if (end > 0) ShowNewMessages(data, start, end - 1);
                    else setTimeout(function() {
                        GetNewMesssages()
                    }, 3000);
                }

            } //else alert("File does not exist.");
        }
    });
}

function AjaxFindPerson() {
    if (document.getElementById("PersonSearchInput").value != "" && document.getElementById("PersonSearchInput").value != personalEmail && !isInArray(document.getElementById("PersonSearchInput").value, myvar)) {
        person = encodeURIComponent(document.getElementById("PersonSearchInput").value);

        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "FriendEmail=" + person + "&f=o",
            success: function(result) {
                result = JSON.parse(result);
                if (result['found'] != null) {
                    document.getElementById('Label-Result').innerHTML = result['msg'];
                    document.getElementById('UserNameSearchResult').innerHTML = result['found'];
                    $('#add-person').removeAttr("disabled");
                    isFoundPerson = true;
                } else {
                    document.getElementById('Label-Result').innerHTML = result['msg'];
                    document.getElementById('UserNameSearchResult').innerHTML = "";
                    $('#add-person').attr("disabled", "disabled");
                    isFoundPerson = false;
                }
                //setTimeout(AjaxFindPerson, 1000);


            }
        });


    } else {
        document.getElementById('Label-Result').innerHTML = "";
        document.getElementById('UserNameSearchResult').innerHTML = "";
        $('#add-person').attr("disabled", "disabled");

    }
    setTimeout(AjaxFindPerson, 1000);
}

function AddPerson() {
    //if (document.getElementById("PersonSearchInput").value != "" && document.getElementById("PersonSearchInput").value != personalEmail && !isInArray(document.getElementById("PersonSearchInput").value, myvar)) {
    person = encodeURIComponent(document.getElementById("PersonSearchInput").value);

    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + person + "&f=m",
        async: false,
        success: function(result) {
            result = JSON.parse(result);
            if (result['found']) {
                location.reload();
            } else {
                $('.top-msg').showMsg({
                    msg: result['msg']
                });
            }

        }
    });
    //}
}



function UpdateProfilePref() {
    var update_pass = encodeURIComponent(document.getElementById("Ppass").value);
    var update_username = encodeURIComponent(document.getElementById("Puser").value);

    if (update_pass == "" && update_username == "") {
        return false;
    }

    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "pass=" + update_pass + "&username=" + update_username + "&f=d",
        async: false,
        success: function(result) {
            result = JSON.parse(result);
            if (result['Pass'] != null && result['Username'] != null) {
                $('.top-msg').showMsg({
                    msg: '<p> Your username and password updated successfully</p>'
                });
            } else {
                if (result['Pass'] != null) {
                    $('.top-msg').showMsg({
                        msg: result['Pass']['Msg']
                    });
                } else {
                    $('.top-msg').showMsg({
                        msg: result['Username']['Msg']
                    });
                }
            }
        }
    });

}


var active_members = [];

function GetOnlineState() {
    if (!isMessaging) {
        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "f=r",
            success: function(result) {
                result = JSON.parse(result);
                if (result.length != 2) {
                    for (var i = 1; i < result.length - 1; i++) {
                        var div = document.getElementById("SeaUser-" + result[i].replace(/\@/, '\-'));
                        $(div.children[0]).css("background-color", "rgb(48,212,67)");
                        $(div.children[0]).attr('title', 'Online');





                        if (!(isInArray(result[i], active_members))) {
                            active_members.push(result[i]);
                        }

                        if (!(isInArray(result[i], KeyValueOnlineUsers))) {
                            KeyValueOnlineUsers.push(result[i]);
                        }



                    }

                    for (var i = 0; i < active_members.length; ++i) {
                        if (!(isInArray(active_members[i], result))) {
                            var div = document.getElementById("SeaUser-" + active_members[i].replace(/\@/, '\-'));
                            $(div.children[0]).css("background-color", "rgba(75, 87, 76, 0.20)");
                            $(div.children[0]).attr('title', 'Offline');

                            DelElemArray(KeyValueOnlineUsers, active_members[i]);

                        }
                    }



                } else {
                    if (active_members != null) {
                        for (person of active_members) {
                            var div = document.getElementById("SeaUser-" + person.replace(/\@/, '\-'));
                            $(div.children[0]).css("background-color", "rgba(75, 87, 76, 0.20)");
                            $(div.children[0]).attr('title', 'Offline');
                        }
                        active_members = [];
                        KeyValueOnlineUsers = [];
                    }

                }


                if (!isMessaging) setTimeout(GetOnlineState, 2000);


            }
        });
    } else {
        setTimeout(GetOnlineState, 2000);
    }
}




function DropTheChatSeason() {

    DeleteFileSystem();
    $('.inside-the-ocean:not(first-child)').remove();
    $('#message-wrapper').fadeOut(300, function() {
        $('.homeWindow, .new-room').fadeIn(300);
        isMessaging = false;
        MsgFriend = null;
        MsgIdx = 0;
        MsgFileName = null;
        window.lastRecieved = 0;

        GetOnlineState();
    });


}

function checkMessagingState() {
    //check if the two person are conected, and want to chat every 2 sec

    //if not drop the one who still is chatting and clear the arary values both the database


    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + MsgFriend + "&f=v",
        cache: false,
        success: function(data) {
            data = JSON.parse(data);
            if (data['State'] == false) {
                if (isMessaging) DropTheChatSeason();
            } else if (isMessaging) setTimeout(function() {
                checkMessagingState()
            }, 2000);

        },
        error: function() {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
            if (isMessaging) setTimeout(function() {
                checkMessagingState()
            }, 2000);
        }
    });
}

function StartChattingProcedure() {

    //ajax send to isMsgWith Querry the email of the person who talks to
    var newChat = '<div class="inside-the-ocean"><div class="message"></div></div>';
    if ($('.inside-the-ocean').length <= 0) $('#message-wrapper').append(newChat);
    /*with sucess -->*/
    $('.homeWindow, .new-room').fadeOut(300, function() {
        $('#message-wrapper').fadeIn(300);
        isMessaging = true;
        checkMessagingState();
        setTimeout(function() {
            GetNewMesssages()
        }, 3000);



    });

}

function DeleteFileSystem() {
    //var fileDeleted = null;
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + MsgFriend + "&f=t",
        async: false,
        success: function(data) {
            data = JSON.parse(data);
            if (data['State'] != false) {
                //fileDeleted = 'okay';
                MsgFileName = null;

            } else {
                $('.top-msg').showMsg({
                    msg: data['Msg']
                });
            }

        }
    });
    //return fileDeleted;
}

function PrepareFileSystem() {
    var fileCreated = null;
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + MsgFriend + "&f=s",
        async: false,
        success: function(data) {
            data = JSON.parse(data);
            if (data['State'] != false) {
                fileCreated = 'okay';
                MsgFileName = data['Msg'];

            } else {
                $('.top-msg').showMsg({
                    msg: data['Msg']
                });
            }

        }
    });
    return fileCreated;

}

function GoAndChat(chatGate) {
    var PersonId = chatGate.id.substring(chatGate.id.indexOf('#') + 1);

    var PersonEmail = myvar[PersonId];
    // check to db insteed!
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + PersonEmail + "&f=v",
        cache: false,
        success: function(data) {
            data = JSON.parse(data);
            if (data['State'] == true) {
                MsgFriend = PersonEmail;
                var fileCreated = PrepareFileSystem();
                if (fileCreated != null) {
                    StartChattingProcedure();

                }

            }

        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });


}


function changeOnlineState(onlineCheckbox) {
    var PersonId = onlineCheckbox.id.substring(onlineCheckbox.id.indexOf('#') + 1);
    var PersonEmail = myvar[PersonId];
    var state;

    if (onlineCheckbox.checked) {
        state = 'online';
    } else {
        state = 'offline';
    }

    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "FriendEmail=" + PersonEmail + "&UserStatus=" + state + "&f=q",
        cache: false,
        async: false,
        success: function(data) {
            if (data == 'online') {
                OnlineUsers.push(PersonEmail);
            } else {
                var idx = myvar.indexOf(PersonEmail);
                if (document.getElementById("ReadyToChat#" + idx) !== null) $(document.getElementById("ReadyToChat#" + idx)).remove();
                DelElemArray(OnlineUsers, PersonEmail);

                DeleteFileSystem();
                $('.inside-the-ocean:not(first-child)').remove();
                isMessaging = false;
                MsgFriend = null;
                MsgIdx = 0;
                MsgFileName = null;
                window.lastRecieved = 0;
                GetOnlineState();


            }
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });

}

function DeleteUser(elem) {
    var idx = elem.id.substring(elem.id.indexOf('#') + 1);
    var PersonEmail = myvar[idx];
    var r = confirm("Delete user " + PersonEmail + " ?");

    if (r == true) {
        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "FriendEmail=" + PersonEmail + "&f=n",
            cache: false,
            async: false,
            success: function(data) {

                $(elem).parent().parent().remove();
                //DelElemArray(myvar, PersonEmail);
                $('.top-msg').showMsg({
                    msg: data
                });
            },
            error: function(er) {
                var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
                $('.top-msg').showMsg({
                    msg: error
                });
            }
        });
    }
}

function CheckIfAbletoChat() {
    for (var i = 0; i < OnlineUsers.length; ++i) {
        if (isInArray(OnlineUsers[i], KeyValueOnlineUsers)) {
            var idx = myvar.indexOf(OnlineUsers[i]);
            if (document.getElementById("ReadyToChat#" + idx) === null) {
                var divtoadd = '<div class="ReadyToChat" id="ReadyToChat#' + idx + '" onclick="GoAndChat(this);">@</div>';
                $(document.getElementById("SeaUser-" + OnlineUsers[i].replace(/\@/, '\-'))).parent().append(divtoadd);

            }

        } else {
            var idx = myvar.indexOf(OnlineUsers[i]);
            if (document.getElementById("ReadyToChat#" + idx) !== null) {
                $(document.getElementById("ReadyToChat#" + idx)).remove();
            }
        }
    }
    setTimeout(CheckIfAbletoChat, 1000);
}

function addUsersToFrontEnd(myvar, friends_to_talk) {
    var i = 0;
    var lenght = Object.keys(myvar).length - 2;
    var bColorIdx = i;

    function nextPerson(i, myvar, lenght, bColorIdx, friends_to_talk) {
        if (bColorIdx > background_colors.lenght - 1) bColorIdx = rand(background_colors);

        var bColor = background_colors[bColorIdx];
        var checked = " ";
        var user = myvar[i];
        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "FriendEmail=" + myvar[i] + "&f=p",
            cache: false,
            success: function(data) {
                if (isInArray(myvar[i], friends_to_talk)) {
                    checked = "checked";
                    OnlineUsers.push(myvar[i]);

                }
                if (myvar[i] in friends_images && friends_images[myvar[i]] !== "") {
                    var div1 = '<div class="sea homeWindow"><div class="inside-the-paint" style="background-image: url(' + friends_images[myvar[i]] + ');"><span class="delete-button" id="delUser#' + i + '" onmouseover="DeleteOVER(this);" onmouseout="DeleteOUT(this);" onclick="DeleteUser(this);" title="Remove user from your contacts.">&#10006;</span></div><div class="inside-the-paint2"> <div class="meeting-create"><span style="cursor: pointer" onclick="MeetingInit(this);" id="MeetingStage#' + i + '" title="Manage your meetings.">Meeting Panel</span></div><div id="SeaUser-' + user.replace(/\@/, '\-') + '" title="' + user + '">' + data + '<span class="online-state" title="Offline"></span> </div><div class="show-active"><div style="display: inline-block; margin-top:-30px;">Let to see you?</div><div class="switch"><input id="cmn-toggle#' + i + '" class="cmn-toggle cmn-toggle-round" type="checkbox" onclick="changeOnlineState(this);" name="onlchange-' + i + '" ' + checked + '><label for="cmn-toggle#' + i + '" ></label></div></div> <div></div> </div> </div>';
                } else {
                    var div1 = '<div class="sea homeWindow"><div class="inside-the-paint" style="background-color: ' + bColor + ';"><span class="delete-button" id="delUser#' + i + '" onmouseover="DeleteOVER(this);" onmouseout="DeleteOUT(this);" onclick="DeleteUser(this);" title="Remove user from your contacts.">&#10006;</span></div><div class="inside-the-paint2"><div class="meeting-create"><span style="cursor: pointer" onclick="MeetingInit(this);" id="MeetingStage#' + i + '" title="Manage your meetings.">Meeting Panel</span></div><div id="SeaUser-' + user.replace(/\@/, '\-') + '"  title="' + user + '" >' + data + '<span class="online-state" title="Offline"></span> </div><div class="show-active"><div style="display: inline-block; margin-top:-30px;">Let to see you?</div><div class="switch"><input id="cmn-toggle#' + i + '" class="cmn-toggle cmn-toggle-round" type="checkbox" onclick="changeOnlineState(this);" name="onlchange-' + i + '" ' + checked + '><label for="cmn-toggle#' + i + '" ></label></div></div> <div></div> </div> </div>';
                    bColorIdx++;
                }
                $(div1).insertBefore($('#new-room'));

                if (isInArray(myvar[i], friends_to_talk) && isInArray(myvar[i], friends_who_want_to_talk_to_you)) {
                    var idx = myvar.indexOf(myvar[i]);
                    if (document.getElementById("ReadyToChat#" + idx) === null) {
                        var divtoadd = '<div class="ReadyToChat" id="ReadyToChat#' + idx + '" onclick="GoAndChat(this);">@</div>';
                        $(document.getElementById("SeaUser-" + myvar[i].replace(/\@/, '\-'))).parent().append(divtoadd);
                        KeyValueOnlineUsers.push(myvar[i]);

                    }

                }
                if (i != lenght) {
                    ++i;
                    checked = " ";
                    nextPerson(i, myvar, lenght, bColorIdx, friends_to_talk);
                } else {
                    GetOnlineState();
                    CheckIfAbletoChat()
                }

            },
            error: function(er) {
                var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
                $('.top-msg').showMsg({
                    msg: error
                });
            }
        });
    }
    nextPerson(i, myvar, lenght, bColorIdx, friends_to_talk);
}


function ClearImg() {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=e",
        cache: false,
        success: function(data) {
            $('.top-msg').showMsg({
                msg: data
            });
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });
}


function setMeetingTime(DateTime, PersonEmail) {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=h" + "&FriendEmail=" + PersonEmail + "&time=" + DateTime,
        cache: false,
        success: function(data) {
            if (data == 'done') {
                $('.top-msg').showMsg({
                    msg: 'Meeting created with ' + PersonEmail
                });
            } else {
                $('.top-msg').showMsg({
                    msg: 'Could not create meeting with ' + PersonEmail
                });
            }
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });
}

function addNew_MeetingFrontEnd(PersonEmail, isCanceled, canceledDate) {
    var showCancelMsg = '';
    if (isCanceled) {
        showCancelMsg = '<div style="padding-bottom: 5px;">Meeting for <span style="font-weight: bold;">' + canceledDate + '</span> has beeen canceled.</div>';
    }

    /*if (!isChecked) {
        if (document.getElementById('Meeting-' + PersonEmail.substr(0, PersonEmail.indexOf('@'))) == null) {
            $('#Meeting-' + PersonEmail.substr(0, PersonEmail.indexOf('@'))).remove();
        }

    }*/

    var div = `<div id="AddNewMeetingDiv"> ` +
        showCancelMsg + ` <div> 
                        Create a new meeting with
                    </div>
                    <div style="font-weight: bold;">@` + PersonEmail.substr(0, PersonEmail.indexOf('@')) + `</div>
                    <div style="padding-bottom: 5px; max-width: 500px; margin: auto;">
                        <input id="meeting-input" data-field="datetime" placeholder="New date/time" readonly type="text" \>
                        <div id="dtBox"></div> 
                    </div>

                    <div style="max-width: 500px; margin: auto;">
                        <div style="display: block; padding-bottom: 3px; padding-top: 2px;">
                            <span class="meeting-buttons" id="set-meeting">Set</span>
                            <span class="meeting-buttons" id="clear-meeting">Clear</span>
                        </div>
                        <div class="meeting-button-close">
                            <span class="meeting-buttons" onclick="document.getElementById(\'light\').style.display=\'none\';document.getElementById(\'fade\').style.display=\'none\'; "><a href="javascript:void(0)">Close</a> </span>
                        </div>
                    </div>
                </div>`;

    $(div).appendTo(".popup_meeting-wrapper");

    // DateTime Picker intialize
    $("#dtBox").DateTimePicker({
        dateTimeFormat: "dd-MM-yyyy HH:mm",
        maxDateTime: null,
        minDateTime: "20-07-2012 12:00 ",
        titleContentDateTime: "Set Date & Time",
        minTime: "00:00",
        maxTime: "23:59",
        buttonsToDisplay: ["HeaderCloseButton", "SetButton"],
        animationDuration: 100
    });

    // Clear DateTime Input box
    $('#clear-meeting').click(function() {
        $('#meeting-input').val('');
    });

    $('#set-meeting').click(function() {
        if ($('#meeting-input').val() != "") {
            var DateTime = $('#meeting-input').val();
            setMeetingTime(DateTime, PersonEmail);
        }
    });

    // Remove the window element when close button is pressed
    $('.meeting-button-close').click(function() {
        $('#light').fadeOut(300);
        $('#fade').fadeOut(300);
        document.getElementById('light').style.display = 'none';
        document.getElementById('fade').style.display = 'none';
        $('#AddNewMeetingDiv').remove();

    });



}

function ConfirmMeetingFunc(data) {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=l" + "&FriendEmail=" + data['friend'],
        cache: false,
        async: false,
        success: function(data) {
            if (data == "done") {
                $('#confirmMeetingMesg').text("Meeting confirmed");
            } else {
                $('#confirmMeetingMesg').text("Could not confirm meeting");
            }
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });
}

function DeleteMeeting(email) {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=j" + "&FriendEmail=" + email,
        cache: false,
        async: false,
        success: function(data) {
            if (data == "done") {
                $('.top-msg').showMsg({
                    msg: 'Meeting with ' + email + ' deleted successfully.'
                });
            }
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });

}


function addExisting_MeetingFrontEnd(data) {

    // Which user Am I? Creator or Reciever?
    if (data['Creator'] != personalEmail) { // Reciever
        // user confirmed the meeting ?
        if (data['Confirmed'] == 1) { // yes
            var confirmDiv = '<input type="checkbox" id="confirmedCheckbox" style="display: inline-block;" checked/><div style="display: inline-block;" id="confirmMeetingMesg">Confirmed meeting</div>';
        } else { // no
            var confirmDiv = '<input type="checkbox" id="confirmedCheckbox" style="display: inline-block;" /><div style="display: inline-block;" id="confirmMeetingMesg">Confirm meeting date</div>';
        }
    } else { // User

        var isConfirm = null;

        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "FriendEmail=" + data['friend'] + "&f=k",
            cache: false,
            async: false,
            success: function(res) {
                isConfirm = res;
            },
            error: function(er) {
                var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
                $('.top-msg').showMsg({
                    msg: error
                });
            }
        });



        if (isConfirm == "1") {
            var confirmDiv = '<span style="font-weight: bold;">@' + data['friend'].substr(0, data['friend'].indexOf('@')) + '</span>  confirmed your meeting';

        } else {
            var confirmDiv = '<div style="padding-top: 10px;" >Waiting for confirm from </div> <span style="font-weight: bold;">@' + data['friend'].substr(0, data['friend'].indexOf('@')) + '</span> ';

        }
    }



    var div = '<div id="AddExistMeetingDiv">         <div class="meeting-header">User  <span style="font-weight: bold;">@' + data['Creator'].substr(0, data['Creator'].indexOf('@')) + '</span> created a meeting for:</div>  <div class="meeting-time">' + data['DateTime'] + '</div>  ' + confirmDiv + ' <div class="clear-elems"></div>    <div style="display: block; padding-bottom: 3px; padding-top: 2px;">   <span class="meeting-buttons" id="delete-meeting" style="display: block;">Delete</span>  </div> <div class="meeting-button-close"><span class="meeting-buttons" onclick="document.getElementById(\'light\').style.display=\'none\';document.getElementById(\'fade\').style.display=\'none\'; "><a href="javascript:void(0)">Close</a> </span></div></div>';
    $(div).appendTo(".popup_meeting-wrapper");

    // Remove the window element when close button is pressed
    $('.meeting-button-close').click(function() {
        $('#light').fadeOut(300);
        $('#fade').fadeOut(300);
        document.getElementById('light').style.display = 'none';
        document.getElementById('fade').style.display = 'none';
        $('#AddExistMeetingDiv').remove();

    });

    $('#confirmedCheckbox').click(function() {
        if (!this.checked) {
            $('.top-msg').showMsg({
                msg: 'You can\'t undo this action.\n If you want, please delete this meeting'
            });
            return false;
        } else {
            ConfirmMeetingFunc(data);
        }

    });

    $('#delete-meeting').click(function() {
        DeleteMeeting(data['friend']);
    });

}

function MeetingInit(elem) {
    var PersonId = elem.id.substring(elem.id.indexOf('#') + 1);
    var PersonEmail = myvar[PersonId];
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=g" + "&FriendEmail=" + PersonEmail,
        cache: false,
        success: function(data) {
             $("#Meeting-" + PersonEmail.substr(0, PersonEmail.indexOf('@'))).remove();
            data = JSON.parse(data);
            if (data['MeetingExist'] == "yes") {
                addExisting_MeetingFrontEnd(data);
            } else if (data['MeetingExist'] == "canceled") {
                addNew_MeetingFrontEnd(PersonEmail, true, data['canceledDate'], data['Checked']);
            } else {
                // The two users has never created a meeting before
                addNew_MeetingFrontEnd(PersonEmail, false, false);
            }
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });
    $('#light').fadeIn(300);
    $('#fade').fadeIn(300);
    document.getElementById('light').style.display = 'block';
    document.getElementById('fade').style.display = 'block';

}

function addUpdateMeetingIco(data, i, stop) {
    var idx = myvar.indexOf(data[i]);
    if (document.getElementById('Meeting-' + data[i].substr(0, data[i].indexOf('@')))  == null ) {
        var divtoadd = '<span id="Meeting-'+data[i].substr(0, data[i].indexOf('@'))+'" class="new-meeting-alert" title="You have a new update for this meeting.">N</span>';
        $(document.getElementById("MeetingStage#"+ idx)).append(divtoadd);
    }
    if (i != stop) {
        i++;
        addUpdateMeetingIco(data, i, stop);
    }
}

function getMeetings() {
    $.ajax({
        type: 'POST',
        url: woking_dir,
        data: "f=x",
        cache: false,
        async: false,
        success: function(data) {
            var data = JSON.parse(data);
            var i = 0;
            var stop = data.length - 1;
            if (data.length != 0) {
                addUpdateMeetingIco(data, i, stop);
            }
            
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });

    setTimeout(getMeetings, 3000);
}
