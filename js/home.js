/* Author   : George Gkasdrogkas (GeorgeGkas)
 * Email    : <georgegkas@gmail.com>
 * Last Edited  : 30-11-2015
 *
 * Purpose :
 *       The main file that holds all the Jquery
 *       actions used in home.php
 */

$(document).ready(function() {

    // Default front-end state for user
    var UserMenu_isShown = false;
    var preferences_isShown = false;
    var emojis_isShown = false;
    var isMeetingUpdate = false;
    $('#UserMenu').hide();
    $('#preferences').hide();
    $('#person-search').hide();
    $('.spawn-emonicons').hide();
    $('#message-wrapper').hide();
    $('#SearchBack').hide();
    $('#light').hide();
    $('#fade').hide();
    $('.ImgUploadWrapper').hide();
    $('#add-person').attr("disabled", "disabled");

    $('.pref_avatar, .avatar-menu').css({
        'background-image': 'url(' + enviromentData.UserAvatar + ')'
    });

    $('#Puser').attr('placeholder', enviromentData.Username);

    $('#UsN').text(enviromentData.UserEmail);

    var PrefUserId = document.getElementById('UserId');
    PrefUserId.innerHTML = PrefUserId.innerHTML + ' ' + enviromentData.UserEmail;

    /*  Summary: Spawn options menu just under
     *           the user avatar image
     */
    $('.avatar-menu').click(function() {
        if (UserMenu_isShown) {
            $('#UserMenu').fadeOut(300);
            UserMenu_isShown = false;
        } else {
            $('#UserMenu').fadeIn(300);
            UserMenu_isShown = true;
        }

    });

    /*  Summary: Spawn options menu just under
     *           the user avatar image
     */
    $('.container, .icon').click(function() {
        if (UserMenu_isShown) {
            $('#UserMenu').fadeOut(300);
            UserMenu_isShown = false;

        }
    });

    /*  Summary: Enter preferences window
     *  
     *  Requirements: User don't chatting 
     *                The preferences window is not already displayed
     */
    $('#pref').click(function() {
        if (!preferences_isShown && !isMessaging) {
            $('.seas').fadeOut(300, function() {
                $('#preferences').fadeIn(300);
                preferences_isShown = true;
            });
        }
    });

    /*  Summary: Leave preferences window
     */
    $('#PrefBack').click(function() {
        $('#preferences').fadeOut(300, function() {
            $('.seas').fadeIn(300);
            preferences_isShown = false;

        });

    });

    /*  Summary: Adds selected emoji to chat input
     *
     */
    $('.ico').click(function() {
        var value = $(this).text(); // The desirable emoji
        var input = $('#message-sender'); // The input selector
        input.val(input.val() + value + ' ');

    });

    /*  Summary: Show or hide the emojis window 
     *
     *  Selector: Button that opens the emojis window
     *
     */
    $('#emonicons').click(function() {
        if (emojis_isShown) {
            $('.spawn-emonicons').fadeOut(300);
            emojis_isShown = false;
        } else {
            $('.spawn-emonicons').fadeIn(300);
            emojis_isShown = true;
        }

    });

    /*  Summary: Action that check and send the desirable
     *           messsage to the server when user press Enter 
     *           key
     */
    $('#message-sender').bind('keydown', function(event) {
        prepareToSendMessage(event, $(this));
    });

    /*  Summary: Leave avatar select window
     */
    $('#ImgBack').click(function() {
        $('.ImgUploadWrapper').fadeOut(300, function() {
            $('#preferences').fadeIn(300);
        });

    });


    /*  Summary: Leave the chat room window
     */
    $('#LeaveChatRoom').click(function() {
        $('#message-wrapper').fadeOut(300, function() {
            $('.homeWindow, .new-room').fadeIn(300);
            isMessaging = false;

        });

    });

    /*  Summary: Ender the window that helps
     *  `        you to add new friends to your
     *           account
     */
    $('#new-room > img').click(function() {
        $('#new-room > img').fadeOut(300);
        document.getElementById("new-room").style.cursor = "default";
        $('#person-search, #SearchBack').fadeIn(300);
    });

    /*  Summary: Leave the window that helps
     *  `        you to add new friends to your
     *           account
     */
    $('#SearchBack').click(function() {
        $('#person-search, #SearchBack').fadeOut(300);
        $('#new-room > img').fadeIn(300);
        $('#PersonSearchInput').val('');

    });

    /*  Selector: The profile image of user account
     *
     *  Action: Spawn a new window to change your account image
     *          The preferences window is not already displayed
     */
    $('#ProfileImage').click(function() {
        $('#preferences').fadeOut(300, function() {
            $('.ImgUploadWrapper').fadeIn(300);
        });
    });


    /*  Summary: Update your profile image
     */
    $('#imageUploadForm').on('submit', function() {
        var formData = new FormData(this);
        formData.append('f', 'f');

        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#loading').append('<img src="img/loading.gif" id="loadingProgressBar"> ');
            },
            success: function(data) {
                $('#loadingProgressBar').fadeOut(300).remove();
                $('.top-msg').showMsg({
                    msg: data
                });

            },
            error: function(er) {
                $('#loadingProgressBar').fadeOut(300).remove();
                var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
                $('.top-msg').showMsg({
                    msg: error
                });
            }
        }); // end of ajax

    }); // end of imageUploadForm submit action


    $('.top-msg').on("click", ".top-msg-close", function() {
        $('.top-msg-ico, .top-msg-inner, .top-msg-close').fadeOut(300, function() {
            $(this).remove()
        });
    });

}); // end document ready event
