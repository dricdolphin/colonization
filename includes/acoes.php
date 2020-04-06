<?php
/**************************
ACOES.PHP
----------------
Cria o objeto "ações", que contém todas 
as ações de um determinado turno de um jogador.
***************************/

//Classe "acoes"
//Contém os dados da instalacao
class acoes 
{
	public $id = [];
	public $id_imperio;
	public $id_planeta = [];
	public $id_instalacao = [];
	public $id_planeta_instalacoes = [];
	public $nivel_instalacao = [];
	public $pop = [];
	public $turno;
	public $data_modifica = [];
	public $max_data_modifica;
	
	function __construct($id_imperio, $turno=0) {
		global $wpdb;
		
		//É necessário pegar o id_imperio à partir do objeto "Império", pois este contém a validação do jogador
		$this->turno = new turno($turno);
		
		$imperio = new imperio($id_imperio, $this->turno->turno);
		$this->id_imperio = $imperio->id;

		

		$resultados =$wpdb->get_results("
			SELECT cic.id_imperio, cat.id AS id, cic.id_planeta AS id_planeta, cpi.id AS id_planeta_instalacoes, cpi.id_instalacao AS id_instalacao, cpi.nivel AS nivel_instalacao, cat.pop AS pop, cat.data_modifica AS data_modifica
			FROM colonization_imperio_colonias AS cic 
			JOIN colonization_planeta_instalacoes AS cpi
			ON cpi.id_planeta = cic.id_planeta
			LEFT JOIN 
			(SELECT id, id_planeta, id_instalacao, id_planeta_instalacoes, id_imperio, pop, data_modifica
			 FROM colonization_acoes_turno
			 WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
			) AS cat
			ON cat.id_planeta = cic.id_planeta
			AND cat.id_instalacao = cpi.id_instalacao
			AND cat.id_planeta_instalacoes = cpi.id
			AND cat.id_imperio = cic.id_imperio
			JOIN colonization_instalacao AS ci
			ON ci.id = cpi.id_instalacao
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE cic.id_imperio = {$this->id_imperio} 
			AND cic.turno = {$this->turno->turno}
			AND cpi.turno_destroi IS NULL
			AND cpi.turno <={$this->turno->turno}
			ORDER BY ce.X, ce.Y, ce.Z, cp.posicao, ci.nome, cpi.id
			");
		
		$chave = 0;
		foreach ($resultados as $valor) {
			if ($valor->id === null) {
				$this->id[$chave] = 0;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->id_planeta_instalacoes[$chave] = $valor->id_planeta_instalacoes;
				$this->nivel_instalacao[$chave] = $valor->nivel_instalacao;
				$this->pop[$chave] = 0;
				$this->data_modifica[$chave] = $this->turno->data_turno;
			} else {
				$this->id[$chave] = $valor->id;
				$this->id_planeta[$chave] = $valor->id_planeta;
				$this->id_instalacao[$chave] = $valor->id_instalacao;
				$this->id_planeta_instalacoes[$chave] = $valor->id_planeta_instalacoes;
				$this->nivel_instalacao[$chave] = $valor->nivel_instalacao;
				$this->pop[$chave] = $valor->pop;
				$this->data_modifica[$chave] = $valor->data_modifica;
			}
			$chave++;
		}

		$chave = 0;
		if (isset($this->id[$chave])) {
			foreach ($this->id as $chave => $valor) {
				if ($this->id[$chave] == 0) {//As chaves estão em branco, vamos criá-las!
					//Se tiver um valor no turno anterior, é para mantê-lo no turno atual
					$turno_anterior = $this->turno->turno - 1;
					$pop_turno_anterior = $wpdb->get_var("SELECT pop FROM colonization_acoes_turno 
					WHERE id_imperio={$this->id_imperio} AND id_planeta={$this->id_planeta[$chave]} AND turno={$turno_anterior} AND id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]}");
					
					if ($pop_turno_anterior === null) {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$this->pop[$chave]}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
					} else {
						$wpdb->query("INSERT INTO colonization_acoes_turno 
						SET id_imperio={$this->id_imperio}, id_planeta={$this->id_planeta[$chave]}, id_instalacao={$this->id_instalacao[$chave]}, id_planeta_instalacoes={$this->id_planeta_instalacoes[$chave]},
						pop={$pop_turno_anterior}, data_modifica='{$this->data_modifica[$chave]}', turno={$this->turno->turno}");
						$this->pop[$chave] = $pop_turno_anterior;
					}
					$this->id[$chave] = $wpdb->insert_id;
				}
			}
		}
	
		$this->max_data_modifica = $wpdb->get_var("SELECT MAX(data_modifica) FROM colonization_acoes_turno WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}");
	}

