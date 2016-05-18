<?php
class ControllerModuleProductCategory extends Controller {
	protected function index($setting) {
//		echo '<pre>';
//		print_r($setting);die;
		$this->language->load('module/product_category');
 
      	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		$this->data['products'] = array();
		
		$this->data['title'] = $setting[$this->config->get('config_language_id')]['title'];
		
		$info_category = $this->model_catalog_category->getCategory($setting['cat_p']);

		if(isset($info_category['name'])){
			$data = array(
				'filter_category_id' =>$info_category['category_id'],
				'sort'  => $setting['sort'],
				'order' => $setting['type'],
				'start' => 0,
				'limit' => $setting['limit']
			);
		$results = $this->model_catalog_product->getProducts($data);
		
			foreach ($results as $result) {
				$width = ''; $height = '';
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
				}else {
					$image = false;
				}			
							
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$result['special']) { 
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);

			}

		}
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/product_category.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/product_category.tpl';
		} else {
			$this->template = 'default/template/module/product_category.tpl';
		}

		$this->render();
	}
}
?>