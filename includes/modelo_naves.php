<?php
/**************************
MODELO_NAVES.PHP
----------------
Cria o objeto "modelo_naves"
***************************/

//Classe "modelo_naves"
//Contém os dados dos modelos de naves
class modelo_naves
{
	public $id;
	public $id_imperio;
	public $nome_modelo;
	public $string_nave;
	public $texto_nave;
	public $texto_custo;
	public $turno;

	private $JSON_custo;
	private $JSON_atributos;
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($id=0) {
		global $wpdb;

		if ($id == 0) {
			$this->id = 0;
			$this->id_imperio = 0;
			return;
		}			
			
		$resultados = $wpdb->get_results("SELECT id, id_imperio, nome_modelo, string_nave, texto_nave, texto_custo, turno FROM colonization_modelo_naves WHERE id={$id}");
		$resultado = $resultados[0];
		
		$this->id = $id;
		$this->id_imperio = $resultado->id_imperio;
		$this->nome_modelo = $resultado->nome_modelo;
		$this->string_nave = $resultado->string_nave;
		$this->texto_nave = $resultado->texto_nave;
		$this->texto_custo = $resultado->texto_custo;
		$this->turno = $resultado->turno;
	}

	/***********************
	function __set()
	----------------------
	Função mágica __set
	***********************/
	function __set($propriedade, $valor) {
		
		$this->$propriedade = $valor;
	}
	
	/***********************
	function get()
	----------------------
	Função mágia __get
	***********************/
	function __get($propriedade) {

		return $this->$propriedade();
	}