	/***********************
	function mdo_planeta()
	----------------------
	Exibe a MdO alocada em um planeta
	----
	$id_planeta = planeta escolhido
	***********************/
	function mdo_planeta($id_planeta) {
		global $wpdb;
		
		$mdo = 0;

		for ($chave=0; $chave < count($this->id_planeta); $chave++) {
			if ($this->id_planeta[$chave] == $id_planeta) {
				$mdo = $mdo + $this->pop[$chave];
			}
		}
	
		/***DEBUG!
		$user = wp_get_current_user();
		$roles = $user->roles[0];

		if ($roles == "administrator") {
			echo ("id_planeta: ".$id_planeta."<br><br>");
			var_dump($this->id_planeta);
			echo "<br><br>";
			var_dump($this->pop);
			echo "<br><br>";
			var_dump($chaves);
			wp_die();
		}
		***/
		
		return $mdo;
	}

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	----
	$turno_atual = somente libera para edição se o Turno exibido for o Turno atual
	***********************/
	function lista_dados($turno_atual=true) {
		global $wpdb;
		$disabled = "";
		
		if (!$turno_atual) {
			$turno_atual = new turno();
			if ($this->turno->turno != $turno_atual->turno) {
				$disabled = 'disabled';
			}
		}
		
		$html = "";
		foreach ($this->id AS $chave => $valor) {
			$planeta = new planeta($this->id_planeta[$chave]);
			$estrela = new estrela($planeta->id_estrela);
			$instalacao = new instalacao($this->id_instalacao[$chave]);
			
			switch($this->nivel_instalacao[$chave]) {
			case 1:
				$nivel = "Mk I";
				break;
			case 2:
				$nivel = "Mk II";
				break;
			case 3:
				$nivel = "Mk III";
				break;
			case 4:
				$nivel = "Mk IV";
				break;
			case 5:
				$nivel = "Mk V";
				break;
			case 6:
				$nivel = "Mk VI";
				break;
			case 7:
				$nivel = "Mk VII";
				break;
			case 8:
				$nivel = "Mk VIII";
				break;
			default:
				$nivel = "";
			}
			
			if ($instalacao->desguarnecida == 0) {
				$exibe_acoes = "<input data-atributo='pop' data-ajax='true' data-valor-original='{$this->pop[$chave]}' type='range' min='0' max='10' value='{$this->pop[$chave]}' oninput='return altera_acao(event, this);' {$disabled}></input>&nbsp;&nbsp;&nbsp;<label data-atributo='pop' style='width: 20px;'>{$this->pop[$chave]}</label>";
			} else {
				$exibe_acoes = "&nbsp";
			}

				$html .= "		<tr><td>
					<input type='hidden' data-atributo='id' data-valor-original='{$this->id[$chave]}' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='{$this->id_imperio}' value='{$this->id_imperio}'></input>
					<input type='hidden' data-atributo='id_planeta' data-ajax='true' data-valor-original='{$this->id_planeta[$chave]}' value='{$this->id_planeta[$chave]}'></input>
					<input type='hidden' data-atributo='id_instalacao' data-ajax='true' data-valor-original='{$this->id_instalacao[$chave]}' value='{$this->id_instalacao[$chave]}'></input>
					<input type='hidden' data-atributo='id_planeta_instalacoes' data-ajax='true' data-valor-original='{$this->id_planeta_instalacoes[$chave]}' value='{$this->id_planeta_instalacoes[$chave]}'></input>
					<input type='hidden' data-atributo='turno' data-ajax='true' data-valor-original='{$this->turno->turno}' value='{$this->turno->turno}'></input>
					<input type='hidden' data-atributo='where_clause' value='id'></input>
					<input type='hidden' data-atributo='where_value' value='{$this->id[$chave]}'></input>
					<input type='hidden' data-atributo='funcao_validacao' value='valida_acao'></input>
					<div data-atributo='nome_planeta' data-valor-original='{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z}) | {$planeta->posicao}'>{$planeta->nome} ({$estrela->X};{$estrela->Y};{$estrela->Z}) | {$planeta->posicao}</div>
				</td>
				<td><div data-atributo='nome_instalacao' data-valor-original='{$instalacao->nome}'>{$instalacao->nome} {$nivel}</div></td>
				<td><div data-atributo='pop' data-valor-original='{$this->pop[$chave]}' data-ajax='true' style='display: flex; align-items: center; justify-content:center;'>{$exibe_acoes}</div></td>
				<td><div data-atributo='gerenciar' style='visibility: hidden;'><a href='#' onclick='return salva_acao(event, this);'>Salvar</a> | <a href='#' onclick='return salva_acao(event, this,true);'>Cancelar</a></div></td>
				</tr>";
		}
		
		return $html;
	}


