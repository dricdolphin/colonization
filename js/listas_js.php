<?
/**************************
LISTAS_JS.PHP
----------------
Cria o "script" com todas as listas
***************************/
//include_once(realpath(dirname(__FILE__).'/../includes/lista.php'));

class listas_js {
	public $html_header;
	
	function __construct() {
		$lista = new lista_usuarios();
		$this->html_header .= $lista->html_lista;

		$lista = new lista_imperios();
		$this->html_header .= $lista->html_lista;
		
		$lista = new lista_estrelas();
		$this->html_header .= $lista->html_lista;
			
		$lista = new lista_recursos();
		$this->html_header .= $lista->html_lista;
		
		$lista = new lista_techs();
		$this->html_header .= $lista->html_lista;

		$lista = new lista_planetas();
		$this->html_header .= $lista->html_lista;
			
		$lista = new lista_instalacoes();
		$this->html_header .= $lista->html_lista;
	}
}

$listas_js = new listas_js();

if (file_exists(dirname(__FILE__)."/listas_js.js")) {
	if (file_get_contents(dirname(__FILE__)."/listas_js.js") == $listas_js->html_header) {
		return;
	}
}
$arquivo = file_put_contents(dirname(__FILE__)."/listas_js.js",$listas_js->html_header,FILE_TEXT);	
?>
