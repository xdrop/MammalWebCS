# Documentation

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
Array
(
    [giraffe] => 2
    [buffalo] => 3
    [dog] => 3
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
Since database integration is not complete yet you may change the test data in the `on()` method
```php
        $this->dataset = [];
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 4, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 4, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 4, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 5, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 5, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 5, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 4, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 4, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 1, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 2, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3, 'giraffe' => 2]);
        $this->dataset[] = new Classification(['giraffe' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 3]);
        $this->dataset[] = new Classification(['dog' => 3]);
        $this->dataset[] = new Classification(['dog' => 3]);
        $this->dataset[] = new Classification(['dog' => 3]);
        $this->dataset[] = new Classification(['dog' => 3]);
        $this->dataset[] = new Classification(['dog' => 3]);
        $this->dataset[] = new Classification(['elephant' => 3]);
        $this->dataset[] = new Classification(['elephant' => 3]);
        $this->dataset[] = new Classification(['elephant' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 2, 'buffalo' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 2, 'buffalo' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 2, 'buffalo' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 3, 'buffalo' => 2]);

```
