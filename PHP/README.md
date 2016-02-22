# Documentation

### Links

- [API Documentation](https://github.com/xdrop/MammalWebCS/tree/dev/PHP/src/api)
- [Database Documentation](https://github.com/xdrop/MammalWebCS/tree/dev/PHP/src/classes/Database)


### How to use
To use the MammalClassifier class create an instance of it
```php
$classifier = new MammalClassifier();
```
Then call `on()` followed by `classify()` before anything else, supplying the id of the image you want the algorithm run on.

```php
$classifier->on('someimageid')->classify();
```

Afterwards you may call `store()` to store the results in the database.
```php
$classifier->on('someimageid')->classify()->store();
```

Or return the results through an array.

```php
$result = $classifier->on('someimageid')->classify()->getResult();
print_r($result);
```

Output:
```php
(
    [classification] => Array
        (
            [giraffe] => 2,
            [buffalo] => 3,
            [dog] => 3
        )

    [evenness_species] => 0
    [evenness_count] => 0.61938219467876
)
```
As in there are 2 giraffes, 3 buffalos and 3 dogs in the image.



### Testing

To try the algorithm:

```sh
$ git clone https://github.com/xdrop/MammalWebCS.git
```

```sh
$ cd MammalWebCS
$ cd PHP
$ cd src
$ php run.php
```

### Remarks
To integrate this with your database first edit `src/config/db_settings.env.ini` with your database settings:
```ini
[database]
; update for actual database used
driver=mysql
host=localhost
port=3306
name=testdatabase
username=root
password=whatever
```

There are two new added tables on the given mammalweb database for the code to work:

One named `evenness`:
```sql
CREATE TABLE `evenness` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `evenness_species` float NOT NULL,
  `evenness_count` float NOT NULL
)
ALTER TABLE `evenness`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `evenness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
```

and `classified`:
```sql
CREATE TABLE `classified` (
  `id` int(11) NOT NULL,
  `photo_id` int(11) NOT NULL,
  `species` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `flagged` tinyint(1) DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)

ALTER TABLE `classified`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `classified`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
```
