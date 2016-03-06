function displayTable(json) {
    /* clear table first */
    $("#resultsTable").html('');
    for (var i = 0; i < json.length; i++) {
        var obj = json[i];
        var data;
        if (obj.flagged == 0) {
            data = "<tr>"
        } else {
            data = "<tr class='error'>"
        }
        //Add species
        data += "<td>" + obj.species_name + "</td>";
        //Add flagged
        if (obj.flagged == 0) {
            data += "<td></td>";
        } else {
            data += "<td class='centered'><i class='attention icon'></i></td>";
        }
        //Add time classified
        data += "<td>" + obj.time_classified + "</td>";
        //Add time uploaded
        data += "<td>" + obj.taken + "</td>";
        //Add person ID
        data += "<td>" + obj.person_id + "</td>";
        //Add site IDs
        data += "<td>" + obj.site_name + "</td>";
        //Add contains human
        if (obj.contains_human == 0) {
            data += "<td class='centered'><i class='remove icon'></i></td>";
        } else {
            data += "<td class='centered'><i class='checkmark icon'></i></td>";
        }
        //Habit ID
        data += "<td>" + obj.habitat_id + "</td>";
        $(data + "</tr>").appendTo("#resultsTable");
    }
}

//Function for BEN as I cant access database
function testTable() {
    var json = [
{

    "photo_id": "123",
    "species": "87",
    "flagged": "0",
    "time_classified": "2016-02-17 18:45:27",
    "taken": "2015-04-15 14:19:26",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "1",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Human <span class='fa fa-male'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/52964ab57e40e7b371aa301ee37bc5fc.jpg"

},
{

    "photo_id": "124",
    "species": "86",
    "flagged": "0",
    "time_classified": "2016-02-17 18:45:33",
    "taken": "2015-04-15 14:19:27",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "0",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Nothing <span class='fa fa-ban'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/0073904af3b7307759194dbd92cd9130.jpg"

},
{

    "photo_id": "126",
    "species": "86",
    "flagged": "0",
    "time_classified": "2016-02-17 18:45:33",
    "taken": "2015-04-15 14:19:28",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "0",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Nothing <span class='fa fa-ban'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/72b771d8dd8b6acb2fb47bfcf13f383e.jpg"

},
{

    "photo_id": "127",
    "species": "87",
    "flagged": "1",
    "time_classified": "2016-02-17 20:04:06",
    "taken": "2015-04-15 14:22:57",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "1",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Human <span class='fa fa-male'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/c94fa30a015bed16c17a218e8d934bcd.jpg"

},
{

    "photo_id": "129",
    "species": "87",
    "flagged": "1",
    "time_classified": "2016-02-17 18:45:33",
    "taken": "2015-04-15 14:22:59",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "1",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Human <span class='fa fa-male'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/458607bd27e4d7ba3d20025077eb517b.jpg"

},
{

    "photo_id": "134",
    "species": "87",
    "flagged": "0",
    "time_classified": "2016-02-17 18:45:33",
    "taken": "2015-04-15 14:23:05",
    "person_id": "182",
    "site_id": "2",
    "contains_human": "1",
    "habitat_id": "104",
    "site_name": "SBBS Little High Wood",
    "species_name": "Human <span class='fa fa-male'/>",
    "evenness_species": "0",
    "evenness_count": "0",
    "url": "http://www.mammalweb.org/biodivimages/person_182/site_2/7d39735ea6fefbe03f9902a7632d78e9.jpg"

}
];
displayTable(json);
}

//Time since variables
var sinces = ["#sinceYear", "#sinceMonth", "#sinceDay", "#sinceHour", "#sinceMinute", "#sinceSecond"]; //The ids of the time forms
var untils = ["#untilYear", "#untilMonth", "#untilDay", "#untilHour", "#untilMinute", "#untilSecond"]; //IDs of datetime forms


var sinceDefaults = ["1970", "1", "1", "00", "00", "00"]; //What is used if no date/time filled in

var untilDefaults = ["2100", "1", "1", "00", "00", "00"]; //Defaults

function equalArray(arr1, arr2) //Checks if two arrays are equal
{
    var result = true;
    if (arr1.length != arr2.length) {
        result = false;
    }
    for (var i = 0; i < arr1.length; i++) {
        if (arr1[i] != arr2[i]) {
            result = false;
        }
    }
    return result;
}

