<?php
namespace Apine\Translation;

interface TranslatorInterface {
	
	public function set_language ($a_language);
	
	public function translate ($a_prefix, $a_key, $a_pattern);
	
	public function language ();
	
	public function translation ();
	
}