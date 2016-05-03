<?php
namespace Apine\MVC;

use Apine\Application as Application;
use Apine\Exception\GenericException;

/**
 * HTML View
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 */
final class InstallView extends View {
	
	/**
	 * Path to layout file
	 *
	 * @var string
	 */
	private $_layout;
	
	/**
	 * Path to view file
	 *
	 * @var string
	 */
	private $_view;
	
	/**
	 * Page Title
	 *
	 * @var string
	 */
	private $_title;
	
	/**
	 * List of custom meta tags
	 * @var array $_metatags
	 */
	private $_metatags;
	
	/**
	 * List of stylesheets to include
	 *
	 * @var array
	 */
	private $_styles;
	
	/**
	 * List of scripts to include
	 *
	 * @var array
	 */
	private $_scripts;
	
	/**
	 * View's HTML Document
	 *
	 * @var string $content
	 */
	private $content;
	
	private $data;
	
	private $rules;
	
	private $stack;
	
	/**
	 * Construct the HTML view
	 *
	 * @param string $a_title
	 * @param string $a_view
	 * @param string $a_layout
	 */
	public function __construct($a_title = "", $a_view, $a_layout) {
	
		parent::__construct();
		$this->_scripts = array();
	
		$this->_title=$a_title;
	
		//$this->set_view($this->parent . '/Views/install_view');
	
		/*$config = Application\Application::get_instance()->get_config();
	
		if (!is_null($config)) {
			if ($a_layout == "layout" && !is_null($config->get('runtime', 'default_layout'))) {
				$a_layout = $config->get('runtime', 'default_layout');
			}
		}
	
		$this->set_layout($a_layout);*/
		
		$this->_view = $a_view;
		$this->_layout = $a_layout;
		
		$this->rules[] = new Rule(
				'php_exclude',
				'~(<\?)~',
				'<?php echo \'<?\'; ?>'
				);
		
		// If conditionning
		$this->rules[] = new Rule(
				'if_boolean',
				'~\{if:(\w+)\}~',
				'<?php if (isset($this->data[\'$1\']) && $this->data[\'$1\']): ?>'
				);
		$this->rules[] = new Rule(
				'if_condition',
				'~\{if:(\w+)([!<>=]+)(\w+)\}~',
				'<?php if (isset($this->data[\'$1\'])) {$base = $this->data[\'$1\'];}else{$base = \'$1\';};
        		if (isset($this->data[\'$3\'])) {$value = $this->data[\'$3\'];}else{$value = \'$3\';}?>
        		<?php if ($base $2 $value) : ?>'
				);
		$this->rules[] = new Rule(
				'ifnot',
				'~\{ifnot:(\w+)\}~',
				'<?php if (!$this->data[\'$1\']): ?>'
				);
		$this->rules[] = new Rule(
				'else',
				'~\{else\}~',
				'<?php else: ?>'
				);
		$this->rules[] = new Rule(
				'elseif_boolean',
				'~\{else:(\w+)\}~',
				'<?php elseif (isset($this->data[\'$1\']) && $this->data[\'$1\']): ?>'
				);
		$this->rules[] = new Rule(
				'elseif_condition',
				'~\{else:(\w+)([!<>=]+)(\w+)\}~',
				'<?php elseif (((isset($this->data[\'$1\']) && isset($this->data[\'$3\'])) && $this->data[\'$1\'] $2 $this->data[\'$3\']) ||
        		((isset($this->data[\'$1\']) && !isset($this->data[\'$3\'])) && $this->data[\'$1\'] $2 \'$3\') ||
        		((!isset($this->data[\'$1\']) && isset($this->data[\'$3\'])) && \'$1\' $2 $this->data[\'$3\']) ||
        		((!isset($this->data[\'$1\']) && !isset($this->data[\'$3\'])) && \'$1\' $2 \'$3\')): ?>'
				);
		$this->rules[] = new Rule(
				'endif',
				'~\{endif\}~',
				'<?php endif; ?>'
				);
		
		// Loops
		$this->rules[] = new Rule(
				'loop',
				'~\{loop:(\w+)\}~',
				'<?php foreach ($this->data[\'$1\'] as $element): $this->wrap($element); ?>'
				);
		$this->rules[] = new Rule(
				'endloop',
				'~\{endloop\}~',
				'<?php $this->unwrap(); endforeach; ?>'
				);
		
		// Importing
		$this->rules[] = new Rule(
				'import_view',
				'~\{import:(([^\/\s]+\/)?(.*))\}~',
				'<?php echo $this->importFile(\'$1\'); ?>'
				);
		$this->rules[] = new Rule(
				'yield',
				'~\{yield\}~',
				'<?php $end = end($this->data); echo $end; ?>'
		);
		
		// Clean variables
		$this->rules[] = new Rule(
				'escape_var',
				'~\{escape:(\w+)\}~',
				'<?php $this->showVariable(\'$1\', true); ?>'
				);
		
		// Variables
		$this->rules[] = new Rule(
				'variable',
				'~\{(\w+)\}~',
				'<?php $this->showVariable(\'$1\'); ?>'
				);
		
		// Arrays
		$this->rules[] = new Rule(
				'variable_array',
				'~\{(\w+)\[(\w+)\]\}~',
				'<?php echo (isset($this->data[\'$1\'][\'$2\'])) ? $this->data[\'$1\'][\'$2\'] : "{$1[$2]}"; ?>'
				);
		
		$this->rules[] = new Rule(
				'variable_array_escape',
				'~\{escape:(\w+)\[(\w+)\]\}~',
				'<?php echo htmlentities($this->showVariable(\'$1\')[\'$2\']); ?>'
				);
	
	}
	
	/**
	 * Send the view to output
	 */
	public function draw() {
	
		$this->apply_headers();
	
		if (is_null($this->content)) {
			$this->content();
		}
	
		print $this->content;
	
	}
	
	/**
	 * Return the content of the view
	 *
	 * @return string
	 */
	public function content() {
	
		$this->data = array(
				'apine_application_https' => Application\Application::get_instance()->get_use_https(),
				'apine_application_mode' => Application\Application::get_instance()->get_mode(),
				'apine_application_secure' => Application\Application::get_instance()->get_secure_session()
		);
		$this->data = array_merge($this->data, $this->_params->get_all());
		$this->data = array_merge($this->data, array("apine_view_title" => $this->_title));
		$content = $this->process($this->_view);
		$this->data[] = $content;
		$this->content = $this->process($this->_layout);
	
		return $this->content;
	
	}
	
	/**
	 * Shows the content of a variable stored in the data.
	 * @var $name string The variable name in the data array.
	 * @var $sanitize boolean If the variable should be escaped before being returned.
	 * @returns mixed
	 */
	private function showVariable($name, $sanitize = false) {
		if (isset($this->data[$name])) {
			if ($sanitize) {
				echo htmlentities($this->data[$name]);
			} else {
				echo $this->data[$name];
			}
		} else {
			echo '{' . $name . '}';
		}
	}
	
	/**
	 * Wraps the content of the loop into the data array so it can be used
	 * @var $element object|array The element that will be looped into.
	 */
	private function wrap($element) {
		$this->stack[] = $this->data;
		foreach ($element as $k => $v) {
			$this->data[$k] = $v;
		}
	}
	
	/**
	 * Removes the loop variables from inside the data so we cannot use it afterwards.
	 */
	private function unwrap() {
		$this->data = array_pop($this->stack);
	}
	
	private function process ($file) {
		
		$template = @file_get_contents($file, true);
		$this->stack = array();
		
		foreach ($this->rules as $rule) {
			$template = preg_replace($rule->rule(), $rule->replacement(), $template);
		}
		
		$template = '?>' . $template;
		
		ob_start();
		eval($template);
		return ob_get_clean();
		
	}
	
}

/**
 * A Rule can be applied to a template to process its content in different ways.
 */
class Rule {

	/**
	 * @var string The Rule's name, for referencing in case of errors.
	 */
	private $name;

	/**
	 * @var string The actual regex rule to be applied to the template.
	 */
	private $rule;

	/**
	 * @var string The replacement for the rule defined above.
	 */
	private $replacement;

	/**
	 * Sets the Rule's values.
	 * @param $name string The Rule's name.
	 * @param $rule string The Rule's regex.
	 * @param $replacement string The Rule's replacement.
	 */
	public function __construct($name, $rule, $replacement) {
		$this->name = $name;
		$this->rule = $rule;
		$this->replacement = $replacement;
	}

	/**
	 * Returns the Rule's name.
	 * @returns string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Returns the Rule's regex.
	 * @returns string
	 */
	public function rule() {
		return $this->rule;
	}

	/**
	 * Returns the Rule's replacement.
	 * @returns string
	 */
	public function replacement() {
		return $this->replacement;
	}

}