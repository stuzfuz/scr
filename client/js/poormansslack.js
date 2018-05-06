'use strict;'

function showError(classname, msg) {
    $(classname).text(msg);
    $(classname).fadeIn(500);
    setTimeout(function(){
        $(classname).fadeOut(500);
    }, 3000);
}

$( document ).ready(function() {
    console.log( "ready!" );

    // ------------------------------------------------------
    // login page
    // ------------------------------------------------------

    // sign button clicked -> send user data to server
    $( "#formSignIn" ).submit(function(e) {
        console.log("#formSignIn submit");
        e.preventDefault();
        data =  {
            username: $("#signInUsername").val(),
            password: $("#signInPassword").val()
        };
        
        $.ajax({
            url: "/api/login",
            type: "POST",
            data: data
        }).done(function( res ) {
            console.log( "/api/login  DONE reponse", JSON.stringify(res, null, 4));
            window.location.assign("/");
        }).fail(function( res ) {
            console.log( "/api/login  FAIL reponse", JSON.stringify(res, null, 4));
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

    
    $( "#formRegister1" ).submit(function(e) {
        e.preventDefault();
        console.log("#formRegister submit");

        // read input data from form
        dataRegister =  {
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
        }).done(function( res ) {
            console.log( "/api/checkusername DONE reponse", JSON.stringify(res, null, 4));

            // registering user ...
            // send data to server
            $.ajax({
                url: "/registerstep2",
                type: "POST",
                data: dataRegister
            }).done(function( res ) {
                console.log( "/registerstep2  DONE response", JSON.stringify(res, null, 4));
                window.location.assign("/registerstep2");
            }).fail(function( res ) {
                console.log( "/registerstep2  FAIL response", JSON.stringify(res, null, 4));
                showError(".register-error", "error registering you ...!");
            });

            // window.location.assign("/");
        }).fail(function( res ) {
            console.log( "/api/checkusername   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log( "/api/checkusername   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $( "#formRegister2" ).submit(function(e) {
        e.preventDefault();
        console.log("#formRegister2 submit");

        channelIds = [];
        $('#registerAvailableChannels option:selected').each(function() {
            console.log("option selected :", $(this).val());
            channelIds.push($(this).val());
        });

        // read input data from form
        data =  {
            channels: channelIds
        };
        console.log("formRegister2  data = " + JSON.stringify(data, null, 4));
        
        $.ajax({
            url: "/api/savechannels",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function( res ) {
            console.log( "/api/savechannels DONE reponse", JSON.stringify(res, null, 4));
            
            // if everything works -> redirect the user to the home page
            window.location.assign("/");
        
        }).fail(function( res ) {
            console.log( "/api/savechannels   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log( "/api/savechannels   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $( "#formNewChannel" ).submit(function(e) {
        e.preventDefault();
        console.log("#formNewChannel submit");

        // read input data from form
        data =  {
            channelname: $("#channelName").val(),
        };
        console.log("formNewChannel  data = " + JSON.stringify(data, null, 4));
        
        $.ajax({
            url: "/api/newchannel",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function( res ) {
            console.log( "/api/newchannel DONE reponse", JSON.stringify(res, null, 4));
            
            // if everything works -> redirect the user to the home page
            window.location.assign("/");
        
        }).fail(function( res ) {
            console.log( "/api/newchannel   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log( "/api/newchannel   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".register-error", res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".formnewmessage").submit(function(e) {
        e.preventDefault();
        console.log(".formnewmessage submit");
        var myClass = $(this).attr("class");
        var s = "formnewmessage topicid-"
        var topicid = myClass.substr(s.length, myClass.length);
        console.log("topicid " + topicid);

        var txt = $(this).find("input[type=text]").val();
        if (txt.lengtth < 1) {
            showError(".register-error", res["status"]);
        }
        console.log("message text " + txt);

        var data = {
            topicid: topicid,
            txt: txt
        };

        $.ajax({
            url: "/api/newmessage",
            type: "POST",
            data: data,
            dataType: "JSON"
        }).done(function( res ) {
            console.log( "/api/newmessage DONE reponse", JSON.stringify(res, null, 4));
            
            // if everything works -> reload page and data
            // location.reload();        
        }).fail(function( res ) {
            console.log( "/api/newmessage   FAIL response", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log( "/api/newmessage   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] >= 2) {
                showError(".feedback-newmessage .topicid-" + topicid, res["status"]);
            } else {
                console.log("well - don't know now ");
            }
        });
    });

    $(".formnewtopic").submit(function(e) {
        e.preventDefault();
        console.log(".formnewtopic submit");
        var myClass = $(this).attr("class");
        var s = "formnewtopic channelid-";
        var channelid = myClass.substr(s.length, myClass.length);
        console.log("formnewtopic     channelid " + channelid);

        var title = $(this).find("input[name=topictitle]").val();
        var description = $(this).find("input[name=topicdescription]").val();

        if (title.lengtth < 1) {
            showError(".register-error", "title too short");
        } else if (description.lengtth < 1) {
            showError(".register-error", "description too short");
        } else {
            console.log("formnewtopic    title:  " + title);
            console.log("formnewtopic    description: " + description);
    
            var data = {
                channelid: channelid,
                description: description,
                title: title
            };
            console.log("formnewtopic    data " + JSON.stringify(data, null, 4));
    
    
            $.ajax({
                url: "/api/newtopic",
                type: "POST",
                data: data,
                dataType: "JSON"
            }).done(function( res ) {
                console.log( "/api/newtopic DONE reponse", JSON.stringify(res, null, 4));
                
                // if everything works -> reload page and data
                // location.reload();        
            }).fail(function( res ) {
                console.log( "/api/newtopic   FAIL response", JSON.stringify(res, null, 4));
                res = JSON.parse(res["responseText"]);
                console.log( "/api/newtopic   FAIL responseText", JSON.stringify(res, null, 4));
    
                if (res["errorcode"] >= 2) {
                    showError(".feedback-newtopic .topicid-" + topicid, res["status"]);
                } else {
                    console.log("well - don't know now ");
                }
            });
        }
    });

    
    $(".importanttopic").click(function(e) {
        e.preventDefault();
        console.log("importanttopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });

    $(".deletetopic").click(function(e) {
        e.preventDefault();
        console.log("deletetopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });

    $(".edittopic").click(function(e) {
        e.preventDefault();
        console.log("edittopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });

    $(".importantmessage").click(function(e) {
        e.preventDefault();
        console.log("importanttopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });

    $(".deletemessage").click(function(e) {
        e.preventDefault();
        console.log("deletetopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });

    $(".editmessage").click(function(e) {
        e.preventDefault();
        console.log("edittopic click");
        var myClass = $(this).attr("class");
        console.log("class " + myClass);
    });


    // display hidden in CSS does not work :-( (not even with !important)
    $(".feedback-newmessage").hide();
    $(".feedback-newtopic").hide();
});
