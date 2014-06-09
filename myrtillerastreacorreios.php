<?php
if (!defined('_PS_VERSION_'))
	exit;

class MyrtilleRastreaCorreios extends Module
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->name = 'myrtillerastreacorreios';
		$this->tab = '';
		$this->version = 1.0;
		$this->author = 'Myrtille';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Rastrea Correios');
		$this->description = $this->l('Oferece o rastreamento direto dos Correios (SEDEX, PAC)');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

		// Set URL endpoint
		$this->correios_endpoint = 'http://www2.correios.com.br/sistemas/rastreamento/resultado.cfm';
	}

	public function install()
	{

		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		return parent::install() || !$this->registerHook('ModuleRoutes');
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	/**
	 * Add Routes to module front controller
	 */
	public function hookModuleRoutes()
	{
		return array(
				'module-myrtillerastreacorreios-check' => array(
					'controller' => 'check',
					'rule' =>  'module{/:module}{/:controller}{/tracking_number}',
					'keywords' => array(
						'module'  => array('regexp' => '[\w]+', 'param' => 'module'),
						'controller' => array('regexp' => '[\w]+',  'param' => 'controller'),
						'tracking_number'   => array('regexp' => '[\w]+',   'param' => 'tracking_number'),
					),
					'params' => array(
						'fc' => 'module',
						'module' => 'myrtillerastreacorreios',
						'controller' => 'check'
					)
				)
			);
	}


	/**
	 * Config content
	 */
	public function getContent()
	{
		return Context::getContext()->link->getModuleLink($this->name, 'check');
	}
}