	/***********************
	function exibe_recursos_produzidos()
	----------------------
	Exibe os recursos produzidos pelas ações
	***********************/
	function exibe_recursos_produzidos() {
		global $wpdb;
				
		$html = "<b>Recursos Produzidos:</b> ";
		
		$resultados = $wpdb->get_results(
		"SELECT cat.pop, cir.id_recurso, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
		FROM
			(SELECT cat.turno, cat.id_imperio, cat.id_instalacao, cat.id_planeta_instalacoes, cat.id_planeta, (CASE WHEN ci.desguarnecida = true THEN 10 ELSE cat.pop END) AS pop
			FROM colonization_acoes_turno AS cat
			JOIN colonization_instalacao AS ci
			ON ci.id = cat.id_instalacao
			WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
			) AS cat
		JOIN colonization_planeta_instalacoes AS cpi
		ON cpi.id = cat.id_planeta_instalacoes
		JOIN colonization_instalacao_recursos AS cir
		ON cir.id_instalacao = cat.id_instalacao
		JOIN colonization_recurso AS cr
		ON cir.id_recurso = cr.id
		WHERE cat.id_imperio={$this->id_imperio} AND cat.turno={$this->turno->turno} AND cir.consome=false AND cpi.turno_destroi IS NULL
		GROUP BY cr.nome
		"
		);

		foreach ($resultados as $resultado) {
			/***************************************************
			--- MODIFICAÇÕES ESPECIAIS NO BALANÇO DO TURNO ---
			***************************************************/
			//TODO -- Aqui entram os Especiais de cada Império
			//No caso, tenho apenas o "hard-coded" do Império 3
			if ($this->id_imperio == 3) {
				if ($resultado->id_recurso !== null) {
					if ($wpdb->get_var("SELECT extrativo FROM colonization_recurso WHERE id={$resultado->id_recurso}") && $resultado->pop == 10) {
						$resultado->producao = $resultado->producao + 1;
					}
				}
			}

			if ($resultado->producao > 0) {
				$html .= "{$resultado->nome}: {$resultado->producao}; ";
			}
		}
		
		return $html;
	}

