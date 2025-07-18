<?php

namespace App\Services;

use App\Exports\Template\Variable;

use QuickChart;

/**
 * in develop set local ssl
 * curl.cainfo = "php/extras/ssl/cacert.pem"
 * openssl.cafile="php/extras/ssl/cacert.pem" 
 */

class QuickChartService
{
	protected $chart;
	protected $title = '';
	protected $subtitle = '';
	protected $type = '';
	protected $dimensions;
	protected $data = null;
	protected $chartConfig = null;
	protected $overwriteConfig = null;
	protected $allowTypes = ['pie', 'bar-vertical', 'bar-horizontal', 'line-vertical', 'line-horizontal'];

	/**
	 * @param string $title
	 * @param string $type
	 * @param mixed $data
	 */
	public function __construct()
	{
		$this->chart = new QuickChart();
		$this->chart->setVersion(4);
		$this->chart->setBackgroundColor('#fff');
		$this->chart->setFormat('png');
	}

	/**
	 * Generates the short URL for a graph based on the parameters provided.
	 * 
	 * @return array
	 */
	public function getShortUrl($config)
	{
		try {
			$chartConfig = $this->getConfigChart($config);

			if (!$chartConfig['success']) return $chartConfig;
			
			$this->chart->setConfig(json_encode($chartConfig['config']));
			$url = $this->chart->getShortUrl();

			$info['success'] = true;
			$info['message'] = 'Grafico generado';
			$info['chart']['title'] = $this->title;
			$info['chart']['url'] = $url;
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Error al generar los graficos';
			return $info;
		}
	}

	private function setChartConfig($config)
	{
		$this->title = $config['headers']['title'];
		$this->subtitle = $config['headers']['subtitle'] ?? null;
		$this->type = $config['type'];
		$this->data = $config['data'];
		$this->dimensions = $config['dimensions'] ?? ['width' => 500, 'height' => 300];
		$this->overwriteConfig = $config['overwrite_config'] ?? [];
		$this->chart->setWidth($this->dimensions['width']);
		$this->chart->setHeight($this->dimensions['height']);
	}
	/**
	 * @param array $configArray
	 * @return array
	 */
	private function overwriteChartConfig($configArray, $orientation, $labelLegendAxis)
	{
		if ( is_null($this->overwriteConfig) ) return $configArray;

		foreach ($this->overwriteConfig as $value) {
			$configPosition = $value[0];
			$configValue = $value[1];

			$keys = explode('.', $configPosition);
			$configTemp = &$configArray;

			foreach ($keys as $key) {
				if (!isset($configTemp[$key])) {
					$configTemp[$key] = [];
				}
				$configTemp = &$configTemp[$key];
			}

			$configTemp = $configValue;
		}

		$hasSuffix = $configArray['options']['plugins']['tickFormat']['suffix'] == ' %';
		if ($hasSuffix && $orientation != 'none') {
			$configArray['options']['scales'][$labelLegendAxis]['suggestedMax'] = 100;
		}

		return $configArray;
	}

	/**
	 * Get config by type
	 * 
	 * @return array
	 */
	private function getConfigChart($config)
	{
		try {
			$this->setChartConfig($config);

			$types = collect($this->allowTypes);
			
			if ( !$types->contains($this->type) ) {
				$info['success'] = false;
				$info['message'] = 'Error el tipo de gráfico no existe';
				return $info;
			}

			$configChart = $this->buildChartConfig();

			$info['success'] = $configChart['success'];
			$info['message'] = $configChart['message'];
			$info['config'] = $configChart['config'] ?? null;
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Error al buscar una configuración de gráficos';
			return $info;
		}
	}

	/**
	 * Construction of API bar chart configuration
	 *
	 * @return array bar chart configuration
	 */
	private function buildChartConfig()
	{
		try {
			$defineType = explode('-', $this->type); 
			$type = $defineType[0];
			$orientation =  $defineType[1] ?? 'none';

			$isVertical = $orientation == 'vertical';
			$mainAxis = $isVertical ? 'x' : 'y';
			$labelLegendAxis = $isVertical ? 'y' : 'x';
			
			$config = [
				"type" => $type,
				"data" => [
					"labels" => $this->data['labels'],
					'datasets' => $this->data['series'],
				],
				"options" => [
					
					"indexAxis" => $mainAxis,
					"layout" => [
						"padding" => 0
					],
					"plugins" => [
						'backgroundColor' => '#fff',
						"title" => [
							"display" => true,
							"text" => $this->title,
							"color" => '#'.Variable::COLOR_TITLE_TEXT,
							"position" => "top",
							"align" => "center",
							"font" => [
								"weight" => "bold"
							]
						],
						"subtitle" => [
							"display" => !is_null($this->subtitle),
							"text" => $this->subtitle,
							"color" => '#'.Variable::COLOR_TITLE_TEXT,
							"position" => "top",
							"align" => "center",
							"font" => [
								"weight" => "bold"
							]
						],
						"legend" => [
							"position" => "top",
							"align" => "center",
							"labels" => [
								"fontSize" => 10
							]
						],
						"datalabels" => [
							"color" => "white",
							"display" => true,
							"anchor" => "center",
							"align" => "center",
							"formatter" => "function(value){returnvalue+'%';}",
							"font" => ["weight" => "bold"]
						],
						"tickFormat" => [
            	"suffix" => ""
						],
					]
				]
			];
			
			if ($orientation != 'none') {
				$config['options']["scales"] = [
					$labelLegendAxis => [
						"title" => [
							"display" => true,
							"text" => "Requerimientos",
							"color" => '#'.Variable::COLOR_TITLE_TEXT,
						],
						"min" => 0,
						"suggestedMax" => ""
					]
				];
			}
			
			$config = $this->overwriteChartConfig($config, $orientation, $labelLegendAxis);

			$info['success'] = true;
			$info['message'] = 'Configuración construida';
			$info['config'] = $config;
			return $info;
		} catch (\Throwable $th) {
			$info['success'] = false;
			$info['message'] = 'Error al construir configuración';
			return $info;
		}
	}
}