$("#applyFilterButton").click(function () {
    var filters = {};

    var species_include = $("#dropdownAnimal").val();
    var species_exclude = $("#dropdownNoAnimal").val();
    var habitat_id = $("#dropdownHabitat").val();
    var site_id = $("#dropdownSite").val();

    /* get the fields from the DOM elements and convert to number */
    filters.species_include = species_include == null ? undefined : species_include.map(Number);
    filters.species_exclude = species_exclude == null ? undefined : species_exclude.map(Number);
    filters.habitat_id = habitat_id == null ? undefined : habitat_id.map(Number);
    filters.site_id = site_id == null ? undefined : site_id.map(Number);


    //Since dates
    var sinceValues = ["1970", "1", "1", "00", "00", "00"]; //Where the selected datetime values are held. The values already there allow it so not all datetime divisions (day, minutes etc) have to be chosen
    var untilValues = ["2100", "1", "1", "00", "00", "00"];

    var i;
    for (i = 0; i < sinces.length; i++) {
        if ($(sinces[i]).val() != "") {
            sinceValues[i] = $(sinces[i]).val(); //Store the selected datetime values
        }
    }
    if (equalArray(sinceValues, sinceDefaults) == false) //If the default values and selected values of datetime are different (i.e. a datetime has been chosen)
    {
        filters.taken_start = sinceValues[0] + "-" + sinceValues[1] + "-" + sinceValues[2] + " " + sinceValues[3] + ":" + sinceValues[4] + ":" + sinceValues[5]; //Format date time;
    }

    //Until dates
    for (i = 0; i < untils.length; i++) {
        if ($(untils[i]).val() != "") {
            untilValues[i] = $(untils[i]).val();
        }
    }
    if (equalArray(untilDefaults, untilValues) == false) {
        filters.taken_end  =  untilValues[0] + "-" + untilValues[1] + "-" + untilValues[2] + " "+
            untilValues[3] + ":" + untilValues[4] + ":" + untilValues[5];

    }

    //Deal with dates. Since seems to work if only both start and end date provided, must make sure if only on give, other is also given
    if (filters.taken_start !== undefined) //If a since date given
    {
        if (filters.taken_end === undefined) //If no until date given
        {
            filters.taken_end = "2017-1-1 00:00:00"; //Add a default futer end date
        }
    }
    if (filters.taken_start === undefined) //If no since date given
    {
        if (filters.taken_end !== undefined) //If no until date given
        {
            filters.taken_start = "1970-1-1 00:00:00"; //Add a default since past date
        }
    }


    $.ajax({
        url:     "../backend/src/api/internal/filter.php",
        type:    "POST",
        data:    {"params": JSON.stringify(filters)}, //JSON.stringify({"species_include":$("#dropdownAnimal").val(), "habitat_id":$("#dropdownHabitat").val(), "site_id":$("#dropdownSite").val()})
        success: function (json) {
            displayTable(json.results);

            var csv_filename = json.csv;
            // TODO: add a link to "filter.php?csv=" + csv_filename which will download the csv output
            console.log(csv_filename + "  ");
        },
        error:   function () {
            alert("It does not work...");
        }
    });
});
//Clear buttons - clears and selected filter options
$("#clearSince").click(function () {
    for (var i = 0; i < sinces.length; i++) {
        document.getElementById(sinces[i].substr(1)).value = "";
    }
});
$("#clearUntil").click(function () {
    for (var i = 0; i < untils.length; i++) {
        document.getElementById(untils[i].substr(1)).value = "";
    }
});
$("#clearDropdownAnimal").click(function () {
    $("#dropdownAnimal").dropdown('clear');
});
$("#clearDropdownNoAnimal").click(function () {
    $("#dropdownNoAnimal").dropdown('clear');
});
$("#clearDropdownSite").click(function () {
    $("#dropdownSite").dropdown('clear');
});


$("#clearDropdownHabitat").click(function () {
    $("#dropdownHabitat").dropdown('clear');
});
$(document).ready(function () {

    $('.ui.dropdown')
        .dropdown()
    ;
    $('.ui.accordion')
        .accordion()
    ;
    testTable();
    var filterOptions = ["species", "species", "habitats", "sites"]; //The possible filters
    var dropdownOptions = ["dropdownAnimal", "dropdownNoAnimal", "dropdownHabitat", "dropdownSite"]; //The ids of the possible filters
    function fromAPI(name, num) {
        $.get("../backend/src/api/internal/list.php?item=" + name[num], function (recvdata) {
            var options = "";
            var done = [];
            for (var i in recvdata) {
                if ($.inArray(recvdata[i], done) == -1) {
                    options += "<option value=" + i + ">" + recvdata[i] + "</option>"; //Make each option in html format
                    done.push(recvdata[i]);
                }
            }
            $("#" + dropdownOptions[num]).html(options); //Put the options in the dropdown as html
        });
    }

    for (var j = 0; j < filterOptions.length; j++) {
        fromAPI(filterOptions, j);
    }


});
