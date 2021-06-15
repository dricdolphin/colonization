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
	
	public $recurso_planeta = [];
	
	//Defesas Planetárias oferecidas por Instalações
	public $instalacoes_ataque = [];
	public $instalacoes_ataque_nivel = [];
	public $html_instalacao_ataque = [];
	public $mini_html_instalacao_ataque = [];
	
	public $escudos;
	
	//public $pdf_instalacoes = [];
	
	//Especiais provenientes de Construções e/ou Techs
	public $slots_extra = 0;
	public $max_slots = 0;
	public $alcance_local = 0;
	public $buraco_de_minhoca = 0;
	public $tamanho_alcance_local = 0;
	public $terraforma = 0;
	
	public $popula_instalacoes_planeta = false;
	
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
		WHERE cpi.id_planeta={$this->id} AND turno<={$this->turno->turno} AND (turno_desmonta = 0 OR turno_desmonta IS NULL)");
	
		//$this->estrela = new estrela($this->id_estrela);

		//Atualiza os recursos do Planeta para o Turno atual, se necessário
		$max_turnos = $wpdb->get_results("SELECT cpr.id_recurso, MAX(cpr.turno) as turno 
		FROM colonization_planeta_recursos AS cpr
		WHERE cpr.id_planeta={$this->id} 
		GROUP BY cpr.id_recurso, cpr.id_planeta");

		foreach ($max_turnos as $max_turno) {
			if ($max_turno->turno < $this->turno->turno) {//Atualiza os recursos do planeta caso não esteja no Turno Atual
				$id_planeta_recurso = $wpdb->get_var("SELECT id FROM colonization_planeta_recursos WHERE id_planeta={$this->id} AND id_recurso={$max_turno->id_recurso} AND turno={$max_turno->turno}");
				
				$planeta_recurso = new planeta_recurso($id_planeta_recurso);
				$wpdb->query("INSERT INTO colonization_planeta_recursos SET turno={$this->turno->turno}, id_planeta={$this->id}, id_recurso={$max_turno->id_recurso}, disponivel={$planeta_recurso->qtd_disponivel}");					
			}
		}
	}

	/***********************
	function popula_instalacoes_planeta()
	----------------------
	Popula os dados do Planeta que dependem de Instalações
	***********************/
	function popula_instalacoes_planeta() {
		global $wpdb;
		
		if ($this->popula_instalacoes_planeta) {
			return;
		}
		
		$user = wp_get_current_user();
		$roles = "";
		if (!empty($user->ID)) {
			$roles = $user->roles[0];
		}
		
		//Verifica se tem Instalações com Especiais
		$id_instalacoes = $wpdb->get_results("
		SELECT cpi.id, cpi.id_instalacao
		FROM colonization_planeta_instalacoes AS cpi
		JOIN colonization_instalacao AS ci
		ON ci.id = cpi.id_instalacao
		WHERE cpi.id_planeta={$this->id} AND cpi.turno<={$this->turno->turno} AND (cpi.turno_destroi = 0 OR cpi.turno_destroi IS NULL)
		AND ci.especiais != ''");
		
		//Precisa verificar se não houve upgrade da instalação
		$instalacao_temp = [];
		$colonia_instalacao_temp = [];
		foreach ($id_instalacoes as $id) {
			if (empty($instalacao_temp[$id->id_instalacao])) {
				$instalacao_temp[$id->id_instalacao] = new instalacao($id->id_instalacao);
			}
			$instalacao = $instalacao_temp[$id->id_instalacao];
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
				$pop_inospito_valor = explode("=",$pop_inospito[0]);
				if (empty($colonia_instalacao_temp[$id->id])) {
					$colonia_instalacao_temp[$id->id] = new colonia_instalacao($id->id);
				}
				$colonia_instalacao = $colonia_instalacao_temp[$id->id];
				$this->pop_inospito = $this->pop_inospito + $pop_inospito_valor[1]*$colonia_instalacao->nivel;
			}
			
			//habitavel=1
			$terraforma = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'habitavel') !== false;
			}));
			
			if (!empty($terraforma)) {
				$habitavel_valor = explode("=",$terraforma[0]);
				$this->terraforma = $habitavel_valor[1];
			}

			//Especiais: alcance_local=qtd
			$alcance_local = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'alcance_local') !== false;
			}));
			
			if (!empty($alcance_local)) {
				$alcance_local_valor = explode("=",$alcance_local[0]);
				if ($alcance_local_valor[1] > $this->alcance_local) {
					$this->alcance_local = $alcance_local_valor[1];
				}
				
				if (empty($colonia_instalacao_temp[$id->id])) {
					$colonia_instalacao_temp[$id->id] = new colonia_instalacao($id->id);
				}
				$colonia_instalacao = $colonia_instalacao_temp[$id->id];
				
				if (10*$colonia_instalacao->nivel > $this->tamanho_alcance_local) {
					$this->tamanho_alcance_local = 10*$colonia_instalacao->nivel;
				}
			}

			//Especiais: buraco_de_minhoca=1
			$buraco_de_minhoca = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'buraco_de_minhoca') !== false;
			}));
			
			if (!empty($buraco_de_minhoca)) {
				$buraco_de_minhoca_valor = explode("=",$buraco_de_minhoca[0]);
				$this->buraco_de_minhoca = $buraco_de_minhoca_valor[1];
			}
			
			//Especiais: escudo=1
			$escudos = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'escudos') !== false;
			}));
			
			if (!empty($escudos)) {
				$this->escudos = "<div class='{$instalacao->icone} tooltip'>&nbsp;</div> - <b>{$instalacao->nome}</b>";
			}
			
			//Especiais: pdf_instalacoes=valor
			$pdf_instalacoes = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_instalacoes') !== false;
			}));
			
			if (!empty($pdf_instalacoes)) {
				if (empty($colonia_instalacao_temp[$id->id])) {
					$colonia_instalacao_temp[$id->id] = new colonia_instalacao($id->id);
				}
				$colonia_instalacao = $colonia_instalacao_temp[$id->id];

				//$index = count($this->instalacoes_ataque);
				$this->instalacoes_ataque[] = $colonia_instalacao->id_instalacao;
				$this->instalacoes_ataque_nivel[] = $colonia_instalacao->nivel;
			}
		}
		
		$qtd_instalacao_ataque_id = [];
		foreach ($this->instalacoes_ataque as $chave => $id_instalacao) {
			if (empty($instalacao_temp[$id_instalacao])) {
				$instalacao_temp[$id_instalacao] = new instalacao($id_instalacao);
			}
			$instalacao_ataque = $instalacao_temp[$id_instalacao];
			$especiais = explode(";",$instalacao_ataque->especiais);
			//Especiais: pdf_instalacoes=valor
			$pdf_instalacoes = array_values(array_filter($especiais, function($value) {
				return strpos($value, 'pdf_instalacoes') !== false;
			}));				
			
			if (empty($pdf_instalacoes)) {
				continue;
			}

			$pdf_instalacoes = explode("=",$pdf_instalacoes[0]);
			$pdf_instalacoes = $pdf_instalacoes[1]*$this->instalacoes_ataque_nivel[$chave];				
			
			if (!empty($qtd_instalacao_ataque_id[$id_instalacao])) {
				$qtd_instalacao_ataque_id[$id_instalacao]++;
				$qtd_instalacao="{$qtd_instalacao_ataque_id[$id_instalacao]} x";
			} else {
				$qtd_instalacao_ataque_id[$id_instalacao] = 1;
				$qtd_instalacao = "";
			}
			
			$this->html_instalacao_ataque[$id_instalacao] = "{$qtd_instalacao}<div class='{$instalacao_ataque->icone} tooltip'><span class='tooltiptext'>{$instalacao_ataque->nome}</span><span style='font-family: Verdana, Tahoma, sans-serif;'>PdF Planetário:{$pdf_instalacoes}</span></div><br>";
			$this->mini_html_instalacao_ataque[$id_instalacao] = "{$qtd_instalacao}<div class='mini_instalacao_ataque {$instalacao_ataque->icone} tooltip'><span class='tooltiptext'>{$instalacao_ataque->nome} | PdF Planetário</span>:{$pdf_instalacoes}</div>";
		}
		
		if ($this->max_slots != 0) {
			if ($this->slots_extra > $this->max_slots) {
				$this->slots_extra = $this->max_slots;
			}
		}
		
		$this->tamanho = $this->tamanho + $this->slots_extra;		
		$this->popula_instalacoes_planeta = true;
	}
	
	/***********************
	function tamanho()
	----------------------
	Retorna o tamanho do planeta, incluindo os valores de slots extra
	***********************/
	function tamanho() {
		$this->popula_instalacoes_planeta();
		return $this->tamanho;
	}
	

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	$id_estrela -- caso tenha vindo da página de edição de Estrelas
	***********************/
	function lista_dados($id_estrela = 0) {
		global $wpdb;
		if (empty($this->estrela)) {
			$this->estrela = new estrela($this->id_estrela);
		}
		
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

	/***********************
	function exibe_recursos_planeta()
	----------------------
	Exibe os recursos do planeta
	***********************/	
	function exibe_recursos_planeta ($exibe_icones = false) {
		global $wpdb;

		$ids_recursos_planeta = $wpdb->get_results("SELECT cpr.id_recurso, cpr.disponivel 
		FROM colonization_planeta_recursos AS cpr
		JOIN colonization_recurso AS cr
		ON cr.id = cpr.id_recurso
		WHERE cpr.id_planeta={$this->id} AND cpr.turno={$this->turno->turno} 
		ORDER BY cr.nivel, cpr.disponivel DESC, cr.nome");
		
		$html = "";
		foreach ($ids_recursos_planeta as $recurso_planeta) {
			$recurso = new recurso($recurso_planeta->id_recurso);
			$this->recurso_planeta[$recurso->id] = $recurso_planeta->disponivel;
			
			$nome_recurso = $recurso->nome;
			$nome_tooltip = "";
			if ($exibe_icones) {
				if ($recurso->icone != "") {
					$nome_recurso = "<div class='{$recurso->icone}'></div>";
					$nome_tooltip = "{$recurso->nome}: ";
				}
			}
			$html .= "<div class='tooltip' style='display: inline-block;' data-atributo='recurso_planeta'>{$nome_recurso}<span class='tooltiptext'>{$nome_tooltip}{$recurso->descricao}</span> - {$recurso_planeta->disponivel}; &nbsp;
						
					</div>";
		}
		
		return $html;
	}

	/***********************
	function icone_habitavel()
	----------------------
	Popula e exibe o ícone de habitabilidade do planeta
	***********************/	
	function icone_habitavel ($exibe_icones = false) {
		global $wpdb;
		
		$globo_inospito = "fas fa-globe";
		$globo_habitavel = "fas fa-globe-americas";
		if ($this->classe == "Gigante Gasoso") {
			$globo_inospito = "fas fa-planet-ringed";
		}
		
		if ($this->inospito == 1) {
			$this->icone_habitavel = "<div class='{$globo_inospito} tooltip' style='color: #912611;'>&nbsp;<span class='tooltiptext'>Inóspito</span></div>";
			if ($this->terraforma == 1) {
				$this->icone_habitavel = "<div class='fas fa-globe-europe tooltip' style='color: #AEB213;'>&nbsp;<span class='tooltiptext'>Terraformado</span></div>";
			}
		} else {
			$this->icone_habitavel = "<div class='{$globo_habitavel} tooltip' style='color: #005221;'>&nbsp;<span class='tooltiptext'>Habitável</span></div>";
		}
		
		return $this->icone_habitavel;
	}
}

?>