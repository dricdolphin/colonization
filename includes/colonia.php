<?php
/**************************
COLONIA.PHP
----------------
Cria o objeto "colônia"
***************************/

//Classe "colonia"
//Contém os dados da colonia
class planeta 
{
	public $id;
	public $id_imperio;
	public $id_planeta;
	public $pop;
	public $poluicao;
	public $turno;
	public $planeta;
	public $estrela;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_imperio, id_planeta, pop, poluicao, turno FROM colonization_imperio_colonias WHERE id=".$this->id);
		$resultado = $resultados[0];				
		
		$this->id_imperio = $resultado->id_imperio;
		$this->id_planeta = $resultado->id_planeta;
		$this->pop = $resultado->pop;
		$this->poluicao = $resultado->poluicao;
		$this->turno = $resultado->turno;
		
		$this->planeta = new planeta($this->id_planeta);
		$this->estrela = new estrela($this->planeta->id_estrela);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {
		global $wpdb;

		//Exibe os dados do Império		
		$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_imperio' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
				<input type='hidden' data-atributo='id_planeta' data-valor-original='{$this->id_planeta}' value='{$this->id_planeta}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_colonia'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>
				<div data-atributo='id' data-ajax='true'>{$this->id}</div>
				<div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome_planeta' data-editavel='true' data-type='select' data-funcao='lista_planetas_html' data-id-selecionado='{$this->id_planeta}' data-valor-original='{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}'>{$this->planeta->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z} / {$this->planeta->posicao}</div></td>
			<td><div data-atributo='pop' data-editavel='true' data-style='width: 30px;'>{$this->pop}</div></td>
			<td><div data-atributo='poluicao' data-editavel='true' data-style='width: 30px;'>{$this->poluicao}</div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='gerenciar_objeto(this);'>Gerenciar Objeto</a></div></td>";
		
		return $html;
	}
}

?>