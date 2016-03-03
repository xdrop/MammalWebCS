var possibleFilters = ["#dropdownAnimal", "#dropdownNoAnimal", "#dropdownHabitat", "#dropdownSite"];
var filterNames = ["species_include", "species_exclude", "habitat_id", "site_id"];

//Time since variables
var sinces = ["#sinceYear", "#sinceMonth", "#sinceDay", "#sinceHour", "#sinceMinute", "#sinceSecond"]; //The ids of the time forms
var sinceDefaults = ["1970", "1", "1", "00", "00", "00"]; //What is used if no date/time filled in

var untils = ["#untilYear", "#untilMonth", "#untilDay", "#untilHour", "#untilMinute", "#untilSecond"]; //IDs of datetime forms
var untilDefaults = ["2100", "1", "1", "00", "00", "00"]; //Defaults

function equalArray(arr1, arr2) //Checks if two arrays are equal
{
    var result = true;
    if(arr1.length != arr2.length)
    {
        result = false;
    }
    for(var i = 0 ; i < arr1.length ; i++)
    {
        if(arr1[i] != arr2[i])
        {
            result = false;
        }
    }
    return result;
}

$("#applyFilterButton").click(function(){
    //Animal include/exclude, site, habitat
    var chosenFilters = ""; //String that stores the selected fillters. Stored as json without the surrounding {}
    var i;
    for(i = 0 ; i < possibleFilters.length ; i++)
    {
        if($(possibleFilters[i]).val() != null) //If a filter has some values chosen
        {
            if(chosenFilters != "")
            {
                chosenFilters += ","; //Only put a comma if there is already some filter options chosen
            }
            chosenFilters += '"' + filterNames[i] + '"' + ":" + "[" + '"' + $(possibleFilters[i]).val() + '"' + "]"; //Format the filter values
        }
    }

    //Since dates
    var sinceValues = ["1970", "1", "1", "00", "00", "00"]; //Where the selected datetime values are held. The values already there allow it so not all datetime divisions (day, minutes etc) have to be chosen
    var sinceDatetime = ""; //Where the datetime string is held
    var untilValues = ["2100", "1", "1", "00", "00", "00"];
    var untilDatetime = "";

    for(i = 0 ; i < sinces.length ; i++)
    {
        if($(sinces[i]).val() != "")
        {
            sinceValues[i] = $(sinces[i]).val(); //Store the selected datetime values
        }
    }
    if(equalArray(sinceValues, sinceDefaults) == false) //If the default values and selected values of datetime are different (i.e. a datetime has been chosen)
    {
        sinceDatetime = '"' + "taken_start" + '"' + ":" + '"' + sinceValues[0] + "-" + sinceValues[1] + "-" + sinceValues[2] + " " + sinceValues[3] + ":" + sinceValues[4] + ":" + sinceValues[5] + '"'; //Format date time
    }

    //Until dates
    for(i = 0 ; i < untils.length ; i++)
    {
        if($(untils[i]).val() != "")
        {
            untilValues[i] = $(untils[i]).val();
        }
    }
    if(equalArray(untilDefaults,untilValues) == false)
    {
        untilDatetime = '"' + "taken_end" + '"' + ":" + '"' + untilValues[0] + "-" + untilValues[1] + "-" + untilValues[2] + " " + untilValues[3] + ":" + untilValues[4] + ":" + untilValues[5] + '"';
    }

    //Deal with dates. Since seems to work if only both start and end date provided, must make sure if only on give, other is also given
    if(sinceDatetime != "") //If a since date given
    {
        if(chosenFilters != "")
        {
            chosenFilters += ",";
        }
        chosenFilters += sinceDatetime; //Add the formatted date to the json string
        if(untilDatetime == "") //If no until date given
        {
            chosenFilters += ', "taken_end":"2017-1-1 00:00:00"'; //Add a default futer end date
        }
    }
    if(untilDatetime != "") //If an until date given
    {
        if(chosenFilters != "")
        {
            chosenFilters += ",";
        }
        if(sinceDatetime == "") //If no since date given
        {
            chosenFilters += '"taken_start":"1970-1-1 00:00:00",'; //Add a default since past date
        }
        chosenFilters += untilDatetime; //Add formatted date to json string
    }

    if(chosenFilters != "") //If some filters chosen
    {
        $.ajax({
            url:"../backend/src/api/internal/filter.php",
            type: "POST",
            data: {"params": JSON.stringify(JSON.parse('{' + chosenFilters + '}'))} , //JSON.stringify({"species_include":$("#dropdownAnimal").val(), "habitat_id":$("#dropdownHabitat").val(), "site_id":$("#dropdownSite").val()})
            success: function(response) {
                var results = "<b>" + response.length + " results</b><br/><br/>";
                var parsed = JSON.parse(JSON.stringify(response));
                for(var i = 0 ; i < parsed.length ; i++)
                {
                    results += "<b>Image " + (i+1) + "</b><br/>";
                    for(var j in parsed[i])
                    {
                        results += j.toString() + ": " + parsed[i][j] + "<br/>"; //Write the results
                    }
                    results += "<br/>";
                }
                $("#results").html(results); //Takes a string and converts to html and placed in #esults
            },
            error: function(){
                alert("It does not work...");
            }
            });
    }
    else
    {
        $("#results").html("No filter selected");
    }
});

