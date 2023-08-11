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
use Wardenyarn\Loripsum\Enums\Option;

$lorem = new Loremipsum();

$result = $lorem
    ->with([
        Option::HEADERS,
        Option::UNORDERED_LIST,
        Option::BLOCKQUOTE,
    ])
    ->long()
    ->paragraphs(10)
    ->withImages() // insert random images from placeholder.com
    ->html(); // html with 10 long paragraphs with headings (h1-h6), unordered lists and blockquotes

// OR

$lorem->random($max_paragraphs = 10)->html(); // html with randomly applied options and size
```

## `with()` Options
```php
Wardenyarn\Loripsum\Enums\Option::AVAILABLE_OPTIONS; // Array of available options
```
* `Option::DECORATE` - Add bold, italic and marked text.
* `Option::LINK` - Add links.
* `Option::UNORDERED_LIST` - Add unordered lists.
* `Option::ORDERED_LIST` - Add numbered lists.
* `Option::DESCRIPTION_LIST` - Add description lists.
* `Option::BLOCKQUOTE` - Add blockquotes.
* `Option::CODE` - Add code samples.
* `Option::HEADERS` - Add headers (h1-h6).
* `Option::AS_PLAINTEXT` - Return result as plain text.
* `Option::ALL_CAPS` - Return result in all uppercase letters.
* `Option::PRUDE` - Avoid potentially offensive Latin words (applied as default, you can disable it with `notPrude()` method)

```php
use Wardenyarn\Loripsum\Loremipsum;
use Wardenyarn\Loripsum\Enums\Option;

$lorem = new Loremipsum();

$result = $lorem
    ->with([
        Option::DECORATE,
        Option::LINK,
        Option::UNORDERED_LIST,
        ...
    ])
    ->html();
```

## Sizes
```php
Wardenyarn\Loripsum\Enums\Size::AVAILABLE_SIZES; // Array of available sizes
```
* `Size::SHORT`
* `Size::MEDIUM`
* `Size::LONG`
* `Size::VERY_LONG`

```php
use Wardenyarn\Loripsum\Loremipsum;
use Wardenyarn\Loripsum\Enums\Size;

$lorem = new Loremipsum();

$result = $lorem
    ->short()
    ->medium()
    ->long()
    ->verylong()
    // OR
    ->size(Size::SHORT)
    ->html();
```

## Images
```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum();

$result = $lorem
    ->withImages() // insert random images from placeholder.com
    ->imageChance(60) // Probability percent of inserting image after DOM node; Default: 30%
    ->html();

$result = $lorem
    ->withImages(['/src/1.jpg', '/src/2.jpg']) // Will use given images first
    ->html();
```

## Misc
```php
use Wardenyarn\Loripsum\Loremipsum;

$lorem = new Loremipsum();

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