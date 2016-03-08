var filters = {};
var species_include = [];

function displayTable(json) {
    /* clear table first */
    $("#resultsTable").html('');
    for (var i = 0; i < json.length; i++) {
        var obj = json[i];
        a = document.createElement('a');
        a.href = obj.url; // Insted of calling setAttribute
        a.target = "_blank";
        a.innerHTML = "View" // <a>INNER_TEXT</a>
        if (obj.flagged == 0) {
            data = "<tr class='center aligned'>"
        } else {
            data = "<tr class='center aligned error'>"
        }
        data += "<td>" + a.outerHTML + "</td>";
        //Add flagged
        if (obj.flagged == 0) {
            data += "<td></td>";
        } else {
            data += "<td class='centered'><i class='flag icon'></i></td>";
        }
        //Add species
        data += "<td>" + obj.species_name + "</td>";
        //Add time classified
        data += "<td>" + obj.time_classified + "</td>";
        //Add person ID
        data += "<td>" + obj.person_id + "</td>";
        //Add site IDs
        data += "<td>" + obj.site_name + "</td>";

        $(data + "</tr>").appendTo("#resultsTable");
    }
}

// //Time since variables
// var sinces = ["#sinceYear", "#sinceMonth", "#sinceDay", "#sinceHour", "#sinceMinute", "#sinceSecond"]; //The ids of the time forms
// var untils = ["#untilYear", "#untilMonth", "#untilDay", "#untilHour", "#untilMinute", "#untilSecond"]; //IDs of datetime forms


// var sinceDefaults = ["1970", "1", "1", "00", "00", "00"]; //What is used if no date/time filled in

// var untilDefaults = ["2100", "1", "1", "00", "00", "00"]; //Defaults

// function equalArray(arr1, arr2) //Checks if two arrays are equal
// {
//     var result = true;
//     if (arr1.length != arr2.length) {
//         result = false;
//     }
//     for (var i = 0; i < arr1.length; i++) {
//         if (arr1[i] != arr2[i]) {
//             result = false;
//         }
//     }
//     return result;
// }

$("#applyFilterButton").click(function () {
    // var species_include = $("#dropdownAnimal").val();
    // var species_exclude = $("#dropdownNoAnimal").val();
    // var habitat_id = $("#dropdownHabitat").val();
    // var site_id = $("#dropdownSite").val();

    // /* get the fields from the DOM elements and convert to number */
    // filters.species_include = species_include == null ? undefined : species_include.map(Number);
    // filters.species_exclude = species_exclude == null ? undefined : species_exclude.map(Number);
    // filters.habitat_id = habitat_id == null ? undefined : habitat_id.map(Number);
    // filters.site_id = site_id == null ? undefined : site_id.map(Number);


    // //Since dates
    // var sinceValues = ["1970", "1", "1", "00", "00", "00"]; //Where the selected datetime values are held. The values already there allow it so not all datetime divisions (day, minutes etc) have to be chosen
    // var untilValues = ["2100", "1", "1", "00", "00", "00"];

    // var i;
    // for (i = 0; i < sinces.length; i++) {
    //     if ($(sinces[i]).val() != "") {
    //         sinceValues[i] = $(sinces[i]).val(); //Store the selected datetime values
    //     }
    // }
    // if (equalArray(sinceValues, sinceDefaults) == false) //If the default values and selected values of datetime are different (i.e. a datetime has been chosen)
    // {
    //     filters.taken_start = sinceValues[0] + "-" + sinceValues[1] + "-" + sinceValues[2] + " " + sinceValues[3] + ":" + sinceValues[4] + ":" + sinceValues[5]; //Format date time;
    // }

    // //Until dates
    // for (i = 0; i < untils.length; i++) {
    //     if ($(untils[i]).val() != "") {
    //         untilValues[i] = $(untils[i]).val();
    //     }
    // }
    // if (equalArray(untilDefaults, untilValues) == false) {
    //     filters.taken_end = untilValues[0] + "-" + untilValues[1] + "-" + untilValues[2] + " " +
    //         untilValues[3] + ":" + untilValues[4] + ":" + untilValues[5];

    // }

    // //Deal with dates. Since seems to work if only both start and end date provided, must make sure if only on give, other is also given
    // if (filters.taken_start !== undefined) //If a since date given
    // {
    //     if (filters.taken_end === undefined) //If no until date given
    //     {
    //         filters.taken_end = "2017-1-1 00:00:00"; //Add a default futer end date
    //     }
    // }
    // if (filters.taken_start === undefined) //If no since date given
    // {
    //     if (filters.taken_end !== undefined) //If no until date given
    //     {
    //         filters.taken_start = "1970-1-1 00:00:00"; //Add a default since past date
    //     }
    // }
    filters.species_include = species_include;
    alert(JSON.stringify(filters));
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
$(document).ready(function () {
    $('#datebtn').daterangepicker({
        timePicker: true,
        timePickerSeconds: true,
        timePickerIncrement: 1,
        locale: {
            format: 'MM/DD/YYYY h:mm:ss  A'
        }
    });

    var $masterDrop = $("#masterDrop");
    var $speciesDrop = $("#speciesDrop");
    var $habitatDrop = $("#habitatDrop");
    var $siteDrop = $("#siteDrop");


    $masterDrop.dropdown({action: function() {}  });

    $speciesDrop.dropdown({
        action: function(text, value) {
            $masterDrop.dropdown("add value", "animal-" +value, "Include: " + text);
            $masterDrop.dropdown("add label", "animal-" + value, "Include: " + text,"green");
            $masterDrop.dropdown("set selected", value);
            $speciesDrop.dropdown("action hide");
            if ($.inArray(parseInt(value), species_include) == -1) {
                species_include.push(parseInt(value));
            }
        },
        fields:      {name: "name", value: "id"},
        apiSettings: {
            url:        '../backend/src/api/internal/list.php?item=species',
            onResponse: function (response) {
                return {success: true, results: response};
            }
        }
    });

    $habitatDrop.dropdown({
        action: function(text, value) {
            $masterDrop.dropdown("add value", "habitat-" +value, "Habitat: " + text);
            $masterDrop.dropdown("add label", "habitat-" + value,
                "Habitat: " + text.substr(0,text.indexOf(' ')),"blue");
            $masterDrop.dropdown("set selected", value);
            $habitatDrop.dropdown("action hide");
        },
        fields:      {name: "name", value: "id"},
        apiSettings: {
            url:        '../backend/src/api/internal/list.php?item=habitats',
            onResponse: function (response) {
                return {success: true, results: response};
            }
        }
    });

    $siteDrop.dropdown({
        action: function(text, value) {
            $masterDrop.dropdown("add value", "site-" +value, "Site: " + text);
            $masterDrop.dropdown("add label", "site-" + value,
                "Site: " + text, "yellow");
            $masterDrop.dropdown("set selected", value);
            $habitatDrop.dropdown("action hide");
        },
        fields:      {name: "name", value: "id"},
        apiSettings: {
            url:        '../backend/src/api/internal/list.php?item=sites',
            onResponse: function (response) {
                return {success: true, results: response};
            }
        }
    });
});
