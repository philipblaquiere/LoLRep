
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
        type: "get",
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
        error:function(jqXHR, textStatus, errorThrown, responseHeaders){
            alert(errorThrown);
            $("#rune_page_verification_result").html(textStatus + ": " + errorThrown +responseHeaders+jqXHR);
        }
    });
});

//===== in player profile page ======
$("#view-player-recent-matches").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();

    var playerid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');

    $.ajax({
            url: '/LoLRep/ajax/player_recent_matches/' + playerid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team roster " + jqXHR + textStatus + " " + errorThrown );
            }
        });
});


$("#view-player-upcoming-matches").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();
    var playerid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');
    

    $.ajax({
            url: '/LoLRep/ajax/player_upcoming_matches/' + playerid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("Error while loading upcoming matches :" + errorThrown );
            }
        });
});


$("#view-player-stats").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();
    var playerid = $(event.currentTarget).attr('data-id');
    
    /* Clear profile content*/
    $("#main-content").html('');

    $.ajax({
            url: '/LoLRep/ajax/player_stats/' + playerid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team " + textStatus + " " + errorThrown );
            }
        });
});

//===== ------------------------ ======


// ======= in team profile page ===========
$("#view-team-roster").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();

    var teamid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');

    $.ajax({
            url: '/LoLRep/ajax/team_roster/' + teamid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team roster " + jqXHR + textStatus + " " + errorThrown );
            }
        });
});

$("#view-team-recent-matches").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();

    var teamid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');

    $.ajax({
            url: '/LoLRep/ajax/team_recent_matches/' + teamid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team roster " + jqXHR + textStatus + " " + errorThrown );
            }
        });
});

$("#view-team-upcoming-matches").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();

    var teamid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');

    $.ajax({
            url: '/LoLRep/ajax/team_upcoming_matches/' + teamid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team roster " + jqXHR + textStatus + " " + errorThrown );
            }
        });
});

$("#view-team-stats").click(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();

    var teamid = $(event.currentTarget).attr('data-id');
    /* Clear profile content*/
    $("#main-content").html('<div class="row"><div class="col-md-1 col-md-offset-5"><div class="spinner"><i class="fa-li fa fa-spinner fa-spin fa-2x"></i></div></div></div>');

    $.ajax({
            url: '/LoLRep/ajax/team_stats/' + teamid,
            type: "post",
            data: {},
            success: function(data){
                $("#main-content").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#main-content").html("error while loading team stats " + jqXHR + textStatus + " " + errorThrown );
            }
        });
});

//===== ------------------------ ======



//used to set value from left text input dropdown
$(".region-list li a").click(function(event) {
    event.preventDefault();
    var selText = $(this).text();
    $(this).parents('.input-group-btn').find('.dropdown-toggle').html(selText + '  <span class="caret"></span> ');
});

$("#ddlViewBy :selected").val()
$('.datepicker').datepicker();
$('.timepicker').timepicker();
$('#mondaytimepicker').timepicker();
$('#tuesdaytimepicker').timepicker();
$('#wednesdaytimepicker').timepicker();
$('#thursdaytimepicker').timepicker();
$('#fridaytimepicker').timepicker();
$('#saturdaytimepicker').timepicker();
$('#sundaytimepicker').timepicker();
$('#leaguestarttime').timepicker();

$("#mondaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("mondaytime").className = "show";
    }
    else {
        document.getElementById("mondaytime").className = "hidden";
    }
});
$("#tuesdaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("tuesdaytime").className = "show";
    }
    else {
        document.getElementById("tuesdaytime").className = "hidden";
    }
});
$("#wednesdaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("wednesdaytime").className = "show";
    }
    else {
        document.getElementById("wednesdaytime").className = "hidden";
    }
});
$("#thursdaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("thursdaytime").className = "show";
    }
    else {
        document.getElementById("thursdaytime").className = "hidden";
    }
});
$("#fridaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("fridaytime").className = "show";
    }
    else {
        document.getElementById("fridaytime").className = "hidden";
    }
});
$("#saturdaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("saturdaytime").className = "show";
    }
    else {
        document.getElementById("saturdaytime").className = "hidden";
    }
});
$("#sundaycheckbox").change(function() {
    if(this.checked) {
        document.getElementById("sundaytime").className = "show";
    }
    else {
        document.getElementById("sundaytime").className = "hidden";
    }
});


//--------Logic for private/invite only league

