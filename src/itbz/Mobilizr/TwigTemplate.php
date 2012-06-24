<?php
/**
 * This file is part of Mreg
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @copyright Copyright (c) 2011, Hannes Forsgård
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Mreg
 */
namespace Mreg;


/**
 * TwigTemplates are used when generation mails and pdfs for reports and
 * communications. Implements \Mobilizr\Template for this reason. Extends
 * MregEditable to be online editable. Templates are executed in a sandbox
 * for enhanced security. Se the twig website for syntax descriptions.
 * @uses Twig
 * @package Mreg
 */
class TwigTemplate extends MregEditable implements \Mobilizr\Template {


    /**
     * Construct
     * @param string $table Name of db table
     */
    public function __construct($table = 'Template'){
        parent::__construct($table, 'name');
        $this->setAccess('root', 'root', 0777);
    }

    /**
     * Get title of collection
     * @return string
     */
    protected function getCollectionTitle(){
        return 'Mallar';
    }


    /**
     * Url to collection
     * @return string
     */
    protected function getBaseUrl(){
        return $this->getServiceUrl()."templates";
    }


    /**
     * Set name on insert
     * @uses \Active_Field_String
     * @return int Number of affected rows
     */
    public function insert(){
        assert('isset($this->headline)');
        if ( !isset($this->name) ) {
            $headline = new \Active_Field_String($this->headline);
            $headline->prettyprint();
            $this->name = $headline->truncate(20, '');
            $this->name = trim($this->name, '-');
        }
        return parent::insert();
    }


    /**
     * Internal Twig object
     * @var Twig_Environment $twig
     */
    private static $twig;


    /**
     * Get twig sandboxed Twig environment
     * @todo Ska använda DI istället!!
     * @return Twig_Environment
     */
    private static function getTwig(){
        if ( !isset(self::$twig) ) {
            if ( !defined('TWIG_CACHE_DIR') ) {
                throw new \RuntimeException('Define TWIG_CACHE_DIR to enable template caching.');
            }

            require_once 'Twig/Autoloader.php';
            \Twig_Autoloader::register();

            //Load templates from string
            $loader = new \Twig_Loader_String();

            self::$twig = new \Twig_Environment($loader, array(
                'cache' => TWIG_CACHE_DIR,
            ));

            //Turn on auto escaping
            $escaper = new \Twig_Extension_Escaper(true);
            self::$twig->addExtension($escaper);

            //Create sandbox
            $tags = array('if', 'for', 'filter', 'set', 'macro');
            $filters = array('date', 'length', 'keys', 'upper', 'lower', 'capitalize', 'escape', 'raw');


            $methods = array(
                '\Mobilizr\Target' => array('getAddress', 'getMobile', 'getMail', 'getName'),
            );
            $properties = array(
                'Member' => array('tCreated', 'names', 'surname', 'paymentType', 'LS', 'dob', 'sex'),
                'Faction' => array('tCreated', 'name', 'type', 'description', 'plusgiro', 'bankgiro', 'url', 'members'),
                'Workplace' => array('tCreated', 'name', 'members'),
            );


            $functions = array('range');
            $policy = new \Twig_Sandbox_SecurityPolicy($tags, $filters, $methods, $properties, $functions);
            $sandbox = new \Twig_Extension_Sandbox($policy, true);

            self::$twig->addExtension($sandbox);
        }
        return self::$twig;
    }


    /**
     * Compile loaded template using $values
     * @param array $values
     * @return string
     */
    public function doCompile(array $values){
        assert('is_string($this->tmpl) /* template loaded? */');
        $twig = self::getTwig();
        return $twig->render($this->tmpl, $values);
    }


    /**
     * Return template title
     * @return string
     */
    public function getTmplTitle(){
        assert('isset($this->headline) /* Template loaded? */');
        return $this->headline;
    }


    /**
     * Validate syntax of loaded template
     * @return bool TRUE if syntax is valid, FALSE otherwise
     */
    public function isValid(){
        assert('is_string($this->tmpl) /* template loaded? */');
        $twig = self::getTwig();
        try {
            $twig->parse($twig->tokenize($this->tmpl));
            return true;
        } catch ( Twig_Error_Syntax $e ) {
            return false;
        }
    }

}
