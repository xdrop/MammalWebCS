API Reference
--------------

Get a list of species and ids

(For now) 
`GET ./internal/animals.php?action=list`

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
    "habitat_id": 104,
    "flagged": false,
    "site_id": 2,
    "taken_start": "2014-04-19 14:55:22",
    "taken_end": "2015-04-19 14:55:22",
    "__comment_ignore": "the dates strictly have to be YYYY-MM-DD HH:MM:SS",
    "contains_human": false
}
```