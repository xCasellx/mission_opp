$("html").ready(function () {
   if(getCookie("jwt") !== undefined){
       $(location).attr('href',"../pages/cabinet.php");
       return false;
   }
    loginForm();
});

$(document).on("submit", "#login-form", function () {
    let form_data = JSON.stringify($(this).serializeObject());
    $.ajax({
        url: "../api/user/login.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,

        success : function(result){
            setCookie("jwt", result.jwt,2);
            $(location).attr('href',"../pages/cabinet.php");
        },
        error : function(result){
            console.log(result.responseJSON);
            printMessage("error",result.responseJSON.message);
        }
    })
    return false;
});

$(document).on("submit", "#register-form", function () {
    let form_data = JSON.stringify($(this).serializeObject());
    $.ajax({
        url: "../api/user/create.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,
        success : function(result) {
            loginForm();
            printMessage("success",result.message);
        },
        error : function(result) {
            printMessage("error",result.responseJSON.message);
        }
    })
    return false;
});


$("#sign-in").on("click", loginForm);

$("#register").on("click", function() {
    deleteMessage();
    $("#sign-in").attr('disabled', false);
    $("#register").attr('disabled', true);
    let d=maxDate(8);
    let html=`<form action="/" id="register-form" method="post" class="form-check p-0 m-5">
                    <div class="row">
                        <input required type="text" class="border border-dark form-control" name="first_name" placeholder="First name">
                        <input required type="text" class="border border-dark form-control" name="second_name" placeholder="Second name">
                    </div>
                    <input required type="email" class="border border-dark form-control w-100" name="email" placeholder="Email address">
                    <div class="row">
                        <input required type="tel" pattern="[0-9]{5,15}" class="border border-dark form-control" name="number" placeholder="Number">
                        <input required type="text" class="border border-dark form-control" name="town" placeholder="Place of residence">
                    </div>
                    <input required type="date" class="w-100 border border-dark form-control" max="`+d+`" name="date" >
                    <div class="row">
                        <input required type="password" class="border border-dark form-control" name="password" placeholder="Password">
                        <input required type="password" class="border border-dark form-control" name="confirm_password" placeholder="Confirm password">
                    </div>
                    <button type="submit" name="submit" class="float-right btn mt-4 mb-3 btn-success"><strong>Register</strong></button>
            </form>`
    $("#form").html(html);
});

function loginForm() {
    deleteMessage();
    let html=`  <form action="/" id="login-form" method="post" class="form-check p-0 m-5">
                    <input required type="email" class="form-control" name="email" placeholder="Email address">
                    <input required type="password" class="form-control" name="password" placeholder="Password">
                    <button type="submit" name="submit" class="float-right btn mt-4 mb-3 btn-success"><strong>Sign in</strong></button>
                </form>`
    $("#form").html(html);
    $("#sign-in").attr('disabled', true);
    $("#register").attr('disabled', false);
}

