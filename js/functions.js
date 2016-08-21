function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function DeleteOVER(elem) {
    elem.style.color = "#DDD";
}

function DeleteOUT(elem) {
    elem.style.color = "#fff";
}

function rand(items) {
    return ~~(Math.random() * items.length);
}

var DelElemArray = function(array, elem) {
    var idx = array.indexOf(elem);
    if (idx > -1) {
        array.splice(idx, 1);
    }
}

function LogOut() {
    $.ajax({
        type: 'POST',
        url: working_dir,
        data: {
            emails: JSON.stringify(OnlineUsers),
            f: 'c'
        },
        cache: false,
        async: false,
        success: function(data) {
            window.location.href = data;
        },
        error: function(er) {
            var error = '[Dev Error] ' + er.status + ' - ' + er.responseText;
            $('.top-msg').showMsg({
                msg: error
            });
        }
    });

}


function isInArray(value, array) {
    return array.indexOf(value) > -1;
}

function getWorkingDir() {
    var url = window.location.href;
    working_dir = url.substring(0, url.lastIndexOf("/") + 1) + "controller/controller.php";
    return working_dir; // it will be "http://mysite.com/stuff/"
}

function intersection_destructive(a, b) {
    var result = [];
    while (a.length > 0 && b.length > 0) {
        if (a[0] < b[0]) {
            a.shift();
        } else if (a[0] > b[0]) {
            b.shift();
        } else { /* they're equal */
            result.push(a.shift());
            b.shift();
        }
    }

    return result;
}

function isset(variable) {
    return typeof variable !== typeof undefined ? true : false;
}

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    } else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function create_xmlHttpRequestObject() {
    var xmlHttp;

    if (window.ActiveXObject) {
        try {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            xmlHttp = false;
        }
    } else {
        try {
            xmlHttp = new XMLHttpRequest();
        } catch (e) {
            xmlHttp = false;
        }
    }

    if (!xmlHttp) {
        alert("Can't create XML Object");
    } else {
        return xmlHttp;
    }
}


function checkRegisterForm() {
    var REInputLength = document.getElementById('Remail').value.length;
    if (REInputLength > 25 || REInputLength < 5) {
        if (REInputLength < 5) $('.checkR').text('Use at least 5 characters for email.');
        else $('.checkR').text('Use no more than 25 characters for email.');
        return false;
    }

    var fistPassInput = document.getElementById("RPass1").value;
    var secondPassInput = document.getElementById("RPass2").value;
    if (fistPassInput !== secondPassInput) {
        $('.checkR').text('Passwords do not match.');
        return false;
    }

    if (fistPassInput.length < 5) {
        $('.checkR').text('Use at least 5 characters for password.');
        return false;
    }
    $('.checkR').text('');
    return true;
}
