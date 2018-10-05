<?php
namespace App\Page;

use CPS\Article;
use Parsedown;

class _Common extends \Gt\Page\Logic {

	public function go() {
		$dir = __DIR__.'/../../data/article';
		$this->article = new Article($dir);
    	$this->parsedown = new Parsedown();
		$this->file = explode('?',$_SERVER['REQUEST_URI'])[0];
		$this->search = $_GET['search'] ?? null;

		$this->file = $this->file != '/' ? $this->file : false; 

		$this->document->querySelector('[name="search"]')->value = $this->search;
		$this->renderArticle();
		$this->renderList();
		
	}

	public function renderList() {
		if (!empty($this->file)) return;
		$list = $this->article->list($this->search);
		if (!$list) {
			$t = $this->template->get('notice');
			$t->textContent = "No Articles could be found.";
			$t->insertTemplate();
			return;
		}
		foreach ($list as $i => $row) {
			$t = $this->template->get('row');
			$t->querySelector('a')->value = ucwords(str_replace('-',' ',$row['name']));
			$t->querySelector('a')->setAttribute('href','/'.$row['name']); 
			$t->insertTemplate();
		}
	}

	public function renderArticle () {
		
		if (!$this->file || empty($this->file) ) return;

		$file = $this->article->load($this->file);
		$t = $this->template->get('article');
		if (!$file) {
			$t->querySelector('.php-name')->textContent = "Not Found";
			$t->querySelector('.php-content')->textContent = "The Article you were looking for can not be found.";
			$t->insertTemplate();
			return;
		}

		$t->querySelector('.php-name')->value = ucwords(str_replace('-',' ',$file['name']));
		$t->querySelector('.php-content')->html = $this->parsedown->text($file['content']);
		$t->insertTemplate();

	}
}

