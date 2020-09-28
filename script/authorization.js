$("html").ready(function () {
   if (getCookie("jwt") !== undefined) {
       $(location).attr('href',"../pages/cabinet.php");
       return false;
   }
    loginForm();
    loadListRegion();
    loadListCity();
});

$(document).on("submit", "#login-form", function () {
    let form_data = JSON.stringify($(this).serializeObject());

    $.ajax({
        url: "../api/user/login.php",
        type : "POST",
        contentType : 'application/json',
        data : form_data,

        success : function(result) {
            setCookie("jwt", result.jwt,2);
            $(location).attr('href',"../pages/cabinet.php");
        },
        error : function(result) {
            console.log(result.responseJSON);
            printMessage("error",result.responseJSON.message);
        }
    })
    return false;
});

$(document).on("submit", "#register-form", function () {
    let form_data = $(this).serializeObject();
    form_data.town=$('#city option:selected').attr("id").replace("city-","");
    form_data = JSON.stringify(form_data);
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

$(document).on("submit", "#restore-form", function () {
    let email = $("#restore-form > input[name = 'email']").val();
    alert(email)
    $.ajax({
        url: "../api/recovery/send.php",
        type : "POST",
        contentType : 'application/json',
        data : JSON.stringify({ email: email}),
        success : function(result) {
            loginForm();
            printMessage("success",result.message);
        },
        error : function(result) {
            console.log(result)
            printMessage("error",result.responseJSON.message);
        }
    })
    return false;
});

$(document).on("click",".sign-in", loginForm);

$("#register").on("click", function() {
    deleteMessage();
    loadListCountry();
    $(".sign-in").attr('disabled', false);
    $("#register").attr('disabled', true);
    let d=maxDate(8);
    let html=`<form action="/" id="register-form" method="post" class="form-check p-0 m-5">
                    <div class="row">
                        <input required type="text" class="border border-dark form-control" name="first_name" placeholder="First name">
                        <input required type="text" class="border border-dark form-control" name="second_name" placeholder="Second name">
                    </div>
                    <input required type="email" class="border border-dark form-control" name="email" placeholder="Email address">
                    <div class="row">
                        <input required type="tel" pattern="[0-9]{5,15}" class="border border-dark form-control" name="number" placeholder="Number">
                        <input required type="date" class="border border-dark form-control" max="`+d+`" name="date" >
                    </div>
                        <div class="row mt-2">
                            <select required id="country" class="border border-dark col mr-1 custom-select"></select>
                            <select required id="region" class="border border-dark col mr-1 custom-select"></select>
                            <select required id="city" class="border border-dark col custom-select"></select>
                        </div>
                    <div class="row">
                        <input required type="password" class="border border-dark form-control" name="password" placeholder="Password">
                        <input required type="password" class="border border-dark form-control" name="confirm_password" placeholder="Confirm password">
                    </div>
                    <button type="submit" name="submit" class="float-right btn mt-4 mb-3 btn-success"><strong>Register</strong></button>
            </form>`
    $("#form").html(html);
});

$(document).on("change","#country", function () {
    $("#city").empty();
    loadListRegion();
});

$(document).on("change","#region", function () {
    loadListCity();
});

function loginForm() {
    deleteMessage();
    let html=`  <form action="/" id="login-form" method="post" class="form-check p-0 m-5">
                    <input required type="email" class="form-control" name="email" placeholder="Email address">
                    <input required type="password" class="form-control" name="password" placeholder="Password">
                    <a class="text-decoration-none " id="restore-password" href="#">restore password</a>
                    <button type="submit" name="submit" class="float-right btn mt-4 mb-3 btn-success"><strong>Sign in</strong></button>
                </form>`
    $("#form").html(html);
    $(".sign-in").attr('disabled', true);
    $("#register").attr('disabled', false);
}

$(document).on("click", "#restore-password",function () {
    deleteMessage();
    let html=`  <form action="/" id="restore-form" method="post" class="form-check p-0 m-5">
                    <input required type="email" class="form-control" name="email" placeholder="Email address">
                    <a class="text-decoration-none sign-in"  href="#">Back</a>
                    <button type="submit"  class="float-right btn mt-4 mb-3 btn-success"><strong>Send</strong></button>
                </form>`
    $("#form").html(html);
    $(".sign-in").attr('disabled', true);
    $("#register").attr('disabled', false);
});

