
<?php

require_once 'app/helpers.php';



/* Session hijacking - store user ip and user agent + session_regenerate*/
sess_start('bake');
$page_title = 'Home Page';
?>
<?php include 'tpl/header.php'?>
        <main style="min-height: 800px;">
            <div class="container">
                <div class="row mt-5">
                    <div class="col-sm-6 m-auto text-center">
                        <h1 class="display-2">Cooking made simple</h1>
                        <p class="mt-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, provident!</p>
                        <p><a href="our_products.php" class="btn btn-outline-success btn-lg">START NOW!</a></p>
                    </div>
                </div>
                
    
            </div>
        </main>
<?php include 'tpl/footer.php'?>
