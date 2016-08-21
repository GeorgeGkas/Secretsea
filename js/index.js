$(document).ready(function() {
    var isRegister = false;
    var woking_dir = getWorkingDir();
    $('#register').hide();
    $('#About').hide();
    $('.top-msg').hide();
    //$('#version').hide();
    $('.informations').hide();
    $('.footer').hide();
    $('.Logo').hide();
    $('.header').hide();

    $("#LearnMoreBack").click(function() {
        $('#About').fadeOut(function() {
            $(' #login, #GetStarted , #LearnMore, #p1, #p2, #version').fadeIn();
            $('.header h2').text('Welcome to SecretSea ');
            $('.informations').css("padding-top", "7px");
        });
    });

    $('#LearnMore').click(function() {
        if (isRegister) {
            $('#register, #GetStarted , #LearnMore').fadeOut(function() {
                isRegister = false;
                $('.header h2').text('Learn More about the Project');
                $('#About').fadeIn();
            });
        } else {
            $('.header h2').text('Learn More about the Project');
            $(' #login, #GetStarted , #LearnMore, #p1, #p2, #version').fadeOut(function() {
                $('#About').fadeIn();
            });
        }

    });
    $("#GetStarted").click(function() {
        if (!isRegister) {
            $('#p1,#p2, #login, #version').fadeOut(function() {
                $('.header h2').text(' Create your Communication Channel ');
                $('#GetStarted a').text('Sea Dive');
                $('#register').fadeIn();
                $('.informations').css("padding-top", "20px");

                grecaptcha.reset();
                isRegister = true;
            });
        } else {
            $('#register').fadeOut(function() {
                $('#GetStarted a').text('Get Started Today');
                $('#p1,#p2, #login, #version').fadeIn();
                $('.header h2').text('Welcome to SecretSea ');
                $('.informations').css("padding-top", "7px");

                grecaptcha.reset();
                //$('.footer').css( { marginTop : "300px"} );

                isRegister = false;
            });
        }


    });

    $('.top-msg').on("click", ".top-msg-close", function() {
        $('.top-msg-ico, .top-msg-inner, .top-msg-close').fadeOut(300, function() {
            $(this).remove()
        });
    });


    $('#login').submit(function(e) {
        e.preventDefault();

        var email = $('#Lemail').val();
        var pass = $('#LPass').val();

        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "Lemail=" + email + "&LPass=" + pass + "&f=a",
            cache: false,
            beforeSend: function() {
                $('.checkL').append('<img src="img/loading.gif" id="loadingProgressBar"> ');
            },
            success: function(data) {
                if (data == "yes") {
                    window.location.reload();
                } else {
                    $('#loadingProgressBar').fadeOut(300).remove();
                    $('.top-msg').showMsg({
                        msg: data
                    });
                }
            },
            error: function(er) {
                $('#loadingProgressBar').fadeOut(300).remove();
                var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
                $('.top-msg').showMsg({
                    msg: error
                });
            }
        });

    });

    $('#register').submit(function(e) {
        e.preventDefault();
        isFormOk = checkRegisterForm();
        if (!isFormOk) {
            return false;
        }

        var email = $('#Remail').val();
        var pass = $('#RPass1').val();
        var pass2 = $('#RPass2').val();

        $.ajax({
            type: 'POST',
            url: woking_dir,
            data: "Remail=" + email + "&RPass1=" + pass + "&RPass2=" + pass2 + "&g-recaptcha-response=" + grecaptcha.getResponse() + "&f=b",
            cache: false,
            beforeSend: function() {
                $('.checkR').append('<img src="img/loading.gif" id="loadingProgressBar"> ');
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
        });

    });
});

function BuildIndexPage() {
    $('.Logo').fadeIn(function() {
        $('.header').fadeIn(function() {
            $('#version').animate({
                marginTop: '0px',
                opacity: '1.0'
            }, function() {
                $('.informations').fadeIn(function() {
                    $('.footer').fadeIn();
                });
            });

        });

    });
}

window.onload = function() {
    setTimeout(BuildIndexPage, 500);
}