	/***********************
	function lista_dados()
	----------------------
	Lista os dados do objeto
	***********************/	
	function lista_dados() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}		

		$string_nave = json_decode(stripslashes($this->string_nave),true, JSON_UNESCAPED_UNICODE);
		$string_nave['nome_modelo'] = $this->nome_modelo;
		$string_nave['id'] = $this->id;
		$this->string_nave = json_encode($string_nave, JSON_UNESCAPED_UNICODE);
		
		$link_deleta_modelo = "";
		$html_nome_imperio = "<b>Modelo em uso</b><br>";
		$data_modelo_em_uso = "true";
		if (!$this->modelo_em_uso() || $roles == "administrator") {
			$link_deleta_modelo = "<br><a href='#' onclick='return deleta_modelo_nave(event, this, {$this->id});'>Deletar Modelo</a></td>";
			$data_modelo_em_uso = "false";
			if (!$this->modelo_em_uso()) {
				$html_nome_imperio = "";
			}
		}

		if ($this->id_imperio != 0 && $roles == "administrator") {
			$imperio = new imperio($this->id_imperio);
			$html_nome_imperio .= "Império '{$imperio->nome}'<br>";
		}		
		
		$html = "<tr><td><input type='hidden' data-atributo='string_nave' value='{$this->string_nave}'></input>{$html_nome_imperio}{$this->nome_modelo}</td>
		<td>{$this->texto_nave}</td>
		<td>{$this->texto_custo}</td>
		<td>{$this->turno}</td>
		<td>
		<a href='#' onclick='return carrega_modelo_nave(event, this, {$this->id_imperio});' data-modelo_em_uso='{$data_modelo_em_uso}'>Carregar Modelo</a>
		{$link_deleta_modelo}
		</tr>";
	
		return $html;
	}
	
	/***********************
	function JSON_custo()
	----------------------
	Inicializa a varíável JSON_custo
	***********************/
	function JSON_custo() {
		global $wpdb;
		
		$custo_modelo = explode(" | ", trim($this->texto_custo));
		$custo_modelo_temp = [];
		foreach ($custo_modelo as $chave => $valor) {
			$valor_explode = explode(":", $valor);
			$chave_nome_recurso = trim($valor_explode[0]);
			if (intval(trim($valor_explode[1])) != 0) {
				$custo_modelo_temp[$chave_nome_recurso] = intval(trim($valor_explode[1]));
			}
		}

		$this->JSON_custo = $custo_modelo_temp;
		return $this->JSON_custo;
	}
	
	/***********************
	function JSON_atributos()
	----------------------
	Inicializa a varíável JSON_atributos
	***********************/	
	function JSON_atributos() {
		global $wpdb;

		$atributos_modelo = explode(";", $this->texto_nave);
		$atributos_modelo_temp = [];
		foreach ($atributos_modelo as $chave => $valor) {
			$valor_explode = explode(":", $valor);
			$chave_nome_atributo = remove_accents(strtolower(str_replace(" ","_", trim($valor_explode[0]))));

			if (intval(trim($valor_explode[1])) != 0) {
				$atributos_modelo_temp[$chave_nome_atributo] = intval(trim($valor_explode[1]));
			}
		}
		
		return $atributos_modelo_temp;		
	}

	/***********************
	function modelo_em_uso()
	----------------------
	Verifica se o Modelo está sendo usado em alguma nave do Império
	***********************/	
	function modelo_em_uso() {
		global $wpdb;
		
		$query = "SELECT cif.string_nave 
		FROM colonization_imperio_frota AS cif 
		WHERE cif.id_imperio = {$this->id_imperio} 
		AND cif.turno_destruido = 0";
		
		$modelos_naves_em_uso = $wpdb->get_results($query);
		
		$array_json_naves = [];
		foreach ($modelos_naves_em_uso as $modelo_nave_em_uso) {
			$json_modelo_em_uso = json_decode(stripslashes($modelo_nave_em_uso->string_nave),true, JSON_UNESCAPED_UNICODE);
			unset($json_modelo_em_uso['id']);
			unset($json_modelo_em_uso['nome_modelo']);
			
			$array_json_naves[] = $json_modelo_em_uso;
		}
		
		$json_modelo_atual = json_decode(stripslashes($this->string_nave),true, JSON_UNESCAPED_UNICODE);
		unset($json_modelo_atual['id']);
		unset($json_modelo_atual['nome_modelo']);
		
		if (in_array($json_modelo_atual, $array_json_naves)) {
			return true;
		}

		return false;
	}
	
	/***********************
	function modelo_ja_existe()
	----------------------
	Verifica se o Modelo já existe
	***********************/
	function modelo_ja_existe() {
		global $wpdb;
		
		$query = "SELECT cmn.string_nave 
		FROM colonization_modelo_naves AS cmn
		WHERE cmn.id_imperio = {$this->id_imperio} 
		AND cmn.id !={$this->id}";
		
		$modelos_naves_em_uso = $wpdb->get_results($query);
		
		$array_json_naves = [];
		foreach ($modelos_naves_em_uso as $modelo_nave_em_uso) {
			$json_modelo_em_uso = json_decode(stripslashes($modelo_nave_em_uso->string_nave),true, JSON_UNESCAPED_UNICODE);
			unset($json_modelo_em_uso['id']);
			unset($json_modelo_em_uso['nome_modelo']);
			
			$array_json_naves[] = $json_modelo_em_uso;
		}
		
		$json_modelo_atual = json_decode(stripslashes($this->string_nave),true, JSON_UNESCAPED_UNICODE);
		unset($json_modelo_atual['id']);
		unset($json_modelo_atual['nome_modelo']);
		
		if (in_array($json_modelo_atual, $array_json_naves)) {
			return true;
		}

		return false;
	}	

	/***********************
	function modelo_mesmo_nome()
	----------------------
	Verifica se já existe um modelo com o mesmo nome
	***********************/
	function modelo_mesmo_nome() {
		global $wpdb;
		
		$query = "SELECT COUNT(cmn.nome_modelo)
		FROM colonization_modelo_naves AS cmn
		WHERE cmn.id_imperio = {$this->id_imperio} 
		AND cmn.id !={$this->id}
		AND cmn.nome_modelo='{$this->nome_modelo}'";
		
		$modelos_naves_em_uso = $wpdb->get_var($query);
		
		if ($modelos_naves_em_uso > 0) {
			return true;
		}

		return false;
	}
}
?>