//Clear buttons - clears and selected filter options
$("#clearSince").click(function(){
    for(var i = 0 ; i < sinces.length ; i++)
    {
        document.getElementById(sinces[i].substr(1)).value = "";
    }
});
$("#clearUntil").click(function(){
    for(var i = 0 ; i < untils.length ; i++)
    {
        document.getElementById(untils[i].substr(1)).value = "";
    }
});
$("#clearDropdownAnimal").click(function(){
    $("#dropdownAnimal").dropdown('clear');
});
$("#clearDropdownNoAnimal").click(function(){
    $("#dropdownNoAnimal").dropdown('clear');
});
$("#clearDropdownSite").click(function(){
    $("#dropdownSite").dropdown('clear');
});
$("#clearDropdownHabitat").click(function(){
    $("#dropdownHabitat").dropdown('clear');
});


$(document).ready(function(){
function displayTable(json, callback) {
    for (var i = 0; i < json.length; i++) {
        var obj = json[i];
        var data = "";
        //Add Preview (empty atm)
        data += "<tr><td></td>";
        //Add species
        data += "<td>" + obj.species_name + "</td>";
        //Add flagged
        data += "<td>" + "False" + "</td>";
        //Add time classified
        data += "<td>" + "Time classified" + "</td>";
        //Add time uploaded
        data += "<td>" + "Time uploaded" + "</td>";
        //Add person ID
        data += "<td>" + obj.person_id + "</td>";
        //Add site ID
        data += "<td>" + obj.site_name + "</td>";
        //Add contains human
        data += "<td>" + "Human" + "</td>";
        //Habit ID
        data += "<td>" + obj.habitat_id + "</td>";
        $(data + "</tr>").appendTo("#resultsTable");
    }
    callback("hello");
}
    $('.ui.dropdown')
      .dropdown()
    ;
    $('.ui.accordion')
        .accordion()
    ;
    var filterOptions = ["species", "species", "habitats", "sites"]; //The possible filters
    var dropdownOptions = ["dropdownAnimal", "dropdownNoAnimal", "dropdownHabitat", "dropdownSite"]; //The ids of the possible filters

    function fromAPI(name, num)
    {
        //console.log("ONE"); //This is left here as I dont know why logs ONE, THREE, FOUR j times the TWO j times - $.get executes after for loop has finished
        $.get("../backend/src/api/internal/list.php?item=" + name[num], function(recvdata){
            var options = "";
            for(var i in recvdata)
            {
                options += "<option value=" + i + ">" + recvdata[i] + "</option>"; //Make each option in html format
            }
            $("#" + dropdownOptions[num]).html(options); //Put the options in the dropdown as html
            //console.log("TWO");
        });
        //console.log("THREE");
    }

    for(var j = 0 ; j < filterOptions.length ; j++)
    {
        fromAPI(filterOptions, j);
        //console.log("FOUR");
    }
    var filterExample;
    $.getJSON("res/filter_example.json", function(json) {
        filterExample = json;
        displayTable(json, function(message){
            console.log(message);
        });
    });
});
