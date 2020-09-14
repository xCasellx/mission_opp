<?php require_once "block/header.php"?>
    <main role="main" class="d-flex align-items-center justify-content-center  container">
        <div class="border-auto  border-success mb-5 bg-light  ">
            <div class="row mt-3 ">
                <button disabled="true" class="text-white  btn  text-center col p-2 bg-success" id="sign-in" style="border-radius: 60px  0px 0px 60px">
                   <strong>Sign in</strong>
                </button>
                <button id="register" class= "text-white btn col p-2 bg-success" style="border-radius:0px 60px 60px 0px">
                    <strong>Register</strong>
                </button>
            </div>
            <div class="status-message text-center mt-2 p-2">

            </div>
            <div id="form" class="mt-0 pt-0">
            </div>
        </div>
    </main>
    <script src="script/control.js"></script>
    <script src="script/authorization.js"></script>
<?php require_once "block/footer.php"?>