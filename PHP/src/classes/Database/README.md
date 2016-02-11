Database Interaction Documentation
===================


To interact with the database you can create new classes extending the `Query` class.

```
class MyNewQuery extends Query {
}
```

Queries can run four operations:
- Fetch  *(Retrieves data from the database)*
- Store  *(Stores data into the database)*
- Update *(Updates data in the database)*
- Delete *(Deletes data from the database)*

You may choose to implement any that you need for any given query. As `Query` is abstract you need to implement five methods:

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

    protected function reformat($results)
    {
		// TODO: Implement reformat() method.
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
	     */

		$query = $this->db->from(self::OPTIONS_TABLE_NAME)
					 ->select(self::OPTION_NAME)
					 ->select(self::OPTION_ID);

		/* Add the query */
		$this->addFetchQuery($query);
		
		/* From now on whenever someone calls fetch() on the query object,
		   the query you just added will be run. */

	}
```


Similarly for `store()`, `update()` etc. there are `addStoreQuery`, `addUpdateQuery`, and `addDeleteQuery`, and the format is the same.



> **Note:**

For creating the actual query you can access the database using `$this->db` and then use methods on it. (See [FluentPDO](http://lichtner.github.io/fluentpdo/) on how exactly).

Reformat
-------------
The last method `reformat()` you need to implement takes the results from your query as input and reformats them in some way you specify in the method and then returns them. It's not nessecary to fill it in if you feel that the results are already in the correct format.

Example:
```php
protected function reformat($results)
    {
    	if(!is_null($results)){
    		$map = [];
    		foreach($results as $entry){
    			$map[$entry[self::OPTION_ID]] = $entry[self::OPTION_NAME];
    		}

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
