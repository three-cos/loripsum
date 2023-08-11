<?php

namespace Wardenyarn\Loripsum;

use DOMDocument;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Wardenyarn\Loripsum\Enums\Option;
use Wardenyarn\Loripsum\Enums\Size;

/**
 * A helper class for https://loripsum.net/ API
 */
class LoremIpsum
{
    public const API = 'https://loripsum.net/api';

    /**
     * Array of used text parameters
     */
    protected array $options = [];

    /**
     * Size of paragraphs
     */
    protected string $size = Size::SHORT;

    /**
     * Number of paragraphs
     */
    protected int $length = 5;

    /**
     * From loripsum.net:
     * The original text contains a few instances of words like 'sex' or 'homo'. Personally, we don't mind, because these are just common latin words meaning 'six' and 'man'. However, some people (or your clients) might be offended by this, so if you select the 'Prude version', these words will be censored.
     */
    protected bool $prude = true;

    /**
     * Array of img src values
     */
    protected array $image_sources = [];

    /**
     * Insert images into output
     */
    protected bool $use_images = false;

    /**
     * Percent of probablitity to insert img after DOM node
     */
    protected int $image_chance = 30;

    protected Client $http_client;

    public function __construct()
    {
        $this->http_client = new Client();
    }

    /**
     * @deprecated use paragraphs() instead
     */
    public function length(int $number): self
    {
        return $this->paragraphs($number);
    }

    /**
     * Set number of paragraphs
     */
    public function paragraphs(int $number): self
    {
        if ($number > 0) {
            $this->length = $number;
        }

        return $this;
    }

    /**
     * Set paragraph size
     */
    public function size(string $size): self
    {
        if (Size::isValid($size)) {
            $this->size = $size;
        }

        return $this;
    }

    /**
     * Set paragraph size to short
     */
    public function short(): self
    {
        $this->size = Size::SHORT;

        return $this;
    }

    /**
     * Set paragraph size to medium
     */
    public function medium(): self
    {
        $this->size = Size::MEDIUM;

        return $this;
    }

    /**
     * Set paragraph size to long
     */
    public function long(): self
    {
        $this->size = Size::LONG;

        return $this;
    }

    /**
     * Set paragraph size to very long
     */
    public function verylong(): self
    {
        $this->size = Size::VERY_LONG;

        return $this;
    }

    /**
     * Use only uppercase characters
     */
    public function allcaps(): self
    {
        return $this->with(Option::ALL_CAPS);
    }

    /**
     * Allow to use potentially offensive Latin words
     * @see $prude description.
     */
    public function notPrude(): self
    {
        $this->prude = false;

        return $this;
    }

    /**
     * Set text options
     * @param array|string $options
     */
    public function with($options): self
    {
        if (is_string($options)) {
            $options = (array) $options;
        }

        foreach ($options as $option) {
            if (Option::isValid($option)) {
                $this->options[] = $option;
            }
        }

        return $this;
    }

    /**
     * Set image sources
     */
    public function withImages(array $image_sources = []): self
    {
        $this->image_sources = $image_sources;

        $this->use_images = true;

        return $this;
    }

    /**
     * Set image probability
     */
    public function imageChance(int $chance): self
    {
        if ($chance <= 100 && $chance >= 1) {
            $this->image_chance = $chance;
        }

        return $this;
    }

    /**
     * Apply random size and options
     */
    public function random(int $max_paragraphs = 10): self
    {
        $this->paragraphs(rand(1, $max_paragraphs));

        $used_options_count = rand(1, count(Option::AVAILABLE_OPTIONS) - 1);
        $this->with(Option::getRandomOptions($used_options_count));

        $this->size = Size::getRandom();

        $this->use_images = (bool) rand(0, 1);

        return $this;
    }

    /**
     * Return full API query string
     */
    public function getUrl(): string
    {
        return implode('/', [
            self::API,
            $this->length,
            $this->size,
            ...$this->getOptions(),
        ]);
    }

    /**
     * Return HTML output
     */
    public function html(): string
    {
        $html = $this->get();

        if ($this->use_images) {
            $html = $this->insertImages($html);
        }

        return $html;
    }

    /**
     * Return plain text output
     */
    public function text(): string
    {
        $this->with(Option::AS_PLAINTEXT);

        return $this->get();
    }

    /**
     * Return options URL string
     */
    protected function getOptions(): array
    {
        if ($this->prude && ! in_array(Option::PRUDE, $this->options)) {
            $this->with(Option::PRUDE);
        }

        return $this->options;
    }

    /**
     * Insert images into generated html
     */
    protected function insertImages(string $html): string
    {
        $dom = new Crawler($html);
        $dom_with_images = new DOMDocument();

        foreach ($dom->filter('body > *') as $node) {
            $cloned_node = $dom_with_images->importNode($node, true);
            $dom_with_images->appendChild($cloned_node);

            if ($this->image_chance >= rand(0, 100)) {
                $node_img = $dom_with_images->createElement('img');
                $node_img->setAttribute('src', $this->getImage());

                $dom_with_images->appendChild($node_img);
            }
        }

        return $dom_with_images->saveHtml();
    }

    /**
     * Return image source
     */
    protected function getImage(): string
    {
        return array_shift($this->image_sources) ?? '//via.placeholder.com/'.$this->getImageDimension(rand(640, 1024));
    }

    /**
     * Generate width x height string for placeholder.com
     */
    protected function getImageDimension(int $width): string
    {
        $image_proportions = ['1:1', '2:1', '4:3', '8:5', '16:9', '16:10'];

        shuffle($image_proportions);

        $proportion = reset($image_proportions);

        list($width_proportion, $height_proportion) = explode(':', $proportion);

        $height = floor($width / $width_proportion * $height_proportion);

        return "{$width}x{$height}";
    }

    /**
     * Make a request
     */
    protected function get(): string
    {
        return $this->http_client
            ->request('GET', $this->getUrl())
            ->getBody()
            ->getContents();
    }
}
