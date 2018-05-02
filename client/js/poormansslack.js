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
            }).fail(function( res ) {
                console.log( "/registerstep2  FAIL response", JSON.stringify(res, null, 4));
                showError(".register-error", "error registering you ...!");
            });

            // window.location.assign("/");
        }).fail(function( res ) {
            console.log( "/api/checkusername   FAIL reponse", JSON.stringify(res, null, 4));
            res = JSON.parse(res["responseText"]);
            console.log( "/api/checkusername   FAIL responseText", JSON.stringify(res, null, 4));

            if (res["errorcode"] == 2) {
                showError(".register-error", "username is already in use - please choose wisely!");
            } else {
                console.log("well - don't know now ");
            }
        });
    });
});
