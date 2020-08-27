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
	let texto_especiais_div = document.getElementById('texto_especiais');
	let texto_partes_nave_div = document.getElementById('texto_partes_nave');
	
	let qtd_laser = document.getElementById('qtd_laser').value;
	let qtd_torpedo = document.getElementById('qtd_torpedo').value;
	let qtd_projetil = document.getElementById('qtd_projetil').value;
	let qtd_blindagem = document.getElementById('qtd_blindagem').value;
	
	let tritanium = document.getElementById('tritanium');
	let neutronium = document.getElementById('neutronium');
	let tricobalto = document.getElementById('tricobalto');
	
	let qtd_escudos = document.getElementById('qtd_escudos').value;
	let qtd_impulso = document.getElementById('qtd_impulso').value;
	let qtd_dobra = document.getElementById('qtd_dobra').value;
	let qtd_combustivel = document.getElementById('qtd_combustivel').value;
	let qtd_pesquisa = document.getElementById('qtd_pesquisa');
	let qtd_estacao_orbital = document.getElementById('qtd_estacao_orbital').value;
	let qtd_tropas = document.getElementById('qtd_tropas').value;
	let qtd_bombas = document.getElementById('qtd_bombas').value;
	let qtd_slots_extra = document.getElementById('qtd_slots_extra').value;
	let qtd_hp_extra = document.getElementById('qtd_hp_extra').value;
	
	let mk_laser = document.getElementById('mk_laser').value;
	let mk_torpedo = document.getElementById('mk_torpedo').value;
	let mk_projetil = document.getElementById('mk_projetil').value;
	let mk_blindagem = document.getElementById('mk_blindagem').value;
	let mk_escudos = document.getElementById('mk_escudos').value;
	let mk_impulso = document.getElementById('mk_impulso').value;
	let mk_dobra = document.getElementById('mk_dobra').value;
	
	let especiais = 0;
	let custo_estacao_orbital = 0;
	
	
	if (qtd_pesquisa.checked) {
		qtd_pesquisa = qtd_pesquisa.value;
		especiais++;
		texto_especiais_div.innerHTML = "Especiais: (1) - Pode realizar Pesquisas"; 
	} else {
		qtd_pesquisa = 0;
		texto_especiais_div.innerHTML = "Especiais: &nbsp;";
	}
	
	if (qtd_estacao_orbital != 0) {
		let dobra = document.getElementById('qtd_dobra');
		dobra.value = 0;
		
		qtd_dobra = document.getElementById('qtd_dobra').value;
		
		custo_estacao_orbital = 20*qtd_estacao_orbital;
		
		if (especiais == 0) {
			texto_especiais_div.innerHTML = "Especiais: (1) - Produz até "+ qtd_estacao_orbital*10 +" Equipamentos de Naves por turno";
			especiais++;
		} else {
			especiais++;
			texto_especiais_div.innerHTML = texto_especiais_div.innerHTML + "; ("+especiais+") - Produz até "+ qtd_estacao_orbital*10 +" Equipamentos de Naves por turno";
		}
	}
	
	if (qtd_tropas != 0) {
		if (especiais == 0) {
			texto_especiais_div.innerHTML = "Especiais: (1) - Carrega Tropas de Invasão";
			especiais++;
		} else {
			especiais++;
			texto_especiais_div.innerHTML = texto_especiais_div.innerHTML + "; ("+especiais+") - Carrega Tropas de Invasão";
		}
	}

	if (qtd_bombas != 0) {
		if (especiais == 0) {
			texto_especiais_div.innerHTML = "Especiais: (1) - Pode realizar Bombardeamento Planetário";
			especiais++;
		} else {
			especiais++;
			texto_especiais_div.innerHTML = texto_especiais_div.innerHTML + "; ("+especiais+") - Pode realizar Bombardeamento Planetário";
		}
	}
	chassi = qtd_bombas*10 + qtd_tropas*5 + qtd_slots_extra*1 + Math.ceil(custo_estacao_orbital/1.5) + qtd_laser*mk_laser + qtd_torpedo*mk_torpedo + qtd_projetil*mk_projetil + qtd_dobra*1 + qtd_impulso*1 + qtd_blindagem*1 + qtd_escudos*1 + qtd_combustivel*1 + qtd_pesquisa*1;
	
	let impulso = document.getElementById('qtd_impulso');
	if (chassi > 30 && chassi <= 90 && qtd_impulso < 2) {
		impulso.value = 2;
		chassi = chassi - qtd_impulso;
		qtd_impulso = 2;
		chassi = chassi + qtd_impulso;
	} else if (chassi > 90 && qtd_impulso < 3) {
		impulso.value = 3;
		chassi = chassi - qtd_impulso;
		qtd_impulso = 3;
		chassi = chassi + qtd_impulso;
	}


	velocidade = Math.floor((qtd_impulso*mk_impulso/chassi)*10);
	hp = chassi*10+qtd_hp_extra*1;
	
	if (qtd_dobra*mk_dobra*10 < chassi) {
		alcance = 0;
	} else {
		alcance = Math.floor(((qtd_dobra*(mk_dobra*2-1)*10)/(chassi+mk_dobra*1-1))) + Math.ceil(((qtd_combustivel*(mk_dobra*0.5 + 0.5)*15)/(chassi+(mk_dobra*2-2))));
	}
	
	if (isNaN(velocidade)) {
		velocidade = 0;
		alcance = 0;
	}

	if (velocidade > 5) {
		velocidade = 5;
	}	

	if (velocidade == 0) {
		velocidade = 1;
	}

	pdf_laser = qtd_laser*(mk_laser*2-1);
	pdf_torpedo = qtd_torpedo*(mk_torpedo*2-1);
	pdf_projetil = qtd_projetil*(mk_projetil*2-1);
	
	blindagem = qtd_blindagem*(mk_blindagem*2-1);
	escudos = qtd_escudos*(mk_escudos*2-1);
	
	let custo_blindagem = Math.ceil(chassi/5)*qtd_blindagem*mk_blindagem;
	let custo_escudos = Math.ceil(chassi/5)*qtd_escudos*mk_escudos;

	let duranium_blindagem = Math.ceil(chassi/15)*qtd_blindagem;
	if (duranium_blindagem < qtd_blindagem) {
		duranium_blindagem = qtd_blindagem;
	}
	
	let nor_duranium_blindagem = 0;
	if (mk_blindagem > 2) {
		nor_duranium_blindagem = duranium_blindagem;
		duranium_blindagem = 0;
	}

	let tritanium_blindagem = 0;
	if (tritanium.checked) {
		tritanium_blindagem = 1;
		blindagem = blindagem*3;
	}

	let neutronium_blindagem = 0;
	if (neutronium.checked) {
		neutronium_blindagem = 1;
		if (tritanium_blindagem) {
			blindagem = blindagem*5;
		} else {
			blindagem = blindagem*10;
		}
	}
	
	let tricobalto_torpedo = 0;
	if (tricobalto.checked) {
		tricobalto_torpedo = 1;
		pdf_torpedo = pdf_torpedo*3;
	}

	let energium_escudos = Math.ceil(chassi/15)*qtd_escudos;
	if (energium_escudos < qtd_escudos) {
		energium_escudos = qtd_escudos;
	}
	
	industrializaveis = qtd_bombas*10 + custo_estacao_orbital*1 + qtd_laser*mk_laser + qtd_torpedo*mk_torpedo + qtd_projetil*mk_projetil + qtd_impulso*mk_impulso + qtd_dobra*mk_dobra + qtd_combustivel*1 + custo_blindagem*1 + custo_escudos*1 + qtd_pesquisa*1 + chassi*1;
	energium = Math.ceil(custo_estacao_orbital/4) + qtd_tropas*1 + qtd_bombas*5 + qtd_laser*1 + qtd_torpedo*1 + qtd_combustivel*1 + energium_escudos*1;
	dillithium = qtd_dobra*mk_dobra;
	duranium = duranium_blindagem*1 + qtd_projetil*1;
	
	let texto_nor_duranium = "";
	if (nor_duranium_blindagem > 0) {
		texto_nor_duranium = " | Nor-Duranium: "+nor_duranium_blindagem;
	}

	let texto_tritanium= "";
	if (tritanium_blindagem > 0) {
		texto_tritanium = " | Tritanium: "+tritanium_blindagem;
	}

	let texto_neutronium = "";
	if (neutronium_blindagem > 0) {
		texto_neutronium = " | Neutronium: "+neutronium_blindagem;
	}

	let texto_tricobalto = "";
	if (tricobalto_torpedo > 0) {
		texto_tricobalto = " | Tricobalto: "+tricobalto_torpedo;
	}	
	
	if (chassi <= 10) {
		categoria = "Corveta";
	} else if (chassi > 10 && chassi <= 20) {
		categoria = "Fragata";
	} else if (chassi > 20 && chassi <= 50) {
		categoria = "Destroier";
	} else if (chassi > 50 && chassi <= 100) {
		categoria = "Cruzador";
	} else if (chassi > 100 && chassi <= 200) {
		categoria = "Nave de Guerra";
	} else if (chassi > 200 && chassi <= 300) {
		categoria = "Nave de Batalha";
	} else if (chassi > 300 && chassi <= 500) {
		categoria = "Couraçado";
	} else if (chassi > 500 && chassi <= 1000) {
		categoria = "Dreadnought";
	} else if (chassi > 1000 && chassi <= 5000) {
		categoria = "Nave-Mãe";
	} else {
		categoria = "????????";
	}

	if (qtd_estacao_orbital != 0) {
		categoria = "Estação Orbital";
	}
	
	dados_div.innerHTML = "Tamanho: "+chassi+"; Velocidade: "+velocidade+"; Alcance: "+alcance+";<br>" 
	+"PdF Laser: "+pdf_laser+"/ PdF Torpedo: "+pdf_torpedo+"/ PdF Projétil: "+pdf_projetil+"; Blindagem: "+blindagem+"/ Escudos: "+escudos+"; HP: "+hp;
	chassi_div.innerHTML = "Chassi: "+chassi+" - Categoria: "+categoria;
	custos_div.innerHTML = "Industrializáveis: "+industrializaveis+" | Enérgium: "+energium+" | Dillithium: "+dillithium+" | Duranium: "+ duranium + texto_nor_duranium 
	+ texto_tritanium + texto_neutronium + texto_tricobalto;
	
	texto_partes_nave_div.innerHTML = qtd_laser+"="+mk_laser+";"+qtd_torpedo+"="+mk_torpedo+";"+qtd_projetil+"="+mk_projetil+";"
	+qtd_blindagem+"="+mk_blindagem+";"+qtd_escudos+"="+mk_escudos+";"
	+qtd_impulso+"="+mk_impulso+";"+qtd_dobra+"="+mk_dobra+";"+qtd_combustivel+"=1;"
	+qtd_pesquisa+"=1;"+qtd_estacao_orbital+"=1;"+qtd_tropas+"=1;"+qtd_bombas+"=1;"+qtd_slots_extra+"=1;"+qtd_hp_extra+"=1;"
	+tritanium_blindagem+"=1;"+neutronium_blindagem+"=1;"+tricobalto_torpedo+"=1";
	
}

