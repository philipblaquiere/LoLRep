
    //ajax call for summoner validation process.
    $('#lolSummonerRegistrationForm').submit(function(event) {
    	/* Stop form from submitting normally */
        event.preventDefault();

        /* Clear rune page div*/
        $("#authenticate_runepage_page").html('');
        $("#summoner_validation_error").html('');

        /* Get some values from elements on the page: */
       var summonername = document.getElementById("summonername").value;
       if(summonername == "")
        summonername = "-";
       var region = document.getElementById("region").firstChild.data;
        
        /* Send the data using post and put the results in a div */
        $.ajax({
            url: '/LoLRep/ajax/authenticate_summoner/'+ region +'/'+ summonername.trim(),
            type: "post",
            data: summonername,
            success: function(data){
                $("#authenticate_runepage_page").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#authenticate_runepage_page").html(summonername + " error " + textStatus + " " + errorThrown );
            }
        });
    });

    $(document).on('submit','#rune_page_verification',function(event) {
        /* Stop form from submitting normally */
        event.preventDefault();

        /* Clear any previous error message*/
        $("#rune_page_verification_result").html('');

        /* Send the data using post and put the results in a div */
        $.ajax({
            url: '/LoLRep/ajax/rune_page_verification',
            type: "post",
            data: {},
            success: function(data){
                if(data == "success") {
                    //verification succeeded, create user
                    switchButtonToRegister();
                }
                else {
                    $("#rune_page_verification_result").html(data);
                }
            },
            error:function(jqXHR, textStatus, errorThrown){
                alert(errorThrown);
                $("#rune_page_verification_result").html(textStatus + ": " + errorThrown );
            }
        });
    });

$('#lolteamsearchform').submit(function(event) {
        /* Stop form from submitting normally */
        event.preventDefault();

        /* Clear rune page div*/
        $("#team_lol_search_result").html('');

        /* Get some values from elements on the page: */
       var teamname = document.getElementById("teamname").value;
       if(teamname == "")
        teamname = "-";
        
        /* Send the data using post and put the results in a div */
        $.ajax({
            url: '/LoLRep/ajax/find_team_lol/' + teamname.trim(),
            type: "post",
            data: teamname,
            success: function(data){
                $("#team_lol_search_result").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#team_lol_search_result").html(summonername + " error " + textStatus + " " + errorThrown );
            }
        });
    });

//used to set value from left text input dropdown
$(".region-list li a").click(function(event) {
    event.preventDefault();
    var selText = $(this).text();
    $(this).parents('.input-group-btn').find('.dropdown-toggle').html(selText + '  <span class="caret"></span> ');
});

function reloadLoLRegister(message) {
    alert("in reload");
    $.ajax({
        url: '/LoLRep/add_esport/register_LoL',
        type: "post",
        data: {},
        success: function(data){
            $("#authenticate_runepage_page").html(message);
        }
    });
}

function switchButtonToRegister(){
    button = document.getElementById('rune_page_verification_button');
    button.setAttribute('id','create_summoner_button');
    button.setAttribute('value','Register');
    form = document.getElementById('rune_page_verification');
    form.setAttribute('id','create_summoner');
    form.setAttribute('action','index.php?/user/create_summoner');
}