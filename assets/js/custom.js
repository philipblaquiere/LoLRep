

	$("#lolSummonerRegistrationForm").submit(function(event) {
		/* Stop form from submitting normally */
	    event.preventDefault();

	    /* Clear rune page div*/
	    $("#authenticate_runepage_page").html('');

	    /* Get some values from elements on the page: */
	   var summonername = document.getElementById("summonername").value;

	    /* Send the data using post and put the results in a div */
	    $.ajax({
	        //url: "<?= site_url('user/test.php') ?>",
	        url: "application/view/user/test.php",
	        type: "post",
	        data: summonername,
	        success: function(){
	            alert("success");
	            $("#authenticate_runepage_page").html(summonername + " success");
	        },
	        error:function(jqXHR, textStatus, errorThrown){
	            alert("failure");
	            $("#authenticate_runepage_page").html(summonername + " error " + textStatus + " " + errorThrown );
	        }
	    });
	});