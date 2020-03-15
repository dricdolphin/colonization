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
		$user = wp_get_current_user();
		$roles = $user->roles[0];

		//Rodar o turno é simples: primeiro, CRIAMOS todos os recursos, depois CONSUMIMOS todos os recursos, e por fim AUMENTAMOS a população dos planetas onde isso for possível
		//O sistema tem por peculiaridade executar "ações especiais", que dependem das Tecnologias e outros detalhes dos Impérios (A SER IMPLEMENTADO)
		//Uma das ações padrão é o consumo de Alimentos -- um por Pop de cada colônia
		
		if ($roles == "administrator") {//Somente pode rodar o turno se for um Administrador
			$turno = new turno(); //Pega o turno atual
			$proximo_turno = $turno->turno + 1;
			
			$imperios = $wpdb->get_results("SELECT id FROM colonization_imperio");
			foreach ($imperios as $id_imperio) {
				$imperio = new imperio($id_imperio->id);
				$imperio_recursos = new imperio_recursos($imperio->id);	

				//Vamos modificar os estoques!
				//Primeiro, PRODUZ os recursos
				$resultados = $wpdb->get_results(
				"SELECT cr.id_recurso, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM colonization_acoes_turno AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				JOIN colonization_recurso AS cr
				ON cir.id_recurso = cr.id
				WHERE cat.id_imperio={$imperio->id} AND cat.turno={$turno->turno} AND cir.consome=false AND cpi.turno_destroi IS NULL AND cr.acumulavel = true
				GROUP BY cr.nome");

				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					$qtd = $imperio_recursos->['qtd'] + $resultado->producao;
					
					$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$qtd}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}");
				}
				
				//Depois, CONSOME os recursos, tanto dos
				$resultados = $wpdb->get_results(
				"SELECT cr.id_recurso, cr.nome, SUM(FLOOR((cir.qtd_por_nivel * cpi.nivel * cat.pop)/10)) AS producao
				FROM colonization_acoes_turno AS cat
				JOIN colonization_planeta_instalacoes AS cpi
				ON cpi.id_instalacao = cat.id_instalacao AND cpi.id_planeta = cat.id_planeta
				JOIN colonization_instalacao_recursos AS cir
				ON cir.id_instalacao = cat.id_instalacao
				JOIN colonization_recurso AS cr
				ON cir.id_recurso = cr.id
				WHERE cat.id_imperio={$imperio->id} AND cat.turno={$turno->turno} AND cir.consome=false AND cpi.turno_destroi IS NULL AND cr.acumulavel = true
				GROUP BY cr.nome");

				foreach ($resultados as $resultado) {
					$chave = array_search($resultado->id_recurso,$imperio_recursos->id_recurso);
					$qtd = $imperio_recursos->['qtd'] + $resultado->producao;
					
					$wpdb->query("INSERT INTO colonization_imperio_recursos SET id_imperio={$imperio->id}, id_recurso ={$resultado->id_recurso}, qtd={$qtd}, turno={$proximo_turno}, disponivel={$imperio_recursos->disponivel[$chave]}");
				}


			}
		}
	}
}
?>