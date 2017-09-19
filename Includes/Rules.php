<?php
/**
 * Custom rules for TinyTemplate
 *
 * @license MIT
 * @copyright 2015-2017 Tommy Teasdale
 */
use TinyTemplate\Engine;
use TinyTemplate\Rule;

Engine::instance()->add_rule(new Rule(
	'apine_data_loop',
	'loopdata',
	'<?php foreach ($this->data as $element): $this->wrap($element); ?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_config',
	'apine_config:(\w+),(\w+)',
	'<?php echo \\Apine\\Application\\Application::get_instance()->get_config()->get(\'$1\',\'$2\');?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_translate',
	'apine_translate:(\w+),(\w+)',
	'<?php echo \\Apine\\Application\\Translator::get_instance()->translate(\'$1\',\'$2\');?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_format_date',
	'apine_format_date:(\w+),(\w+)',
	'<?php echo \\Apine\\Application\\Translator::get_instance()->translation()->get_locale()->format_date("$1", Apine\\Application\\Translator::get_instance()->translation()->get_locale()->$2());?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_format_date_array',
	'apine_format_date:(\w+)\[(\w+)\],(\w+)',
	'<?php echo \\Apine\\Application\\Translator::get_instance()->translation()->get_locale()->format_date($this->data[\'$1\'][\'$2\'], Apine\\Application\\Translator::get_instance()->translation()->get_locale()->$3());?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_language',
	'apine_language:(code|short|name)',
	'<?php switch("$1"){case "code": echo Apine\\Application\\Translator::get_instance()->translation()->get("language","code");break;case "short": echo Apine\Application\Translator::get_instance()->translation()->get("language","shortcode");break;case "name": echo Apine\Application\Translator::get_instance()->translation()->get("language","name");break;}?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_execution',
	'apine_execution_time',
	'<?php echo apine_execution_time();?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_version',
	'apine_version:(framework|application)',
	'<?php echo \\Apine\\Application\\Application::get_instance()->get_version()->$1();?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_url',
	'apine_url_(path|resource):(([^\/\s]+\/)?([^\{\}]*))',
	'<?php echo \\Apine\\MVC\URLHelper::get_instance()->$1("$2");?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_url_secure',
	'apine_url_(path|resource)_secure:(([^\/\s]+\/)?([^\{\}]*))',
	'<?php echo Apine\\MVC\\URLHelper::get_instance()->$1("$2", APINE_PROTOCOL_HTTPS);?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_view_apply_meta',
	'apine_apply_meta',
	'<?php echo Apine\\MVC\\HTMLView::apply_meta($data["apine_view_metatags"]);?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_view_apply_scripts',
	'apine_apply_scripts',
	'<?php echo Apine\\MVC\\HTMLView::apply_scripts($data["apine_view_scripts"]);?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_view_apply_stylesheets',
	'apine_apply_stylesheets',
	'<?php echo Apine\\MVC\\HTMLView::apply_stylesheets($data["apine_view_stylesheets"]);?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_view_apply_literals',
	'apine_apply_literals',
	'<?php echo (isset($data["apine_view_literals"]["default"])) ? Apine\\MVC\\HTMLView::apply_html_literals($data["apine_view_literals"]["default"]) : "";?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_view_apply_literals_zone',
	'apine_apply_literals:(\w+)',
	'<?php echo (isset($data["apine_view_literals"]["$1"])) ? Apine\\MVC\\HTMLView::apply_html_literals($data["apine_view_literals"]["$1"]) : "";?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_user_has_group',
	'if:apine_user\[groups\]==([0-9]+)',
	'<?php if (\Apine\Session\SessionManager::get_user()->has_group($1)) : ?>'
));

Engine::instance()->add_rule(new Rule(
	'apine_user_group',
	'apine_user\[groups\]\[([0-9]+)\]',
	'<?php echo (\Apine\Session\SessionManager::get_user()->has_group($1)) : \Apine\Session\SessionManager::get_user()->get_group()->get_item($1)->get_name() : ""; ?>'
));