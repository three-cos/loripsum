<?php 

namespace Wardenyarn\Loripsum;

use Symfony\Component\DomCrawler\Crawler;

/**
 * A helper class for https://loripsum.net/ API
 */
class LoremIpsum 
{
	const API = 'https://loripsum.net/api';
	const AVAILABLE_OPTIONS = ['decorate', 'link', 'ul', 'ol', 'dl', 'bq', 'code', 'headers', 'plaintext'];
	const AVAILABLE_SIZES   = ['short', 'medium', 'long', 'verylong'];

	/**
	 * Array of used text parameters
	 * @var array
	 */
	protected $options = [];

	/**
	 * Size of paragraphs
	 * @var string
	 */
	protected $size = 'short';

	/**
	 * Number of paragraphs
	 * @var integer
	 */
	protected $length = 5;

	/**
	 * From loripsum.net:
	 * The original text contains a few instances of words like 'sex' or 'homo'. Personally, we don't mind, because these are just common latin words meaning 'six' and 'man'. However, some people (or your clients) might be offended by this, so if you select the 'Prude version', these words will be censored.
	 * @var boolean
	 */
	protected $prude = true;

	/**
	 * Array of img src values
	 * @var array
	 */
	protected $image_sources = [];

	/**
	 * Insert images into output 
	 * @var boolean
	 */
	protected $use_images = false;

	/**
	 * Percent of probablitity to insert img after DOM node
	 * @var integer
	 */
	protected $image_chance = 30;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $http_client;

	public function __construct()
	{
		$this->http_client = new \GuzzleHttp\Client();
	}

	/**
	 * Set number of paragraphs
	 * @param  int    $num
	 * @return self
	 */
	public function length(int $num)
	{
		$this->length = $num;

		return $this;
	}

	/**
	 * Set paragraph size
	 * @param  string $size
	 * @return self
	 */
	public function size(string $size)
	{
		if (in_array($size, self::AVAILABLE_SIZES)) {
			$this->size = $size;
		}

		return $this;
	}

	/**
	 * Set paragraph size to short
	 * @return self
	 */
	public function short()
	{
		$this->size = 'short';

		return $this;
	}

	/**
	 * Set paragraph size to medium
	 * @return self
	 */
	public function medium()
	{
		$this->size = 'medium';
		
		return $this;
	}

	/**
	 * Set paragraph size to long
	 * @return self
	 */
	public function long()
	{
		$this->size = 'long';
		
		return $this;
	}

	/**
	 * Set paragraph size to very long
	 * @return self
	 */
	public function verylong()
	{
		$this->size = 'verylong';
		
		return $this;
	}

	/**
	 * Use only uppercase characters
	 * @return self
	 */
	public function allcaps()
	{
		$this->options[] = 'allcaps';
		
		return $this;
	}

	/**
	 * Allow to use potentially offensive Latin words
	 * @see $prude description.
	 * @return self
	 */
	public function notPrude()
	{
		$this->prude = false;

		return $this;
	}

	/**
	 * Set text options
	 * @param  mixed $options
	 * @return self
	 */
	public function with($options)
	{
		if (is_array($options)) {
			$this->options = $options;
		} else {
			$this->options[] = $options;
		}
		
		return $this;
	}

	/**
	 * Set image sources
	 * @return self
	 */
	public function withImages($image_sources = [])
	{
		$this->image_sources = $image_sources;

		$this->use_images = true;

		return $this;
	}

	/**
	 * Set image probability
	 * @param  integer $chance
	 * @return self
	 */
	public function imageChance(int $chance)
	{
		if ($chance <= 100 & $chance >= 1) {
			$this->image_chance = $chance;
		}

		return $this;
	}

	/**
	 * Insert images into generated html
	 * @param  string $html
	 * @return string
	 */
	protected function insertImages($html)
	{
		$dom = new Crawler($html);
		$dom_with_images = new \DOMDocument;

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
	 * @return string 
	 */
	protected function getImage()
	{
		return array_shift($this->image_sources) ?? '//via.placeholder.com/'.$this->getImageDimension(rand(640, 1024));
	}

	/**
	 * Generate width x height string for placeholder.com
	 * @param  int $width
	 * @return string
	 */
	protected function getImageDimension($width)
	{
		$image_proportions = ['1:1', '2:1', '4:3', '8:5', '16:9', '16:10'];
		
		shuffle($image_proportions);

		$proportion = reset($image_proportions);

		list($width_proportion, $height_proportion) = explode(':', $proportion);

		$height = floor($width / $width_proportion * $height_proportion);

		return "{$width}x{$height}";
	}

	/**
	 * Apply random size and options
	 * @return self
	 */
	public function random(int $max_paragraphs = 10)
	{
		$this->length(rand(1, $max_paragraphs));

		$used_options = rand(1, count(self::AVAILABLE_OPTIONS) - 1);

		$random_options = ($used_options === 1) 
			? [rand(0, count(self::AVAILABLE_OPTIONS) - 1)]
			: array_rand(self::AVAILABLE_OPTIONS, $used_options);

		foreach ($random_options as $option) {
			if (in_array($option, $random_options)) {
				$this->options[] = self::AVAILABLE_OPTIONS[$option];
			}
		}

		$random_size = rand(0, count(self::AVAILABLE_SIZES) - 1);
		$this->size = self::AVAILABLE_SIZES[$random_size];

		$this->use_images = rand(0, 1) ? true : false;

		return $this;
	}

	/**
	 * Return options URL string 
	 * @return string
	 */
	protected function getOptions()
	{
		if ($this->prude) {
			$this->options[] = 'prude';
		}

		$this->options = array_filter($this->options, function($option) {
			return in_array($option, self::AVAILABLE_OPTIONS);
		});

		return implode('/', $this->options);
	}

	/**
	 * Return full API query string
	 * @return string
	 */
	public function getUrl()
	{
		return self::API.'/'.$this->length.'/'.$this->size.'/'.$this->getOptions();
	}

	/**
	 * Make a request
	 * @return string
	 */
	protected function get()
	{
		return $this->http_client->request('GET', $this->getUrl())->getBody()->getContents();
	}

	/**
	 * Return HTML output
	 * @return string
	 */
	public function html()
	{
		$html = $this->get();

		if ($this->use_images) {
			$html = $this->insertImages($html);
		}

		return $html;
	}

	/**
	 * Return plain text output
	 * @return string
	 */
	public function text()
	{
		$this->options[] = 'plaintext';

		return $this->get();
	}
}