var filters = {};
var species_include = [];
var species_exclude = [];
var habitats = [];
var sites = [];

function displayTable(json) {
	if(json == "NO RESULT")
	{
		$("#resultsTable").html("<tr class='center aligned'><td colspan='6' class='align right'><b>No results</b></td></tr>");
	}
	else 
	{
		/* clear table first */
		$("#resultsTable").html('');
		for (var i = 0; i < json.length; i++) {
			var obj = json[i];
			a = document.createElement('a');
			a.setAttribute("data-lightbox", ""+i);
			a.setAttribute("data-title", obj.species_name);
			a.href = obj.url; // Insted of calling setAttribute
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
			//Add time taken
			data += "<td>" + obj.taken + "</td>";
			//Add person ID
			data += "<td>" + obj.person_id + "</td>";
			//Add site IDs
			data += "<td>" + obj.site_name + "</td>";

			$(data + "</tr>").appendTo("#resultsTable");
		}
	}
}

$("#applyFilterButton").click(function () {
	if(species_include.length != 0)
	{
		filters.species_include = species_include;
	}
	if(species_exclude.length != 0)
	{
		filters.species_exclude = species_exclude;
	}
	if(habitats.length != 0)
	{
		filters.habitat_id = habitats;
    }
	if(sites.length != 0)
	{
		filters.site_id = sites;
    }
	//alert(JSON.stringify(filters));
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
            //alert("It does not work...");
			displayTable("NO RESULT");
        }
    });
	filters = {}; //reset filters
});
$(document).ready(function () {
    var $masterDrop = $("#masterDrop");
    var $speciesIncludeDrop = $("#speciesIncludeDrop");
    var $speciesExcludeDrop = $("#speciesExcludeDrop");
    var $habitatDrop = $("#habitatDrop");
    var $siteDrop = $("#siteDrop");

	$("#clearMaster").click(function(){
		$masterDrop.dropdown('clear');
	});
	
	
    $masterDrop.dropdown({action: function() {}  });
    $masterDrop.dropdown("clear values");

	$masterDrop.dropdown({onChange: function(value,text){ //Whenever there is a change in the main dropdown 
			species_include.length = 0; //Resets the array. arr = [] does not work.
			species_exclude.length = 0;
			habitats.length = 0;
			sites.length = 0;
			values = $masterDrop.dropdown("get values");
			//var values = value.split(',');
			for(var i = 0 ; i < values.length ; i++)
			{
				var val = values[i].split('-');
				var filterCategory = val[0];
				var filterValue = val[1];
				
				if(filterCategory == "animal")
				{
					species_include.push(parseInt(filterValue));
				}
				else if(filterCategory == "no_animal")
				{
					species_exclude.push(parseInt(filterValue));
				}
				else if(filterCategory == "habitat")
				{
					habitats.push(parseInt(filterValue));
				}
				else if(filterCategory == "site")
				{
					sites.push(parseInt(filterValue));
				}
			}
		}
	});
	
	
    $speciesIncludeDrop.dropdown({
        action: function(text, value) {
            $masterDrop.dropdown("add value", "animal-" +value, "Include: " + text);
            $masterDrop.dropdown("add label", "animal-" + value, "Include: " + text,"green");
            $masterDrop.dropdown("set selected", value);
            $speciesIncludeDrop.dropdown("action hide");
        },
        fields:      {name: "name", value: "id"},
        apiSettings: {
            url:        '../backend/src/api/internal/list.php?item=species',
            onResponse: function (response) {
                return {success: true, results: response};
            }
        }
    });
	
	$speciesExcludeDrop.dropdown({
        action: function(text, value) {
            $masterDrop.dropdown("add value", "no_animal-" +value, "Exclude: " + text);
            $masterDrop.dropdown("add label", "no_animal-" + value, "Exclude: " + text,"red");
            $masterDrop.dropdown("set selected", value);
            $speciesExcludeDrop.dropdown("action hide");
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
	
	
	$("#dateform").submit(function(){
		alert($("#dateFormInput").val());
	});
});
