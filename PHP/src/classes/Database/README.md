Database Interaction Documentation
===================


To interact with the database you can create new classes extending the `Query` class. These "queries" are not really queries themselves, they are sets of real sql queries describing how we obtain data for a specific purpose.

```php
class MyNewQuery extends Query {
}
```

Queries can run four operations:
- Fetch  *(Retrieves data from the database)*
- Store  *(Stores data into the database)*
- Update *(Updates data in the database)*
- Delete *(Deletes data from the database)*

You may choose to implement any that you need for any given query. As `Query` is abstract you need to implement all four methods (however fill only the ones you need):

```php
class MyNewQuery extends Query {

	protected function fetchQuery(&$params){
		// TODO: Implement fetchQuery() method
	}

	protected function storeQuery(&$params){
		// TODO: Implement storeQuery() method
	}

	protected function updateQuery(&$params)
    {
        // TODO: Implement updateQuery() method.
    }

    protected function deleteQuery(&$params)
    {
        // TODO: Implement deleteQuery() method.
    }

}
```

In these methods you can set what you want the query to be doing if it's asked to do one of the relevant actions (`fetch()` for example).

The `$params` argument specifies the parameters to that query and you can use it any way you like.



----------


Fetch
-------------

To set a fetch query for example you need to call `$this->addFetchQuery()` in the `fetchQuery()` method.

```php
	protected function fetchQuery(&$params){

	    /* Query
	        SELECT (options_name,options_id) FROM options
	            WHERE 'options_id' = ? (The parameter provided)
	     */

		$query = $this->db->from('options')
					 ->select('options_name')
					 ->select('options_id');
					 ->where('options_id ', $params['optionId']) // assuming whoever calls this passes an optionsId in the params array

		/* Add the query */
		$this->addFetchQuery($query);
		
		/* From now on whenever someone calls fetch() on the query object,
		   the query you just added will be run. */

	}
```


Similarly for `store()`, `update()` etc. there are `addStoreQuery`, `addUpdateQuery`, and `addDeleteQuery`, and the format is the same.



> **Note:**

For creating the actual query you can access the database using `$this->db` and then use methods on it. (See [FluentPDO](http://fpdo.github.io/fluentpdo/) on how exactly).

Reformat
-------------
The last method `reformat()` you can reimplement (override) takes the results from your query as input and reformats them in some way you specify in the method and then returns them. 
It's not necessary to override it if you feel that the results are already in the correct format.

Example:
```php
protected function reformat($results)
    {
    	if(!is_null($results)){
    		$map = [];
    		foreach($results as $entry){
    			$map[$entry[self::OPTION_ID]] = $entry[self::OPTION_NAME];
    		}
    		return $map; // don't forget to return back the results!
    	} else{
    	    return []; 
    	}
    }
```

Using our query
-----------------

To use your query either in another class or to test all you need to do is create an instance your query, pass in the arguments using `with()` and call the method you wish to use on it (`fetch()`,`store()`,`update()`,`delete()`).

```php
$myQuery = new MyNewQuery();

$results = $myQuery->with(['id' => 2] /* Some parameters to the query */)->fetch();
```

You can also export results in CSV using `fetchCSV()` or in JSON using `fetchJSON()`.