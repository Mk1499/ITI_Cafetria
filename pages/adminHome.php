<?php
    include 'tempelates/adminNavbar.php' ;
    require_once('classes/db.php');
    include 'controllers/functions.php';
    if ($_SESSION['userName']=="")
    header('Location: /');
    ?>

    <section class = "container user-home">
     
    <!-- Latest Product -->
    <h2> Add to User </h2>
    <hr />
    <!-- <div class = "row latest-product"> -->
     
    <div class="dropdown-menu">    
    <?php 
     $users=getUsers();
     foreach ($users as $key => $userName) {
      echo "<button class='dropdown-item' id='$key' onclick='userFilter(event)' type='button'>$userName</button>";
    }
    ?>
  <!-- </div> -->

    </div>

     <hr />

        <!-- Our Product  -->
        <div class = "row">

        <div class = "col-sm-5">
           <?php include  'tempelates/order-form/order-form.php'?>
        </div>

        <div class="col-sm-7">
        <div class="row">
            
              <?php include  'tempelates/product/allProduct.php' ?>
      
           
            </div>
        </div>

    </section>





    <script>
        document.getElementById("end").valueAsDate = new Date();
        let start = new Date();
        start.setDate(start.getDate() - 3)
        document.getElementById("start").valueAsDate = start;
        function dateFilter(){
          const startD = document.getElementById("start").value;
          const endD = document.getElementById("end").value;
          console.log(startD);
          console.log(endD);
          const accordionElement = document.getElementById("accordion");
          let xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                while (accordionElement.firstChild) {
                  accordionElement.removeChild(accordionElement.firstChild);
                }
                accordionElement.innerHTML = this.responseText;
                console.log("Recieve MSG");
              }
          };
          xmlhttp.open("GET", `/function?start=${startD}&end=${endD}`, true);
          xmlhttp.send();
          console.log("SEND") 
        }
        function userFilter(event){
          const idOfUser = event.target.id;
          const accordionElement = document.getElementById("accordion");
          let xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                while (accordionElement.firstChild) {
                  accordionElement.removeChild(accordionElement.firstChild);
                }
                accordionElement.innerHTML = this.responseText;
                console.log("Recieve MSG");
              }
          };
          xmlhttp.open("GET", `/function?UID=${idOfUser}`, true);
          xmlhttp.send();
        }
            // let request = $.ajax({
          //   url: "checks.php",
          //   method: "GET",
          //   data: { startD , endD},
          //   dataType: "html"
          // });
          
          // request.done(function( msg ) {
          //   console.log(msg);
          //   // $( "#log" ).html( msg );
          // });
          
          // request.fail(function( jqXHR, textStatus ) {
          //   alert( "Request failed: " + textStatus );
          // });
    </script>
