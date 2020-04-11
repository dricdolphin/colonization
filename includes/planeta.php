<?php
/**************************
PLANETA.PHP
----------------
Cria o objeto "planeta" e mostra os dados do Império
***************************/

//Classe "planeta"
//Contém os dados do planeta
class planeta 
{
	public $id;
	public $id_estrela;
	public $nome;
	public $posicao;
	public $classe;
	public $subclasse;
	public $tamanho;
	public $estrela;
	public $inospito;
	
	function __construct($id) {
		global $wpdb;
		
		$this->id = $id;

		$resultados = $wpdb->get_results("SELECT id_estrela, nome, posicao, classe, subclasse, tamanho, inospito FROM colonization_planeta WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_estrela = $resultado->id_estrela;
		$this->nome = $resultado->nome;
		$this->posicao = $resultado->posicao;
		$this->classe = $resultado->classe;
		$this->subclasse = $resultado->subclasse;
		$this->tamanho = $resultado->tamanho;
		$this->inospito = $resultado->inospito;
		
		
		$this->estrela = new estrela($this->id_estrela);
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	$id_estrela -- caso tenha vindo da página de edição de Estrelas
	***********************/
	function lista_dados($id_estrela = 0) {
		global $wpdb;
		if ($this->inospito == 1) {
			$inospito_checked = "checked";
		} else {
			$inospito_checked = "";
		}
		
		$link_gerenciamento = "\"page=colonization_admin_planetas\"";
		if ($id_estrela != 0) {
			$link_gerenciamento = "\"page=colonization_admin_planetas&id_estrela={$id_estrela}\"";
		}
		
		//Exibe os dados do objeto	
		$html = "		<td>
				<input type='hidden' data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'></input>
				<input type='hidden' data-atributo='id_estrela' data-valor-original='{$this->id_estrela}' value='{$this->id_estrela}'></input>
				<input type='hidden' data-atributo='where_clause' value='id'></input>
				<input type='hidden' data-atributo='where_value' value='{$this->id}'></input>
				<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>
				<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>
				<div data-atributo='id' data-valor-original='{$this->id}' value='{$this->id}'>{$this->id}</div>
				<div><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
			</td>
			<td><div data-atributo='nome' data-editavel='true' data-valor-original='{$this->nome}'>{$this->nome}</div></td>
			<td><div data-atributo='nome_estrela'>{$this->estrela->nome} - {$this->estrela->X};{$this->estrela->Y};{$this->estrela->Z}</div></td>
			<td><div data-atributo='posicao' data-style='width: 30px;' data-editavel='true' data-valor-original='{$this->posicao}'>{$this->posicao}</div></td>
			<td><div data-atributo='classe' data-editavel='true' data-valor-original='{$this->classe}'>{$this->classe}</div></td>
			<td><div data-atributo='subclasse' data-editavel='true' data-valor-original='{$this->subclasse}'>{$this->subclasse}</div></td>
			<td><div data-atributo='tamanho' data-style='width: 30px;' data-editavel='true' data-valor-original='{$this->tamanho}'>{$this->tamanho}</div></td>
			<td><div data-atributo='inospito' data-type='checkbox' data-editavel='true' data-valor-original='{$this->inospito}'><input type='checkbox' data-atributo='inospito' data-ajax='true' {$inospito_checked} disabled></input></div></td>
			<td><div data-atributo='gerenciar'><a href='#' onclick='return gerenciar_objeto(event, this,{$link_gerenciamento});'>Gerenciar Objeto</a></div></td>";
		
		return $html;
	}
}

?>