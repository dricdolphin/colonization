<?php
/**************************
TURNO.PHP
----------------
Cria o objeto "turno"
***************************/

//Classe "turno"
//Contém os dados do turno
class turno
{
	public $turno;
	public $data_turno;
	public $bloqueado;
	public $encerrado;
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($turno=0) {
		global $wpdb;

		if ($turno == 0) {
			$resultados = $wpdb->get_results("SELECT id, data_turno, bloqueado, encerrado FROM colonization_turno_atual ORDER BY id DESC");
		} else {
			$resultados = $wpdb->get_results("SELECT id, data_turno, bloqueado, encerrado FROM colonization_turno_atual WHERE id={$turno}");
		}

		if (empty($resultados)) {//Turno inválido!
			$resultados = $wpdb->get_results("SELECT id, data_turno, bloqueado, encerrado FROM colonization_turno_atual ORDER BY id DESC");
		}
		$resultado = $resultados[0];
		
		$this->turno = $resultado->id;
		$this->data_turno = $resultado->data_turno;
		$this->bloqueado = $resultado->bloqueado;
		$this->encerrado = $resultado->encerrado;
	}
}