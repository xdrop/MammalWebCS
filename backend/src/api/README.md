API Reference
--------------

Get a list of species and ids

(For now) 
`GET ./internal/list.php?item=species`

```JSON
[
   {
      "id":"10",
      "name":"Badger"
   },
   {
      "id":"11",
      "name":"Blackbird"
   },
   {
      "id":"12",
      "name":"Domestic cat"
   }
]
```

`GET ./internal/list.php?item=habitats`

```JSON
[
   {
      "id":"103",
      "name":"forest - high density forest more than 60% canopy cover"
   },
   {
      "id":"104",
      "name":"woodland - low density forest less than 60% canopy cover"
   },
   {
      "id":"105",
      "name":"scrubland - dominated by shrubs, i.e. small to medium woody plants less than 8 m high"
   }
]
```

`GET ./internal/list.php?item=sites`

```JSON
[
   {
      "id":"1",
      "name":"[New site]"
   },
   {
      "id":"2",
      "name":"SBBS Little High Wood"
   },
   {
      "id":"3",
      "name":"my backyard"
   }
]
```

`GET ./internal/list.php?item=all`

```JSON
{
   "species":[
      {
         "id":"10",
         "name":"Badger"
      },
      {
         "id":"11",
         "name":"Blackbird"
      },
      {
         "id":"12",
         "name":"Domestic cat"
      }
   ],
   "sites":[
      {
         "id":"1",
         "name":"[New site]"
      },
      {
         "id":"2",
         "name":"SBBS Little High Wood"
      },
      {
         "id":"3",
         "name":"my backyard"
      }
   ],
   "habitats":[
      {
         "id":"103",
         "name":"forest - high density forest more than 60% canopy cover"
      },
      {
         "id":"104",
         "name":"woodland - low density forest less than 60% canopy cover"
      },
      {
         "id":"105",
         "name":"scrubland - dominated by shrubs, i.e. small to medium woody plants less than 8 m high"
      }
   ]
}
```

Species Counts

`GET ./internal/list.php?item=counts`

```JSON
[
   {
      "species":"20",
      "count":"15",
      "name":"Rabbit"
   },
   {
      "species":"22",
      "count":"281",
      "name":"Roe Deer"
   },
   {
      "species":"86",
      "count":"234",
      "name":"Nothing <span class='fa fa-ban'/>"
   },
   {
      "species":"87",
      "count":"7",
      "name":"Human <span class='fa fa-male'/>"
   }
]
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
    "no_of_species" : 1,
    "habitat_id": 104,
    "flagged": false,
    "photo_id": 1039,
    "site_id": 2,
    "taken_start": "2014-04-19 14:55:22",
    "taken_end": "2015-04-19 14:55:22",
    "__comment_ignore": "the dates strictly have to be YYYY-MM-DD HH:MM:SS",
    "contains_human": false,
    "limit" : 10,
    "__comment_ignore": "the limit defines results per page",
    "page" : 1,
    "__comment_ignore": "the page of the results (each page has as many results as limit)"
}
```

Download CSV file
`GET ./internal/filter.php?csv=BY3Jj2nsQi.csv'

Should download the file

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
