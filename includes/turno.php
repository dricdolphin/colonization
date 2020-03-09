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
	
	/***********************
	function __construct()
	----------------------
	Inicializa os dados do turno
	***********************/
	function __construct($turno=0) {
		global $wpdb;

		if ($turno == 0) {
			$resultados = $wpdb->get_results("SELECT MAX(id) AS id, data_turno, bloqueado FROM colonization_turno_atual");
		} else {
			$resultados = $wpdb->get_results("SELECT id, data_turno, bloqueado FROM colonization_turno_atual WHERE id={$turno}");
		}
		if ($resultados == 0) {
			$resultados = $wpdb->get_results("SELECT MAX(id) AS id, data_turno, bloqueado FROM colonization_turno_atual");
		}
		$resultado = $resultados[0];
		
		$this->turno = $resultado->id;
		$this->data_turno = $resultado->data_turno;
		$this->bloqueado = $resultado->bloqueado;
	}
}