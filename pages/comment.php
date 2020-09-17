<?php
    require_once "../block/header.php";
    require_once "../block/nav.php";?>
    <main class="container mt-5  p-5 bg-light">
        <div class="container" >
            <form action="#" id="form-comment">
                <textarea class="border-dark border form-control" name="text" rows="10" cols="120"></textarea>
                <button type="submit"  class="float-right p-2 mt-2 btn btn-success"><strong>Send</strong></button>
            </form>
        </div>
        <div class="container border-success mt-5 " id="comments">

        </div>
    </main>
    <script src="../script/comments.js"></script>
<?php require_once "../block/footer.php"?>