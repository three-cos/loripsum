# Loripsum

Simple https://loripsum.net API helper

## Installation

You can install the package via composer:

```bash
composer require wardenyarn/loripsum
```

## Usage

```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum;

$result = $lorem->with(['headers', 'ul', 'bq'])
    ->long()
    ->length(10)
    ->html(); // html with 10 long paragraphs with headings (h1-h6), unordered lists and blockquotes

// OR

$lorem->random($max_paragraphs = 10)->html(); // html with randomly applied options, size and number of paragraphs
```

## `with()` Options
```php
Wardenyarn\Loripsum\Loremipsum::AVAILABLE_OPTIONS; // Array of available options
```
* decorate - Add bold, italic and marked text.
* link - Add links.
* ul - Add unordered lists.
* ol - Add numbered lists.
* dl - Add description lists.
* bq - Add blockquotes.
* code - Add code samples.
* headers - Add headers.

```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum;

$result = $lorem->with(['decorate', 'link', 'ul', 'ol', 'dl', 'bq', 'code', 'headers'])->html();
```

## Sizes
```php
Wardenyarn\Loripsum\Loremipsum::AVAILABLE_SIZES; // Array of available sizes
```
* short
* medium
* long
* verylong

```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum;

$result = $lorem
    ->short()
    ->medium()
    ->long()
    ->verylong()
    // OR
    ->size('short')
    ->html();
```

## Misc
```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum;

$result = $lorem
    ->notPrude() // Will use potentially offensive Latin words 
    ->html();

$result = $lorem
    ->allcaps() // Will uppercase all output
    ->html();

$result = $lorem->plaintext(); // Will strip tags
```

## Trait
```php
use Wardenyarn\Loripsum\WithLoremIpsum;

class Example
{
    use WithLoremIpsum;

    function factory()
    {
        return [
            'body' = $this->loremIpsum() // returns an instance of Wardenyarn\Loripsum\Loremipsum
                ->random()
                ->html(),
            ...
        ]
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.