<?php
class ControllerModuleBlogLatest extends Controller {
	public function index($setting) {
		$this->load->language('module/blog_latest');

		$data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('blog/blog');

		$this->load->model('tool/image');

		$data['blogs'] = array();

		$filter_data = array(
			'sort'  => 'p.date_added',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_blog_blog->getBlogs($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				$data['blogs'][] = array(
					'blog_id'  => $result['blog_id'],
					'thumb'       => $image,
					'name'        => $result['title'],
					'brief' 	  => utf8_substr(strip_tags(html_entity_decode($result['brief'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('cms_blog_brief_length')) . '..',
					'href'        => $this->url->link('blog/blog', 'blog_id=' . $result['blog_id'])
				);
			}

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/blog_latest.tpl')) {
				return $this->load->view($this->config->get('config_template') . '/template/module/blog_latest.tpl', $data);
			} else {
				return $this->load->view('default/template/module/blog_latest.tpl', $data);
			}
		}
	}
}
