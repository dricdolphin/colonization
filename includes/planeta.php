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
	public $pop_inospito;
	public $icone_habitavel;
	public $instalacoes;
	public $turno;
	
	//Defesas Planetárias oferecidas por Instalações
	public $instalacoes_ataque = [];
	//public $pdf_instalacoes = [];
	
	//Especiais provenientes de Construções e/ou Techs
	public $slots_extra = 0;
	public $max_slots = 0;
	
	function __construct($id, $turno=0) {
		global $wpdb;

		$this->id = $id;
		$this->turno = new turno($turno);

		$resultados = $wpdb->get_results("SELECT id_estrela, nome, posicao, classe, subclasse, tamanho, inospito FROM colonization_planeta WHERE id=".$this->id);
		$resultado = $resultados[0];
		
		$this->id_estrela = $resultado->id_estrela;
		$this->nome = $resultado->nome;
		$this->posicao = $resultado->posicao;
		$this->classe = $resultado->classe;
		$this->subclasse = $resultado->subclasse;
		$this->tamanho = $resultado->tamanho;
		$this->inospito = $resultado->inospito;
		$this->pop_inospito = 0;

		$this->instalacoes = $wpdb->get_var("SELECT SUM(ci.slots) 
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id} AND turno<={$this->turno->turno}");		
	
		$this->estrela = new estrela($this->id_estrela);
		
		//Verifica se tem Instalações com Especiais
		$id_instalacoes = $wpdb->get_results("
		SELECT cpi.id, cpi.id_instalacao
		FROM colonization_planeta_instalacoes AS cpi
		WHERE cpi.id_planeta={$this->id} AND cpi.turno<={$this->turno->turno}");
		
		foreach ($id_instalacoes as $id) {
			$instalacao = new instalacao($id->id_instalacao);
			$especiais = explode(";",$instalacao->especiais);
			
			//Especiais: slots_extra=qtd
			//Tem também o max_slots=max, que define o máximo de slots

			$slots_extra = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'slots_extra') !== false;
			}));
			
			$max_slots = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'max_slots') !== false;
			}));
			
			if (!empty($slots_extra)) {
				$slots_extra_valor = explode("=",$slots_extra[0]);
				$this->slots_extra = $this->slots_extra + $slots_extra_valor[1];
			}
			
			if (!empty($max_slots)) {
				$max_slots_valor = explode("=",$max_slots[0]);
				if ($this->max_slots < $max_slots_valor[1]) {
					$this->max_slots = $max_slots_valor[1];
				}
			}
			
			//Especiais: pop_inospito=qtd
			$pop_inospito = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pop_inospito') !== false;
			}));
			
			if (!empty($pop_inospito)) {
				$colonia_instalacao = new colonia_instalacao($id->id);

				$pop_inospito_valor = explode("=",$pop_inospito[0]);
				$this->pop_inospito = $this->pop_inospito + $pop_inospito_valor[1]*$colonia_instalacao->nivel;
			}
			
			//Especiais: pdf_instalacoes=valor
			$pdf_instalacoes = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_instalacoes') !== false;
			}));
			
			if (!empty($pdf_instalacoes)) {
				$colonia_instalacao = new colonia_instalacao($id->id);

				$index = count($this->instalacoes_ataque);
				$this->instalacoes_ataque[$index] = $colonia_instalacao->id_instalacao;
			}
		}
		
		if ($this->max_slots != 0) {
			if ($this->slots_extra > $this->max_slots) {
				$this->slots_extra = $this->max_slots;
			}
		}
		
		$this->tamanho = $this->tamanho + $this->slots_extra;
		
		if ($this->inospito == 1) {
			$this->icone_habitavel = "<div class='fas fa-globe tooltip' style='color: #912611;'>&nbsp;<span class='tooltiptext'>Inóspito</span></div>";
		} else {
			$this->icone_habitavel = "<div class='fas fa-globe-americas tooltip' style='color: #005221;'>&nbsp;<span class='tooltiptext'>Habitável</span></div>";
		}
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
				<div data-atributo='gerenciar'><a href='#' onclick='return edita_objeto(event, this);'>Editar</a> | <a href='#' onclick='return excluir_objeto(event, this);'>Excluir</a></div>
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