<?php
/**
 * Myrtille Rastrea Correios Front Controller
 * Check and display Tracking Number Status
 */
if (!defined('_PS_VERSION_'))
  exit;

class MyrtilleRastreaCorreiosCheckModuleFrontController extends ModuleFrontController
{
	private $message = '';
	private $tn = null;
	private $tn_status = null;
	private $cache_time = 1800; /* 30 min */
	private $cache_dir = _PS_CACHE_DIR_;

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if(!Tools::getIsset('tracking_number'))
			return;

		$this->cache_dir = $this->cache_dir.'rastreacorreios/';

		if(!file_exists($this->cache_dir) && !is_dir($this->cache_dir))
		{
			mkdir($this->cache_dir);
		}

		// Retrieve Tracking Number
		$this->tn = Tools::getValue('tracking_number');
		$filename = $this->cache_dir.$this->tn.'.json';
		$this->tn_status = null;

		// Check if the Tracking number exist in database (i.e. this service is just for customers)
		if(!Db::getInstance()->getRow("SELECT tracking_number FROM "._DB_PREFIX_."order_carrier WHERE tracking_number = '".pSQL($this->tn)."'"))
			return;

		// Check if cache doesn't exists OR cache is too old
		if(file_exists($filename) != true || (filemtime($filename) + $this->cache_time) < time()) {
			// Call Correios to get the parcel status
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array('objetos' => $this->tn, 'btnPesq' => 'Buscar'));
			curl_setopt($ch, CURLOPT_URL, $this->module->correios_endpoint);
			curl_setopt($ch, CURLOPT_REFERER, $this->module->correios_endpoint);
			$res = curl_exec($ch);
			curl_close($ch);

			$libxml_errors = libxml_use_internal_errors(true);
			$dom = new DOMDocument();
			$dom->loadHTML($res);
			libxml_clear_errors();
			libxml_use_internal_errors($libxml_errors);

			$finder = new DomXPath($dom);
			$classname = 'listEvent sro';
			$nodes = $finder->query("//*[@class='$classname']/tr");

			$status = array();
			$i = $nodes->length;

			foreach ($nodes as $node)
			{
				$status[$i] = $node->textContent;
				$i--;
			}

			// Find status in response
			$this->tn_status = $status;

			// Put content as json array
			file_put_contents($filename, json_encode($this->tn_status));
		} else {
			$this->tn_status = json_decode(file_get_contents($filename));
		}

		return;
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign('tracking_number', $this->tn);
		$this->context->smarty->assign('tracking_number_status', $this->tn_status);
		$this->setTemplate('check.tpl');
	}
}
