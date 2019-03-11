<?php
// require_once ('../classes/db.php');
include '../tempelates/user-navbar/user-navbar.php';
include '../tempelates/header.php';
include './functions.php'
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<style>

.mb-0 > a {
  display: block;
  position: relative;
}
.mb-0 > a:after {
  content: "\f078"; /* fa-chevron-down */
  font-family: 'FontAwesome';
  position: absolute;
  right: 0;
  top : -4;
}
.mb-0 > a[aria-expanded="true"]:after {
  content: "\f077"; /* fa-chevron-up */
}
</style>

<h2>Checks</h2>
        <p class="search_input">
            <label>start date</label>
            <input type="date" name="start" id="start">
            <label>end date</label>
            <input type="date" name="end" id="end">
            <Button onclick="FilterCheck()"value="filter" name="submit">Filter</Button>
        </p>
<div id="accordion">
  <?=generateAccordion() ?>
</div>

    <script>
        document.getElementById("end").valueAsDate = new Date();
        let start = new Date();
        start.setDate(start.getDate() - 3)
        document.getElementById("start").valueAsDate = start;
        function FilterCheck(){
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
                console.log(this.responseText);
                console.log("Recieve MSG");
              }
          };
          console.log(startD)
          console.log(endD)
          xmlhttp.open("GET", `functions.php?start=${startD}&end=${endD}`, true);
          xmlhttp.send();
          console.log("SEND") 
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
        }

    </script>

<?php 
include "../tempelates/footer.php"; ?>