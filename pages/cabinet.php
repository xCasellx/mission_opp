<?php   require_once "../block/header.php";
        require_once "../block/nav.php"?>;

    <main class="bg-light container">
        <div class="card mt-5">
            <div class="text-center container  card-header"><strong>User information</strong></div>
            <div class="row card-body ">
                <div class="col">
                    <img class="rounded  img-fluid img" src="/image/nan.png" style="width: 480px;height: 480px;" alt="">
                </div>
                <div class="col border-left border-dark">
                    <strong>Name: </strong><label id="user_first_name"> </label><br>
                    <strong>Surname: </strong><label id="user_second_name"></label><br>
                    <strong>Number: </strong><label id="user_number"></label><br>
                    <strong>Date: </strong><label id="user_date"></label><br>
                    <strong>Town: </strong><label id="user_town"></label><br>
                    <strong>Email address: </strong><label id="user_email"></label><br>
                </div>
            </div>
        </div>
    </main>
    <script src="../script/cabinet.js"></script>
    <script src="../script/control.js"></script>
<?php require_once "../block/footer.php"?>