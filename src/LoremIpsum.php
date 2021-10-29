<?php 

namespace Wardenyarn\Loripsum;

/**
 * A helper class for https://loripsum.net/ API
 */
class LoremIpsum 
{
	const API = 'https://loripsum.net/api';
	const AVAILABLE_OPTIONS = ['decorate', 'link', 'ul', 'ol', 'dl', 'bq', 'code', 'headers'];
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
		return $this->http_client->request('GET', $this->getUrl())->getBody();
	}

	/**
	 * Return HTML output
	 * @return string
	 */
	public function html()
	{
		return $this->get();
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