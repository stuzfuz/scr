'use strict;'

function showError(classname, msg) {
    $(classname).text(msg);
    $(classname).show();
    $(classname).fadeIn(500);
    setTimeout(function () {
        $(classname).fadeOut(500);
    }, 3000);
}

$(document).ready(function () {
    console.log("ready!");

    // ------------------------------------------------------
    // login page
    // ------------------------------------------------------

    // sign button clicked -> send user data to server
    $("#formSignIn").submit(function (e) {
        console.log("#formSignIn submit");
        e.preventDefault();
        data = {
            username: $("#signInUsername").val(),
            password: $("#signInPassword").val()
        };

        $.ajax({
            url: "/api/login",
            type: "POST",
            data: data
        }).done(function (res) {
            console.log("/api/login  DONE reponse", JSON.stringify(res, null, 4));
            window.location.assign("/");
        }).fail(function (res) {
            console.log("/api/login  FAIL reponse", JSON.stringify(res, null, 4));
            showError(".login-error", "An error occured with your login credentials - please try again.");
        });
    });

    // ------------------------------------------------------
    // register page
    // ------------------------------------------------------

    // TODO: remove!!  but its easer this way than filling out the form on every reload
    $("#registerUsername").val(Math.floor((Math.random() * 10000) + 1));
    $("#registerPassword").val("a"),
        $("#registerPasswordConfirm").val("a"),
        $("#registerFirstname").val("a"),
        $("#registerLastname").val("a")


    $("#formRegister1").submit(function (e) {
        e.preventDefault();
        console.log("#formRegister submit");

        // read input data from form
        dataRegister = {
            username: $("#registerUsername").val(),
            password: $("#registerPassword").val(),
            passwordConfirm: $("#registerPasswordConfirm").val(),
            firstname: $("#registerFirstname").val(),
            lastname: $("#registerLastname").val()
        };
        console.log("registerForm  dataRegister = " + JSON.stringify(dataRegister, null, 4));

        console.log("checking passwords");
        // validate input check passwords
        if (dataRegister.password !== dataRegister.passwordConfirm) {

            console.log("password not qual");
            showError(".register-error", "password and confirmation password are not equal - please try again");
            return;
        }

        console.log("checking username");
        // check if username is available? 
        $.ajax({
            url: "/api/checkusername",
            type: "POST",
            data: { username: $("#registerUsername").val() }
        }).done(function (res) {
            console.log("/api/checkusername DONE reponse", JSON.stringify(res, null, 4));

            // registering user ...
            // send data to server
            $.ajax({
                url: "/registerstep2",
                type: "POST",
                data: dataRegister
            }).done(function (res) {
                console.log("/registerstep2  DONE response", JSON.stringify(res, null, 4));
                window.location.assign("/registerstep2");
            }).fail(function (res) {
                console.log("/registerstep2  FAIL response", JSON.stringify(res, null, 4));
                showError(".register-error", "error registering you ...!");
            });

            // window.location.assign("/");
        }).fail(function (res) {
            console.log("/api/checkusername   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/checkusername   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $("#formRegister2").submit(function (e) {
        e.preventDefault();
        console.log("#formRegister2 submit");

        channelIds = [];
        $('#registerAvailableChannels option:selected').each(function () {
            console.log("option selected :", $(this).val());
            channelIds.push($(this).val());
        });

        // read input data from form
        data = {
            channels: channelIds
        };
        console.log("formRegister2  data = " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/savechannels",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/savechannels DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> redirect the user to the home page
            window.location.assign("/");

        }).fail(function (res) {
            console.log("/api/savechannels   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/savechannels   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $("#formNewChannel").submit(function (e) {
        e.preventDefault();
        console.log("#formNewChannel submit");

        // read input data from form
        data = {
            channelname: $("#channelName").val(),
            description:  $("#channelDescription").val()
        };
        console.log("formNewChannel  data = " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/newchannel",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/newchannel DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> redirect the user to the home page
            window.location.assign("/");

        }).fail(function (res) {
            console.log("/api/newchannel   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/newchannel   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".formnewmessage").submit(function (e) {
        e.preventDefault();
        console.log(".formnewmessage submit");
        var myClass = $(this).attr("class");
        var s = "formnewmessage channelid-"
        var channelid = myClass.substr(s.length, myClass.length);
        console.log("channelid " + channelid);

        var txt = $(this).find("input[type=text]").val();
        if (txt.lengtth < 1) {
            showError(".register-error", res["status"]);
        }
        console.log("message text " + txt);

        var data = {
            channelid: channelid,
            txt: txt
        };

        console.log("new message data " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/newmessage",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/newmessage DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> reload page and data
            location.reload();
        }).fail(function (res) {
            console.log("/api/newmessage   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/newmessage   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                showError(".feedback-newmessage ", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".importantmessage").click(function (e) {
        e.preventDefault();
        console.log("importantmessage   click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
        var s = "importantmessage messageid-"
        var messageid = myClass.substr(s.length, myClass.length);
        console.log("messageid " + messageid);

        var data = {
            messageid: messageid
        };

        console.log("mark message as important " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/markmessageimportant",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/markmessageimportant DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> reload page and data
            location.reload();
            alert("Message marked as important!");
        }).fail(function (res) {
            console.log("/api/markmessageimportant  FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/markmessageimportant   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                // showError(".feedback-message ", res["status"]);
                alert("Message could not be marked as important!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".notimportantmessage").click(function (e) {
        e.preventDefault();
        console.log("notimportantmessage   click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
        var s = "notimportantmessage messageid-"
        var messageid = myClass.substr(s.length, myClass.length);
        console.log("messageid " + messageid);

        var data = {
            messageid: messageid
        };

        console.log("mark message as NOT  important " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/markmessagenotimportant",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/markmessagenotimportant DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> reload page and data
            location.reload();
            alert("Message marked as NOT important!");
        }).fail(function (res) {
            console.log("/api/markmessagenotimportant  FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/markmessagenotimportant   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                // showError(".feedback-message ", res["status"]);
                alert("Message could not be marked as NOT important!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".deletemessage").click(function (e) {
        e.preventDefault();
        console.log("deletemessage   click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
        var s = "deletemessage messageid-"
        var messageid = myClass.substr(s.length, myClass.length);
        console.log("messageid " + messageid);

        var data = {
            messageid: messageid
        };

        console.log("delete message    " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/deletemessage",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/deletemessage DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> reload page and data
            location.reload();
            alert("Message deleted!");
        }).fail(function (res) {
            console.log("/api/deletemessage  FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/deletemessage   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                // showError(".feedback-message ", res["status"]);
                alert("Message could not be deleted!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".editmessage").click(function (e) {
        e.preventDefault();
        console.log("editmessage click");

        var myClass = $(this).attr("class");
        console.log("class " + myClass);
        var s = "editmessage messageid-"
        var messageid = myClass.substr(s.length, myClass.length);
        console.log("messageid " + messageid);

        var data = {
            messageid: messageid
        };

        $.ajax({
            url: "/api/editmessageallowed",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/editmessageallowed DONE reponse", JSON.stringify(res, null, 4));

            // if message can be edited -> then show a form with old text
            console.log("edit  message    " + JSON.stringify(data, null, 4));

            var selector = ".card.msg.messageid-" + messageid;
            var selectorTxt = ".card.msg.messageid-" + messageid + " .card-text";
            $(".formeditmessage").appendTo(selector);
            $(".formeditmessage").show();
            $(selectorTxt).hide();
            var txt = $(selectorTxt).text();
            console.log("edit  message   txt  " + txt);

            $(".formeditmessage").find("input[type=text]").val(txt);
            $(".formeditmessage").addClass("messageid-" + messageid);
            // location.reload();
            // alert("Message saved!");
        }).fail(function (res) {
            console.log("/api/editmessageallowed  FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/editmessageallowed  FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                // showError(".feedback-message ", res["status"]);
                alert("Message  can not be edited - it's already read by a user!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".formeditmessage").submit(function (e) {
        e.preventDefault();
        console.log(".formeditmessage submit");
        var myClass = $(this).attr("class");
        var s = "formeditmessage messageid-"
        var messageid = myClass.substr(s.length, myClass.length);
        console.log("messageid " + messageid);

        var txt = $(this).find("input[type=text]").val();
        if (txt.lengtth < 1) {
            showError(".register-error", res["status"]);
        }
        console.log("message text " + txt);

        var data = {
            messageid: messageid,
            txt: txt
        };

        console.log("edit message data " + JSON.stringify(data, null, 4));

        $.ajax({
            url: "/api/editmessage",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function (res) {
            console.log("/api/editmessage DONE reponse", JSON.stringify(res, null, 4));

            // if everything works -> reload page and data
            location.reload();
        }).fail(function (res) {
            console.log("/api/editmessage   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log("/api/editmessage   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                // showError(".feedback-newmessage ", res["status"]);
                Alert("Message could not be saved!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    // display hidden in CSS does not work :-( (not even with !important)
    // $(".feedback-newmessage").hide();

    var unreadMessages = [];

    $('.unreadmessage').appear();
    $('.unreadmessage').on('appear', function (event) {
        console.log("element with class appeared: " + $(this).attr('class'));
        var myClass = $(this).attr('class');
        var s = "card msg messageid-";
        var messageid = parseInt(myClass.substr(s.length, myClass.length));

        console.log("unreadmessage   messageid: " + messageid);

        unreadMessages.push(messageid);

        // remove class, so it wont be added twice to the array
        var selector = ".card.msg.messageid-" + messageid;
        console.log("selector   : " + selector);

        $(selector).removeClass("unreadmessage").unbind('appear');

        // remove the "unread" span
        $(selector).find(".badge-secondary").remove();
    });

    function markMessagesUnread() {
        console.log("MarkMessagesUnread   current 'unreadMessages' = " + JSON.stringify(unreadMessages));
        var msgIds = JSON.parse(JSON.stringify(unreadMessages));
        console.log("MarkMessagesUnread   deep copy 'msgIds' = " + JSON.stringify(msgIds));

        unreadMessages = [];
        console.log("MarkMessagesUnread   empty  'unreadMessages' = " + JSON.stringify(unreadMessages));

        if (msgIds.length > 0 ) {
            var data = {
                messageIds: msgIds
            };
    
            console.log("mark messages as read  " + JSON.stringify(data, null, 4));
    
            $.ajax({
                url: "/api/markmessagesasread",
                type: "POST",
                data: data,
                dataType: "JSON"
            }).done(function (res) {
                console.log("/api/markmessagesasread DONE reponse", JSON.stringify(res, null, 4));
                // if everything works -> do nothing
             }).fail(function (res) {
                console.log("/api/markmessagesasread   FAIL response", JSON.stringify(res, null, 4));
                res = JSON.parse(res["responseText"]);
                console.log("/api/markmessagesasread   FAIL responseText", JSON.stringify(res, null, 4));
    
                if (res["errorcode"] >= 2) {
                    //showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
                    // showError(".feedback-newmessage ", res["status"]);
                    Alert("Message could not be saved!");
                } else {
                    console.log("well - don't know now ");
                }
            });
        }
    }

    if (window.location.href.includes("/channel/")) {
        setInterval(markMessagesUnread, 5000);
    }
});
