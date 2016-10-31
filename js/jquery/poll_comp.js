// Global variable definitions
var OPT_ID    = 'id';
var OPT_TITLE = 'name';
var OPT_VOTES = 'count';

var votedID;

$(document).ready(function(){
	$("form.poll").each(function (i) {
	    var voting_id = $(this).attr('id');
	    voting_id = voting_id.replace("poll",'');
		var action = $("form#poll"+voting_id).attr('action');

		$("form#poll"+voting_id).submit(formProcess);

		if ($("#poll-results"+voting_id).length > 0 ) {
			animateResults(voting_id);
		}

	});


});

function intval( mixed_var) {

    if(isNaN(parseInt(mixed_var))){
        return 0;
    } else{
        return parseInt(mixed_var);
    }
}

function formProcess(event){
	event.preventDefault();
	var voting_id = $(this).attr('id');
	voting_id = voting_id.replace("poll",'');
	var action = $("form#poll"+voting_id).attr('action');

	var id = $("form#poll"+voting_id+" input[@name='poll']:checked").attr("value");
	id = id.replace("opt",'');

	$("#poll-container"+voting_id).fadeOut("slow",function(){
		$(this).html('идет загрузка...');

		votedID = id;
		$.getJSON(action + "?voting=" + voting_id + "&vote="+id,function(data){
  			loadResults(data, voting_id);
    	});

		$.cookie('voting'+voting_id, id, {expires: 365});
	});
}

function animateResults(voting_id){
	$("#poll-results"+voting_id+" div.result_bar").each(function(){
		var percentage = $(this).next().text();
		$(this).css({width: "0%"}).animate({
			width: percentage}, 2500);
	});
}

function loadResults(data,voting_id) {
	var total_votes = 0;
	var percent;

	for (id in data) {
		total_votes = total_votes+intval(data[id][OPT_VOTES]);
	}


	var results_html = "<div class='poll_results_container' id='poll-results"+voting_id+"'>Результаты голосования\n<div class='graph'>\n";
	var answersLegend="";
	var counter=0;
	for (id in data) {
	    counter++;
	    if (intval(total_votes)>0)
			percent = Math.round((intval(data[id][OPT_VOTES])/intval(total_votes))*100);
		else
			percent = 0;
		if (data[id][OPT_ID] !== votedID) {
			results_html = results_html+"<b>"+counter+".</b> <div class='bar-container'><div class='result_bar' id='bar"+data[id][OPT_ID]+"' style='width:0%;'>&nbsp;</div><strong>"+percent+"%</strong></div><div class='clear'></div>\n";
			answersLegend = answersLegend + "<div class='bar-title'>"+counter+". "+data[id][OPT_TITLE]+"</div>";
		} else {
			results_html = results_html+"<b>"+counter+".</b> <div class='bar-container'><div class='result_bar' id='bar"+data[id][OPT_ID]+"'style='width:0%;background-color:#0066cc;'>&nbsp;</div><strong>"+percent+"%</strong></div><div class='clear'></div>\n";
			answersLegend = answersLegend + "<div class='bar-title'>"+counter+". "+data[id][OPT_TITLE]+"</div>";
		}
	}

	results_html = results_html+"</div><div class='clear'></div><div>Всего голосов: "+total_votes+"</div></div>\n<br />";
    results_html = answersLegend + "<br/><br/>" + results_html;

	$("#poll-container"+voting_id).empty();
	$("#poll-container"+voting_id).append(results_html).fadeIn(2000,function(){
		animateResults(voting_id);});
}