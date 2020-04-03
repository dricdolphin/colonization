var industrializaveis = 0;
var energium = 0;
var dillithium = 0;
var duranium = 0;

var chassi = 0;
var categoria = "Corveta";

var pdf_laser = 0;
var pdf_torpedo = 0;
var pdf_projetil = 0;
var blindagem = 0;
var escudos = 0;
var alcance = 0;
var velocidade = 0;

var tamanho = 0;
var velocidade = 0;
var alcance = 0;
var hp = 0;

/******************
function calcula_custos(evento, objeto)
--------------------
Calcula os custos de uma nave
******************/	
function calcula_custos(evento, objeto) {
	let custos_div = document.getElementById('custos');
	let chassi_div = document.getElementById('chassi');
	let dados_div = document.getElementById('dados');
	
	let qtd_laser = document.getElementById('qtd_laser').value;
	let qtd_torpedo = document.getElementById('qtd_torpedo').value;
	let qtd_projetil = document.getElementById('qtd_projetil').value;
	let qtd_blindagem = document.getElementById('qtd_blindagem').value;
	let qtd_escudos = document.getElementById('qtd_escudos').value;
	let qtd_impulso = document.getElementById('qtd_impulso').value;
	let qtd_dobra = document.getElementById('qtd_dobra').value;
	let qtd_combustivel = document.getElementById('qtd_combustivel').value;
	let qtd_pesquisa = document.getElementById('qtd_pesquisa');
	
	let mk_laser = document.getElementById('mk_laser').value;
	let mk_torpedo = document.getElementById('mk_torpedo').value;
	let mk_projetil = document.getElementById('mk_projetil').value;
	let mk_blindagem = document.getElementById('mk_blindagem').value;
	let mk_escudos = document.getElementById('mk_escudos').value;
	let mk_impulso = document.getElementById('mk_impulso').value;
	let mk_dobra = document.getElementById('mk_dobra').value;
	
	if (qtd_pesquisa.checked) {
		qtd_pesquisa = qtd_pesquisa.value;
	} else {
		qtd_pesquisa = 0;
	}
	
	chassi = qtd_laser*mk_laser + qtd_torpedo*mk_torpedo + qtd_projetil*mk_projetil + qtd_dobra*mk_dobra + qtd_impulso*mk_impulso + qtd_blindagem*1 + qtd_escudos*1 + qtd_combustivel*1 + qtd_pesquisa*1;
	velocidade = Math.floor((qtd_impulso*mk_impulso/chassi)*10);
	hp = chassi*10;
	
	if (qtd_dobra*mk_dobra*10 < chassi) {
		alcance = 0;
	} else {
		alcance = Math.floor(((qtd_dobra*(mk_dobra*2-1)*10)/chassi)) + Math.ceil(((qtd_combustivel*15)/chassi));
	}

	if (isNaN(velocidade)) {
		velocidade = 0;
		alcance = 0;
	}

	pdf_laser = qtd_laser*(mk_laser*2-1);
	pdf_torpedo = qtd_torpedo*(mk_torpedo*2-1);
	pdf_projetil = qtd_projetil*(mk_projetil*2-1);
	
	blindagem = qtd_blindagem*(mk_blindagem*2-1);
	escudos = qtd_escudos*(mk_escudos*2-1);
	
	let custo_blindagem = Math.ceil(chassi/5)*qtd_blindagem*mk_blindagem;
	let custo_escudos = Math.ceil(chassi/5)*qtd_escudos*mk_escudos;

	industrializaveis = qtd_laser*mk_laser + qtd_torpedo*mk_torpedo + qtd_projetil*mk_projetil + qtd_impulso*mk_impulso + qtd_dobra*mk_dobra + qtd_combustivel*1 + custo_blindagem*1 + custo_escudos*1 + qtd_pesquisa*1 + chassi*1;
	energium = qtd_laser*1 + qtd_torpedo*1 + qtd_combustivel*1 + custo_escudos*1;
	dillithium = qtd_dobra*1;
	duranium = custo_blindagem*1 + qtd_projetil*1;
	
	if (chassi <= 10) {
		categoria = "Corveta";
	} else if (chassi > 10 && chassi <= 20) {
		categoria = "Fragata";
	} else if (chassi > 20 && chassi <= 30) {
		categoria = "Destroier";
	} else if (chassi > 30 && chassi <= 40) {
		categoria = "Warship";
	} else {
		categoria = "????????";
	}
	
	dados_div.innerHTML = "Tamanho: "+chassi+"; Velocidade: "+velocidade+"; Alcance: "+alcance+";<br>" 
	+"PdF Laser: "+pdf_laser+"/ PdF Torpedo: "+pdf_torpedo+"/ PdF Projétil: "+pdf_projetil+"; Blindagem: "+blindagem+"/ Escudos: "+escudos+"; HP: "+hp;
	chassi_div.innerHTML = "Chassi: "+chassi+" - Categoria: "+categoria;
	custos_div.innerHTML = "Industrializáveis: "+industrializaveis+" | Enérgium: "+energium+" | Dillithium: "+dillithium+" | Duranium: "+duranium;
}