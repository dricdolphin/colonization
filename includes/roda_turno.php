<?php
/**************************
RODA_TURNO.PHP
----------------
Responsável por "rodar" os turnos, ou seja, por alterar os dados do jogo.

Para acessá-lo, é necessário ter acesso de administrador do Wordpress e do Fórum.

Antes de rodar o turno, verifica qual a data do último turno e não libera 
para rodar caso não tenha passado pelo menos 24 horas do último turno.
***************************/

include_once(geral.php); //Contém todas as conexões, objetos e estruturas do Colonization

//Classe "roda_turno"
//Contém as rotinas para rodar o turno
class roda_turno {
	/***********************
	function roda_turno ()
	----------------------
	Responsável por rodar o turno!
	***********************/
	function roda_turno () {
		if usuario_autorizado() {
		//TODO -- Processo de rodar o turno!
		}
	}

	/***********************
	function usuario_autorizado ()
	----------------------
	Verifica se o usuário do Wordpress é o Admin
	***********************/
	function usuario_autorizado () {
	//TODO -- verifica o usuário
	}
}
?>