<?php
/**************************
RODA_TURNO.PHP
----------------
Responsável por "rodar" os turnos, ou seja, por alterar os dados do jogo.

Para acessá-lo, é necessário ter acesso de administrador do Wordpress e do Fórum.

Antes de rodar o turno, verifica qual a data do último turno e não libera 
para rodar caso não tenha passado pelo menos 24 horas do último turno.
***************************/

//Classe "roda_turno"
//Contém as rotinas para rodar o turno
class roda_turno {

	
	function __construct() {

	}
	
	/******************
	function executa_roda_turno()
	-----------
	Roda o Turno
	******************/	
	function executa_roda_turno() {
		global $wpdb;
		
		$user = wp_get_current_user();
		$roles = $user->roles[0];
		
		$html = "";
		//Rodar o turno é simples: primeiro, CRIAMOS todos os recursos, depois CONSUMIMOS todos os recursos, e por fim AUMENTAMOS a população dos planetas onde isso for possível
		//O sistema tem por peculiaridade executar "ações especiais", que dependem das Tecnologias e outros detalhes dos Impérios (A SER IMPLEMENTADO)
		//Uma das ações padrão é o consumo de Alimentos -- um por Pop de cada colônia
		
		if ($roles == "administrator") {//Somente pode rodar o turno se for um Administrador
			$turno = new turno(); //Pega o turno atual
			$proximo_turno = $turno->turno + 1;
			
			if ($turno->bloqueado) {
				$html = "Não é possível rodar o turno. Ele se encontra BLOQUEADO!<br>
				<div><a href='#' class='page-title-action colonization_admin_botao' onclick='return desbloquear_turno(event);'>DESBLOQUEAR TURNO</a></div>
				";
				return $html;
			}
			
			$imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
			foreach ($imperios as $id_imperio) {
				$html .= "<br>";
				$imperio = new imperio($id_imperio->id);
				$imperio_recursos = new imperio_recursos($imperio->id);	

				//Vamos modificar os estoques!
				//Primeiro, CONSUME os Recursos dos Planetas
				$resultados = $wpdb->get_results(
				"SELECT cir.id_recurso, cpi.id_planeta, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM colonization_acoes_turno AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				JOIN colonization_recurso AS cr
				ON cir.id_recurso = cr.id
				WHERE cat.id_imperio={$imperio->id} AND cat.turno={$turno->turno} AND cir.consome=false AND cpi.turno_destroi IS NULL AND cr.acumulavel = true
				GROUP BY cr.nome");
				
				$html .= "CONSUMINDO Recursos Planetários do Império {$imperio->id}:<br>";
				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					$qtd = $imperio_recursos->qtd[$chave] + $resultado->producao;

					$recursos_disponivel = $wpdb->get_var("SELECT disponivel FROM colonization_planeta_recursos WHERE id_planeta={$resultado->id_planeta} AND id_recurso={$resultado->id_recurso} AND turno={$turno->turno}");
					if ($recursos_disponivel > 0) {
						$recursos_disponivel = $recursos_disponivel - $resultado->producao;
						$html .= "INSERT INTO colonization_planeta_recursos SET id_planeta={$resultado->id_planeta}, id_recurso ={$resultado->id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}<br>";
						//$wpdb->query("INSERT INTO colonization_planeta_recursos SET id_planeta={$resultado->id_planeta}, id_recurso ={$resultado->id_recurso}, disponivel={$recursos_disponivel}, turno={$proximo_turno}");
					}
				}
				
				$html .= "<br>FAZENDO O BALANÇO dos Recursos do Império {$imperio->id}:<br>";
				//Depois, FAZ O BALANÇO dos recursos dos Estoques
				$resultados = $wpdb->get_results("
				SELECT nome, id_recurso, (producao-consumo+estoque) AS balanco 
				FROM (
					SELECT cr.nome, cr.id as id_recurso, (CASE WHEN tabela_produz.producao IS NULL THEN 0 ELSE tabela_produz.producao END) AS producao, 
					(CASE WHEN tabela_consome.producao IS NULL THEN 0 ELSE tabela_consome.producao END) AS consumo, 
					cimr.qtd AS estoque 
					FROM colonization_recurso AS cr
					LEFT JOIN (
						SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
						FROM 
						(SELECT turno, id_imperio, id_instalacao, id_planeta, pop
						FROM colonization_acoes_turno 
						WHERE id_imperio={$imperio->id} AND turno={$turno->turno}
						) AS cat
						JOIN colonization_planeta_instalacoes AS cpi
						ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
						JOIN colonization_instalacao_recursos AS cir
						ON cir.id_instalacao = cat.id_instalacao
						WHERE cir.consome=false AND cpi.turno_destroi IS NULL
						GROUP BY cir.id_recurso
					) AS tabela_produz
					ON tabela_produz.id_recurso = cr.id
					LEFT JOIN (
					SELECT cir.id_recurso, cat.turno, cat.id_imperio, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
						FROM 
						(SELECT turno, id_imperio, id_instalacao, id_planeta, pop
						FROM colonization_acoes_turno 
						WHERE id_imperio={$imperio->id} AND turno={$turno->turno}
						) AS cat
						JOIN colonization_planeta_instalacoes AS cpi
						ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
						JOIN colonization_instalacao_recursos AS cir
						ON cir.id_instalacao = cat.id_instalacao
						WHERE cir.consome=true AND cpi.turno_destroi IS NULL
						GROUP BY cir.id_recurso
					) AS tabela_consome
					ON tabela_consome.id_recurso = cr.id
					LEFT JOIN colonization_imperio_recursos AS cimr
					ON cimr.id_imperio = {$imperio->id}
					AND cimr.id_recurso = cr.id 
					AND cimr.turno = {$turno->turno}
				) AS tabela_balanco
				ORDER BY (producao-consumo) ASC
				");				
				
				
				//Faz o balanço dos resultados
				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					
					if ($resultado->id_recurso == 7) {//Se for ALIMENTO, precisamos alterar o valor do balanço, pois a população consome Alimentos
						$resultado->balanco = $resultado->balanco - $imperio->pop;
					}
					
					$html .= "INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$resultado->balanco}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}<br>";
					//$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$qtd}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}");
				}
			}
		} else {
			$html = "É NECESSÁRIO TER PRIVILÉGIOS ADMINISTRATIVOS PARA RODAR O TURNO!";
		}
		
		return $html;
	}
}
?>