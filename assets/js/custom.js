
//ajax call for summoner validation process.
$("#lolSummonerRegistrationForm").submit(function(event) {
	/* Stop form from submitting normally */
    event.preventDefault();

    /* Clear rune page div*/
    $("#authenticate_runepage_page").html('');

    /* Get some values from elements on the page: */
   var summonername = document.getElementById("summonername").value;
   //var region = document.getElementById("region").value;

    /* Send the data using post and put the results in a div */
    $.ajax({
        url: 'index.php?/ajax/authenticate_summoner/' + summonername,
        type: "post",
        data: summonername,
        success: function(data){
            alert("success");
            $("#authenticate_runepage_page").html(data);
        },
        error:function(jqXHR, textStatus, errorThrown){
            alert("failure");
            $("#authenticate_runepage_page").html(summonername + " error " + textStatus + " " + errorThrown );
        }
    });
});

//used to set value from left text input dropdown
$(".dropdown-menu li a").click(function(){
  var selText = $(this).text();
  $(this).parents('.input-group-btn').find('.dropdown-toggle').html(selText + '  <span class="caret"></span> ');
});