$("#inviteonlyleaguecheckbox").change(function() {
    if(!this.checked) {
        document.getElementById("privateleaguecheckbox").checked = false;
    }
});

$("#privateleaguecheckbox").change(function() {
    if(this.checked) {
        document.getElementById("inviteonlyleaguecheckbox").checked = true;
    }
});
//---------



//==== LEAGUE SEARCH =======

$("#league-search-text").keyup(function() {

    var searchtext = document.getElementById("league-search-text").value;
    var notfull = document.getElementById("league-not-full-checkbox").checked;
    var notempty = document.getElementById("league-not-empty-checkbox").checked;
    var inviteonly = document.getElementById("league-invite-only-checkbox").checked;

    $.ajax({
        url: '/LoLRep/ajax/search_leagues',
        type: "post",
        data: { 'notfull' : notfull, 'notempty' : notempty, 'inviteonly' : inviteonly, 'searchtext' : searchtext },
        success: function(data){
            $("#league-search-results").html(data);
        },
        error:function(jqXHR, textStatus, errorThrown){
            $("#league-search-results").html("Error while searching leagues " + jqXHR + textStatus + " " + errorThrown );
        }
    });
});

$("#league-not-full-checkbox").change(function() {
    var searchtext = document.getElementById("league-search-text").value;
    var notfull = this.checked;
    var notempty = document.getElementById("league-not-empty-checkbox").checked;
    var inviteonly = document.getElementById("league-invite-only-checkbox").checked;
    
    $.ajax({
        url: '/LoLRep/ajax/search_leagues',
        type: "post",
        data: { 'notfull' : notfull, 'notempty' : notempty, 'inviteonly' : inviteonly, 'searchtext' : searchtext },
        success: function(data){
            $("#league-search-results").html(data);
        },
        error:function(jqXHR, textStatus, errorThrown){
            $("#league-search-results").html("Error while searching leagues " + jqXHR + textStatus + " " + errorThrown );
        }
    });
});

$("#league-not-empty-checkbox").change(function() {
    var searchtext = document.getElementById("league-search-text").value;
    var notfull = document.getElementById("league-not-full-checkbox").checked;
    var notempty = this.checked;
    var inviteonly = document.getElementById("league-invite-only-checkbox").checked;
    
    $.ajax({
        url: '/LoLRep/ajax/search_leagues',
        type: "post",
        data: { 'notfull' : notfull, 'notempty' : notempty, 'inviteonly' : inviteonly, 'searchtext' : searchtext },
        success: function(data){
            $("#league-search-results").html(data);
        },
        error:function(jqXHR, textStatus, errorThrown){
            $("#league-search-results").html("Error while searching leagues " + jqXHR + textStatus + " " + errorThrown );
        }
    });
});

$("#league-invite-only-checkbox").change(function() {
    var searchtext = document.getElementById("league-search-text").value;
    var notfull = document.getElementById("league-not-full-checkbox").checked;
    var notempty = document.getElementById("league-not-empty-checkbox").checked;
    var inviteonly = this.checked;
    
    $.ajax({
        url: '/LoLRep/ajax/search_leagues',
        type: "post",
        data: { 'notfull' : notfull, 'notempty' : notempty, 'inviteonly' : inviteonly, 'searchtext' : searchtext },
        success: function(data){
            $("#league-search-results").html(data);
        },
        error:function(jqXHR, textStatus, errorThrown){
            $("#league-search-results").html("Error while searching leagues " + jqXHR + textStatus + " " + errorThrown );
        }
    });
});
$("#league-search-result").ready(function(){ 
    
    $.ajax({
            url: '/LoLRep/ajax/search_leagues',
            type: "post",
            data: { 'notfull' : false, 'notempty' : false, 'inviteonly' : false, 'searchtext' : "" },
            success: function(data){
                $("#league-search-results").html(data);
            },
            error:function(jqXHR, textStatus, errorThrown){
                $("#league-search-results").html("Error while searching leagues " + jqXHR + textStatus + " " + errorThrown );
            }
    });
 });


//==== END = LEAGUE SEARCH =======

function loadleagues()
{
    
}



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
    button.setAttribute('id','create_player_button');
    button.setAttribute('value','Register');
    form = document.getElementById('rune_page_verification');
    form.setAttribute('id','create');
    form.setAttribute('action', 'player/create');
}

$('textarea.form-control').maxlength({
            threshold: 20,
            placement: 'bottom-right'
        });