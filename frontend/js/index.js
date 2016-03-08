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

$("#applyFilterButton").click(function () {

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
    $masterDrop.dropdown("clear values");

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
