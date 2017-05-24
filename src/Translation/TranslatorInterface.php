<?php
/**
 * Translator Interface
 * This script contains an interface for translators
 *
 * @license MIT
 * @copyright 2015 Tommy Teasdale
 */

namespace Apine\Translation;

/**
 * Interface TranslatorInterface
 *
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package Apine\Translation
 */
interface TranslatorInterface
{
    /**
     * @param string|TranslationLanguage $a_language
     */
    public function set_language($a_language);
    
    /**
     * @param string $a_prefix
     * @param string $a_key
     * @param string $a_pattern
     *
     * @return string
     */
    public function translate($a_prefix, $a_key, $a_pattern);
    
    /**
     * @return TranslationLanguage
     */
    public function language();
    
    /**
     * @return Translation
     */
    public function translation();
}