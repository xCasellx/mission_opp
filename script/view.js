$(document).ready(function () {


});



$(".sign-in").on("click",function() {
    let html=`<div class="container">
                        <form action="#" id="login-form" method="post">
                            <input required type="email" name="email" class="form-control mb-2" placeholder="Email address">
                            <input required type="password" name="password" class="form-control mb-2" placeholder="Password">
                            <button type="submit" class="btn bg-info text-light float-right">Sing in</button>
                        </form>
                </div>`
    $("#modal-title-h").html("<strong>Sign in</strong>");
    $(".modal-body").html(html);
});

$('#myModal').on('hide.bs.modal', function() {
    $(".modal-body").html("");
    $("#modal-title-h").html("");
});

$(".register").on("click",function() {
    let html=`<div class="container">
                        <form action="#" method="post">
                            <input type="text" name="first_name" class="form-control mb-2" placeholder="Email address">
                            <input type="text" name="second_name" class="form-control mb-2" placeholder="Email address">
                            <input type="text" name="" class="form-control mb-2" placeholder="Email address">                   
                            <input type="email" name="email" class="form-control mb-2" placeholder="Email address">
                            <input type="password" name="password" class="form-control mb-2" placeholder="Password">
                            <button type="submit" class="btn bg-info text-light float-right">Sing in</button>
                        </form>
                </div>`
});
