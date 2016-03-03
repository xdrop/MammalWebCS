API Reference
--------------

Get a list of species and ids

(For now) 
`GET ./internal/list.php?item=species`

```JSON
{

    "10": "Badger",
    "11": "Blackbird",
    "12": "Domestic cat",
    "13": "Crow",
    "14": "Domestic dog",
    "16": "Grey squirrel",
    "17": "Horse",
    "19": "Magpie",
    "20": "Rabbit",
    "21": "Red fox",
    "22": "Roe Deer",
    "23": "Stoat or Weasel",
    "24": "Woodpigeon",
    "34": "Muntjac",
    "35": "Brown hare",
    "36": "Hedgehog",
    "37": "Pheasant",
    "38": "Jackdaw",
    "39": "Red deer",
    "40": "Fallow deer",
    "41": "Mountain hare",
    "42": "Small rodent (e.g. mouse, vole, rat)",
    "43": "Livestock",
    "44": "Pine marten",
    "45": "Red squirrel",
    "86": "Nothing <span class='fa fa-ban'/>",
    "87": "Human <span class='fa fa-male'/>",
    "159": "Otter"
}
```

`GET ./internal/list.php?item=habitats`

```JSON
{

    "103": "forest - high density forest more than 60% canopy cover",
    "104": "woodland - low density forest less than 60% canopy cover",
    "105": "scrubland - dominated by shrubs, i.e. small to medium woody plants less than 8 m high",
    "106": "heath - a kind of scrubland characterised by open, low-growing woody plants less than 2 m high",
    "107": "grassland - dominated by grasses",
    "108": "marsh - a wetland dominated by herbaceous, i.e. non-woody plants",
    "109": "bog - a wetland with few/no trees, some shrubs, with lots of peat accumulation",
    "110": "swamp - a forested wetland",
    "111": "rocky - lots of bare rocks with little vegetation",
    "113": "coastal - right on the coast, beach",
    "114": "riverbank - right on the riverbank",
    "115": "farmland - pasture, etc.",
    "116": "garden - like a backyard garden, probably right next to a residence",
    "117": "park - recreational place",
    "118": "residential - houses, apartments, etc.",
    "119": "commercial - stores and offices",
    "120": "industrial - factories and warehouses"
}
```

`GET ./internal/list.php?item=sites`

```JSON
{
    "1": "[New site]",
    "2": "SBBS Little High Wood",
    "3": "my backyard",
    "4": "camera 4",
    "5": "[New site]",
    "6": "[New site]",
    "7": "Grey-Science Site",
    "8": "Ustinov woods",
    "9": "[New site]",
    "10": "[New site]",
    "11": "near Josephine Butler allotment",
    "12": "woods near my house",
    "13": "River-cam",
    "...": "....."
}
```


Get filtered results

(For now) `POST ./internal/filter.php`, on field 'params' (ie. params=JSON_below)
(all fields are optional)
```JSON
{
    "species_include": [],
    "species_exclude": [],
    "users_include": [],
    "users_exclude": [],
    "no_of_classifications": 9,
    "no_of_classifications_from": 5,
    "no_of_classifications_to":10,
    "habitat_id": 104,
    "flagged": false,
    "site_id": 2,
    "taken_start": "2014-04-19 14:55:22",
    "taken_end": "2015-04-19 14:55:22",
    "__comment_ignore": "the dates strictly have to be YYYY-MM-DD HH:MM:SS",
    "contains_human": false
}
```


Getting the settings:

`GET ./internal/settings.php?action=get`

Updating the settings (need to set two post fields 'action' and 'settings')

```JSON
    POST ./internal/settings.php
    action=store
    setttings= 
    {
    	"consecutive_expected": 12,
    	"votes_before_consensus": 15,
    	"unreasonable_number_of_species_in_image": 10,
    	"evenness_threshold_species": 0.69,
    	"evenness_threshold_count": 0.7
    }
```