	/***********************
	function exibe_recursos_consumidos()
	----------------------
	Exibe os recursos consumidos pelas ações
	***********************/
	function exibe_recursos_consumidos() {
		global $wpdb;
		
		$html = "<b>Recursos Consumidos:</b> ";

		$resultados = $wpdb->get_results(
		"SELECT cat.pop, cir.id_recurso, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
		FROM
			(SELECT cat.turno, cat.id_imperio, cat.id_instalacao, cat.id_planeta_instalacoes, cat.id_planeta, (CASE WHEN ci.desguarnecida = true THEN 10 ELSE cat.pop END) AS pop
			FROM colonization_acoes_turno AS cat
			JOIN colonization_instalacao AS ci
			ON ci.id = cat.id_instalacao
			WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
			) AS cat
		JOIN colonization_planeta_instalacoes AS cpi
		ON cpi.id = cat.id_planeta_instalacoes
		JOIN colonization_instalacao_recursos AS cir
		ON cir.id_instalacao = cat.id_instalacao
		JOIN colonization_recurso AS cr
		ON cir.id_recurso = cr.id
		WHERE cat.id_imperio={$this->id_imperio} AND cat.turno={$this->turno->turno} AND cir.consome=true AND cpi.turno_destroi IS NULL
		GROUP BY cr.nome
		"
		);

		foreach ($resultados as $resultado) {
			if ($resultado->producao > 0) {
				$html .= "{$resultado->nome}: {$resultado->producao}; ";
			}
		}
		
		return $html;
	}

	/***********************
	function exibe_recursos_balanco()
	----------------------
	Exibe o balanço dos recursos
	***********************/
	function exibe_recursos_balanco() {
		global $wpdb;
		
		$html = "";

		$resultados = $wpdb->get_results("
		SELECT pop, id_recurso, nome, (producao-consumo) AS balanco 
		FROM (
			SELECT tabela_produz.pop, tabela_produz.id_recurso, cr.nome, (CASE WHEN tabela_produz.producao IS NULL THEN 0 ELSE tabela_produz.producao END) AS producao, 
			(CASE WHEN tabela_consome.producao IS NULL THEN 0 ELSE tabela_consome.producao END) AS consumo, 
			cimr.qtd AS estoque 
			FROM colonization_recurso AS cr
			LEFT JOIN (
				SELECT cat.pop, cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM 
				(SELECT cat.turno, cat.id_imperio, cat.id_instalacao, cat.id_planeta_instalacoes, cat.id_planeta, (CASE WHEN ci.desguarnecida = true THEN 10 ELSE cat.pop END) AS pop
				FROM colonization_acoes_turno AS cat
				JOIN colonization_instalacao AS ci
				ON ci.id = cat.id_instalacao
				WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
				) AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				WHERE cir.consome=false AND cpi.turno_destroi IS NULL
				GROUP BY cir.id_recurso
			) AS tabela_produz
			ON tabela_produz.id_recurso = cr.id
			LEFT JOIN (
			SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM 
				(SELECT turno, id_imperio, id_instalacao, id_planeta_instalacoes, id_planeta, pop
				FROM colonization_acoes_turno 
				WHERE id_imperio={$this->id_imperio} AND turno={$this->turno->turno}
				) AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id = cat.id_planeta_instalacoes
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				WHERE cir.consome=true AND cpi.turno_destroi IS NULL
				GROUP BY cir.id_recurso
			) AS tabela_consome
			ON tabela_consome.id_recurso = cr.id
			LEFT JOIN colonization_imperio_recursos AS cimr
			ON cimr.id_imperio = tabela_produz.id_imperio 
			AND cimr.id_recurso = tabela_produz.id_recurso 
			AND cimr.turno = tabela_produz.turno
		) AS tabela_balanco
		ORDER BY (producao-consumo) ASC
		");

		foreach ($resultados as $resultado) {
			/***************************************************
			--- MODIFICAÇÕES ESPECIAIS NO BALANÇO DO TURNO ---
			***************************************************/
			//TODO -- Aqui entram os Especiais de cada Império
			//No caso, tenho apenas o "hard-coded" do Império 3
			if ($this->id_imperio == 3) {
				if ($resultado->id_recurso !== null) {
					if ($wpdb->get_var("SELECT extrativo FROM colonization_recurso WHERE id={$resultado->id_recurso}") && $resultado->pop == 10) {
						$resultado->balanco = $resultado->balanco + 1;
					}
				}
			}
			
			if ($resultado->balanco > 0) {
				$html .= "{$resultado->nome}: {$resultado->balanco}; ";
			} elseif ($resultado->balanco < 0) {
				$html .= "{$resultado->nome}: <span style='color: #FF2222;'>{$resultado->balanco}</span>; ";
			}
				
		}
		
		return $html;
	}

}
?>