/******************
function processa_string(evento, objeto)
--------------------
Processa a string com os dados da nave
******************/	
function processa_string(evento, objeto) {
	var input_string_construcao = document.getElementById('input_string_construcao').value;

	let qtd_laser = document.getElementById('qtd_laser');
	let qtd_torpedo = document.getElementById('qtd_torpedo');
	let tricobalto = document.getElementById('tricobalto');
	
	let qtd_projetil = document.getElementById('qtd_projetil');
	let qtd_blindagem = document.getElementById('qtd_blindagem');
	let tritanium = document.getElementById('tritanium');
	let neutronium = document.getElementById('neutronium');
	
	let qtd_escudos = document.getElementById('qtd_escudos');
	let qtd_impulso = document.getElementById('qtd_impulso');
	let qtd_dobra = document.getElementById('qtd_dobra');
	let qtd_combustivel = document.getElementById('qtd_combustivel');
	let qtd_pesquisa = document.getElementById('qtd_pesquisa');
	let qtd_estacao_orbital = document.getElementById('qtd_estacao_orbital');
	let qtd_tropas = document.getElementById('qtd_tropas');
	let qtd_bombas = document.getElementById('qtd_bombas');
	let qtd_slots_extra = document.getElementById('qtd_slots_extra');
	let qtd_hp_extra = document.getElementById('qtd_hp_extra');
	
	let mk_laser = document.getElementById('mk_laser');
	let mk_torpedo = document.getElementById('mk_torpedo');
	let mk_projetil = document.getElementById('mk_projetil');
	let mk_blindagem = document.getElementById('mk_blindagem');
	let mk_escudos = document.getElementById('mk_escudos');
	let mk_impulso = document.getElementById('mk_impulso');
	let mk_dobra = document.getElementById('mk_dobra');

	/*
	{
		"laser":{"qtd":0,"mk":1},
		"torpedo":{"qtd":0,"mk":1},
		"projetil":{"qtd":0,"mk":1},
		"blindagem":{"qtd":0,"mk":1},
		"escudos":{"qtd":0,"mk":1}},
		"impulso":{"qtd":0,"mk":1}},
		"dobra":{"qtd":0,"mk":1}},
		"combustivel":{"qtd":0,"mk":1}},
		"pesquisa":{"qtd":0,"mk":1}},
		"estacao_orbital":{"qtd":0,"mk":1}},
		"tropas":{"qtd":0,"mk":1}},
		"bombas":{"qtd":0,"mk":1}},
		"slots":{"qtd":0,"mk":1}}
	}
	/***/
	
	let qtd = [qtd_laser, qtd_torpedo, qtd_projetil, qtd_blindagem, qtd_escudos, qtd_impulso, qtd_dobra, qtd_combustivel, qtd_pesquisa, qtd_estacao_orbital, qtd_tropas, qtd_bombas, qtd_slots_extra, qtd_hp_extra, tritanium, neutronium, tricobalto];
	let mk = [mk_laser, mk_torpedo, mk_projetil, mk_blindagem, mk_escudos, mk_impulso, mk_dobra];

	let partes_nave = input_string_construcao.split(";");
	
	for (let index=0; index<partes_nave.length; index++) {
		let subparte = partes_nave[index].split("=");
		if (qtd[index].type == "checkbox") {
			if (subparte[0] == 1) {
				qtd[index].checked = true;
			} else {
				qtd[index].checked = false;
			}
		} else {
			qtd[index].value = subparte[0];
		}
		
		if (mk[index] !== undefined) {
			mk[index].value = subparte[1];
		}
	}
	
	let calcula = calcula_custos(evento, objeto);
	
	evento.preventDefault();
	return false;
}