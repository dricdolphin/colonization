<?php
/**************************
VALIDA_NAVE.PHP
----------------
Cria o objeto "valida_nave"
***************************/

//Classe "valida_nave"
//Função auxiliar de validação de naves
class valida_nave
{
	public $imperio;

	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($imperio) {
		global $wpdb;
		
		$this->imperio = $imperio;
	}

	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function valida_nave($tamanho, $X, $Y, $Z, $string_nave, $upgrade = false) {
		global $wpdb, $plugin_colonization;
		//Primeiro verifica se tem uma Estação Orbital no local sendo criado a nave
		//Naves acima de CORVETAS requerem uma Estação Orbital de nível adequado...
		$nivel_estacao_orbital_requerida = 0;
		if (!isset($string_nave['nivel_estacao_orbital'])) {
			$string_nave['nivel_estacao_orbital'] = 0;
		}
		
		if ($string_nave['nivel_estacao_orbital'] == 0) {//Estações Orbitais podem ter qualquer tamanho...
			if ($tamanho > 5000) {
				$nivel_estacao_orbital_requerida = 10;
			} elseif ($tamanho > 1000) {
				$nivel_estacao_orbital_requerida = 8;
			} elseif ($tamanho > 500) {
				$nivel_estacao_orbital_requerida = 7;
			} elseif ($tamanho > 300) {
				$nivel_estacao_orbital_requerida = 6;
			} elseif ($tamanho > 200) {
				$nivel_estacao_orbital_requerida = 5;
			} elseif ($tamanho > 100) {
				$nivel_estacao_orbital_requerida = 4;
			} elseif ($tamanho > 50) {
				$nivel_estacao_orbital_requerida = 3;
			} elseif ($tamanho > 20) {
				$nivel_estacao_orbital_requerida = 2;
			} else {
				$nivel_estacao_orbital_requerida = 1;
			}
			
			//Verifica se tem uma Estação Orbital, e se a Estação tem nivel_estacao_orbital suficiente para construir a nave
			$id_estrela_capital = $wpdb->get_var("
			SELECT cp.id_estrela
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			WHERE cic.id_imperio={$this->imperio->id}
			AND cic.turno={$this->imperio->turno->turno}
			AND cic.capital=true");
			
			$estrela_capital = new estrela($id_estrela_capital);
			
			if ($tamanho > 10) {
				$estacao_orbital_na_estrela = $wpdb->get_var("
				SELECT COUNT(cif.id) 
				FROM colonization_imperio_frota AS cif
				WHERE cif.X={$X} AND cif.Y={$Y} AND cif.Z={$Z}
				AND cif.nivel_estacao_orbital >= {$nivel_estacao_orbital_requerida}
				AND (cif.turno_destruido IS NULL OR cif.turno_destruido = 0)");
				
				
				if ($estacao_orbital_na_estrela == 0) {
					$html_mk = $plugin_colonization->html_mk($nivel_estacao_orbital_requerida);
					return "É necessário ter uma Estação Orbital{$html_mk} no Sistema para poder construir essa nave!";
				}
			}
			
			$colonias_imperio = $wpdb->get_var("
			SELECT COUNT(cic.id) 
			FROM colonization_imperio_colonias AS cic
			JOIN colonization_planeta AS cp
			ON cp.id = cic.id_planeta
			JOIN colonization_estrela AS ce
			ON ce.id = cp.id_estrela
			WHERE ce.X={$X} AND ce.Y={$Y} AND ce.Z={$Z}
			AND cic.id_imperio = {$this->imperio->id} AND cic.turno={$this->imperio->turno->turno}");

			if ($colonias_imperio == 0) {
				if ($upgrade) {
					return "Só é possível atualizar suas naves em suas Colônias!";
				}
				return "Só é possível criar novas naves em suas Colônias!";
			}
			
			$sob_cerco = $wpdb->get_var("SELECT ce.cerco FROM colonization_estrela AS ce
			WHERE ce.X={$X} AND ce.Y={$Y} AND ce.Z={$Z}");
			
			if ($sob_cerco == 1) {
				return "Não é possível criar ou atualizar naves em um sistema sob ataque!";
			}
			unset($string_nave['nivel_estacao_orbital']);
		}
		
		foreach ($string_nave as $chave_tech => $valor) {
			if (str_contains($chave_tech, "mk_") || str_contains($chave_tech, "nivel_estacao_orbital")) {//Todas as chaves "mk_" representam alguma Tech
				$tem_tech = $wpdb->get_var("
				SELECT cit.id
				FROM colonization_imperio_techs AS cit
				JOIN colonization_tech AS ct
				ON ct.id = cit.id_tech
				WHERE cit.id_imperio = {$this->imperio->id}
				AND cit.custo_pago = 0
				AND ct.especiais LIKE '%{$chave_tech}={$valor}%'
				");
				
				if (empty($tem_tech)) {
					$nome_tech = $wpdb->get_var("SELECT nome FROM colonization_tech WHERE especiais LIKE '%{$chave_tech}={$valor}%'");
					$dados_salvos['resposta_ajax'] = "{$chave_tech}\nÉ necessário ter a Tech '{$nome_tech}' para poder construir essa nave!";
					if ($nome_tech == "") {
						return "{$chave_tech}={$valor} \nNão existe Tech que permita construir essa nave!";
					}
					break;
				}
			} elseif (str_contains($chave_tech, "qtd_") || str_contains($chave_tech, "nome_modelo") || str_contains($chave_tech, "id")) {//Pula chaves de qtd, nome_modelo e id
				continue;
			} else {
				$tem_tech = $wpdb->get_var("
				SELECT cit.id
				FROM colonization_imperio_techs AS cit
				JOIN colonization_tech AS ct
				ON ct.id = cit.id_tech
				WHERE cit.id_imperio = {$this->imperio->id}
				AND cit.custo_pago = 0
				AND ct.especiais LIKE '%id={$chave_tech}%'
				");
				
				if (empty($tem_tech)) {
					$nome_tech = $wpdb->get_var("SELECT nome FROM colonization_tech WHERE especiais LIKE '%id={$chave_tech}%'");
					return "{$chave_tech}\nÉ necessário ter a Tech '{$nome_tech}' para poder construir essa nave!";
					break;
				}
			}
		}
		
		return "OK!";
	}
}