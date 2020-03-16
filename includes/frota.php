<?php
/**************************
FROTA.PHP
----------------
Cria o objeto "FROTA"
***************************/

//Classe "frota"
//Contém os dados da frota
class frota 
{
	public $id;
	public $id_imperio;
	public $nome;
	public $tipo;
	public $X;
	public $Y;
	public $Z;
	public $tamanho;
	public $velocidade;
	public $alcance;
	public $PDF_laser;
	public $PDF_projetil;
	public $PDF_torpedo;
	public $blindagem;
	public $escudos;
	public $poder_invasao;
	public $especiais;
	public $HP;
	public $qtd;
	public $turno;
	
	function __construct($id) {
		
	}
	

	/***********************
	function lista_dados()
	----------------------
	Exibe os dados do objeto
	***********************/
	function lista_dados() {

	}		
}
?>