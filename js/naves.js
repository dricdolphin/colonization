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
var hp = 0;

/******************
function calcula_custos(evento, objeto)
--------------------
Calcula os custos de uma nave
******************/	
function calcula_custos(evento, objeto, nave={}, exibe_resultados = true) {
	if (Object.keys(nave).length == 0) {
		nave = {
			'qtd_laser' : document.getElementById('qtd_laser').value,
			'qtd_torpedo' : document.getElementById('qtd_torpedo').value,
			'qtd_projetil' : document.getElementById('qtd_projetil').value,
			
			'tritanium_blindagem' : document.getElementById('tritanium').checked,
			'neutronium_blindagem' : document.getElementById('neutronium').checked,
			'tricobalto_torpedo' : document.getElementById('tricobalto').checked,

			'qtd_combustivel' : document.getElementById('qtd_combustivel').value,
			'qtd_pesquisa' : document.getElementById('qtd_pesquisa').checked,
			'qtd_estacao_orbital' : document.getElementById('qtd_estacao_orbital').value,
			'qtd_tropas' : document.getElementById('qtd_tropas').value,
			'qtd_bombas' : document.getElementById('qtd_bombas').value,
			'qtd_slots_extra' : document.getElementById('qtd_slots_extra').value,
			'qtd_hp_extra' : document.getElementById('qtd_hp_extra').value,
			
			'mk_laser' : document.getElementById('mk_laser').value,
			'mk_torpedo' : document.getElementById('mk_torpedo').value,
			'mk_projetil' : document.getElementById('mk_projetil').value,
			'mk_blindagem' : document.getElementById('mk_blindagem').value,
			'mk_escudos' : document.getElementById('mk_escudos').value,
			'mk_impulso' : document.getElementById('mk_impulso').value,
			'mk_dobra' : document.getElementById('mk_dobra').value
		};
	}


	let custos_div = document.getElementById('custos');
	let chassi_div = document.getElementById('chassi');
	let dados_div = document.getElementById('dados');
	let texto_especiais_div = document.getElementById('texto_especiais');
	let texto_partes_nave_div = document.getElementById('texto_partes_nave');
	
	let especiais = 0;
	let custo_estacao_orbital = 0;
	let texto_especiais = "";
	
	if (nave.qtd_pesquisa) {
		texto_especiais = "(1) - Pode realizar Pesquisas";
		qtd_pesquisa = 1;
		especiais++;
	} else {
		qtd_pesquisa = 0;
	}
	
	if (nave.qtd_tropas != 0) {
		if (especiais == 0) {
			texto_especiais = "(1) - Carrega Tropas de Invasão";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Carrega Tropas de Invasão";
			especiais++;
		}
	}

	if (nave.qtd_bombas != 0) {
		if (especiais == 0) {
			texto_especiais = "(1) - Pode realizar Bombardeamento Planetário";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Pode realizar Bombardeamento Planetário";
			especiais++;
		}
	}
	
	chassi = nave.qtd_bombas*10 + nave.qtd_tropas*5 + nave.qtd_slots_extra*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil + qtd_pesquisa*1;
	
	let capacidade_dobra = nave.mk_dobra*5;
	let capacidade_impulso = nave.mk_impulso*5;
	
	let qtd_dobra = 1 + Math.trunc(chassi/capacidade_dobra,0);
	let qtd_impulso = 1 + Math.trunc(chassi/capacidade_impulso,0);
	
	/*************************
		CÁLCULO DO ALCANCE
	**************************/
	alcance = Math.ceil(8.2927*Math.log(nave.mk_dobra*1)+9.9005); //Alcance inicial

	//Cada qtd_combustível adiciona alcance
	let alcance_maximo_por_celula = 7 - qtd_dobra;
	let qtd_celulas_combustivel = nave.qtd_combustivel*1;
	
	if (alcance_maximo_por_celula == 6) {
		//A primeira célula pode valer 6
		
		if (qtd_celulas_combustivel > 0) {
			alcance = alcance + 6;
			qtd_celulas_combustivel = qtd_celulas_combustivel - 1;
			alcance_maximo_por_celula = 5;
		}
	} else if(alcance_maximo_por_celula <= 0) {
		alcance_maximo_por_celula = 1;
	}
	
	let celulas_grupo = 0;
	while (qtd_celulas_combustivel > 0) {
		alcance = alcance + alcance_maximo_por_celula;
		qtd_celulas_combustivel--;
		celulas_grupo++;
		if (celulas_grupo == capacidade_dobra) {
			celulas_grupo = 0;
			alcance_maximo_por_celula--;
			if (alcance_maximo_por_celula < 1) {alcance_maximo_por_celula = 1;}
		}
	} 
	
	//let fator_a = -0.4504*Math.pow(capacidade_dobra,-0.949);
	//let fator_b = (6.303-qtd_dobra);
	
	if (exibe_resultados) {
		document.getElementById('qtd_combustivel').value = nave.qtd_combustivel;
	}

	//alcance = Math.ceil(10 + fator_a*Math.pow(nave.qtd_combustivel,2) + fator_b*nave.qtd_combustivel);
	
	if (nave.qtd_estacao_orbital != 0) {
		custo_estacao_orbital = 20*nave.qtd_estacao_orbital;
		alcance = 0;
		velocidade = 0;
		qtd_dobra = 0;
		qtd_impulso = 0;
		nave.qtd_combustivel = 0;
		if (exibe_resultados) {
			document.getElementById('qtd_combustivel').value = 0;
		}
	
		let categoria_estacao_orbital = "";
		switch(nave.qtd_estacao_orbital*1) {
			case 1:
			categoria_estacao_orbital = "Fragatas";
			break;
			case 2:
			categoria_estacao_orbital = "Destróiers";
			break;
			case 3:
			categoria_estacao_orbital = "Cruzadores";
			break;
			case 4:
			categoria_estacao_orbital = "Naves de Guerra";
			break;
			case 5:
			categoria_estacao_orbital = "Naves de Batalha";
			break;
			case 6:
			categoria_estacao_orbital = "Couraçados";
			break;
			case 7:
			categoria_estacao_orbital = "Dreadnoughts";
			break;			
			case 8:
			categoria_estacao_orbital = "Naves-Mãe";
			break;			
			default:
			categoria_estacao_orbital = "??????";
		}
		
		if (especiais == 0) {
			texto_especiais = "(1) - Permite produzir " + categoria_estacao_orbital;
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Permite produzir " + categoria_estacao_orbital;
			especiais++;
		}
	}
	
	chassi = chassi + qtd_dobra*1 + qtd_impulso*1 + nave.qtd_combustivel*1 + nave.qtd_estacao_orbital*20;
	hp = chassi*10 + nave.qtd_hp_extra*1;
	
	pdf_laser = nave.qtd_laser*(nave.mk_laser*2-1);
	pdf_torpedo = nave.qtd_torpedo*(nave.mk_torpedo*2-1);
	pdf_projetil = nave.qtd_projetil*(nave.mk_projetil*2-1);
	
	let custo_blindagem = (Math.trunc(chassi/10,0)+1)*nave.mk_blindagem;
	let custo_escudos = (Math.trunc(chassi/5,0)+1)*nave.mk_escudos;

	let qtd_blindagem = custo_blindagem;
	let qtd_escudos = custo_escudos;	
	
	blindagem = Math.ceil(Math.pow(nave.mk_blindagem*2-1,1.5));
	escudos = Math.ceil(Math.pow(nave.mk_escudos*2-1,1.5));
	
	if (blindagem < 0 || isNaN(blindagem)) {blindagem = 0;}
	if (escudos < 0 || isNaN(escudos)) {escudos = 0;}
	
	let duranium = qtd_blindagem*1;
	if (duranium < qtd_blindagem) {
		duranium = qtd_blindagem;
	}
	
	let nor_duranium = 0;
	let texto_nor_duranium = "";
	if (nave.mk_blindagem*1 > 2) {
		nor_duranium = duranium;
		duranium = 0;
		texto_nor_duranium = " | Nor-Duranium: "+nor_duranium;
	}

	let tritanium = 0;
	let texto_tritanium= "";
	if (nave.tritanium_blindagem) {
		tritanium = 1*qtd_blindagem;
		blindagem = blindagem*3;
		texto_tritanium = " | Tritanium: "+tritanium;
	}

	let neutronium = 0;
	let texto_neutronium = "";
	if (nave.neutronium_blindagem) {
		neutronium = 1*qtd_blindagem;
		texto_neutronium = " | Neutronium: "+neutronium;
		if (tritanium) {
			blindagem = blindagem*5;
		} else {
			blindagem = blindagem*10;
		}
	}

	let texto_trillithium = "";
	let trillithium = 0;
	if (nave.mk_dobra*1 > 2) {
		trillithium = dillithium;
		texto_trillithium = " | Trillithium: "+ trillithium;
	}
	
	let tricobalto = 0;
	let texto_tricobalto = "";
	if (nave.tricobalto_torpedo) {
		tricobalto = 1*nave.qtd_torpedo;
		pdf_torpedo = pdf_torpedo*3;
		texto_tricobalto = " | Tricobalto: "+tricobalto;
	}

	let energium_escudos = qtd_escudos*1;
	if (energium_escudos < qtd_escudos) {
		energium_escudos = qtd_escudos;
	}

	let corasita = 0;
	let texto_corasita = "";
	if (nave.mk_escudos*1 > 2) {
		corasita = energium_escudos;
		energium_escudos = 0;
		texto_corasita = " | Corasita: "+corasita;
	}	
	
	if (chassi <= 10) {
		categoria = "Corveta";
		velocidade = 4 + nave.mk_impulso*1;
	} else if (chassi > 10 && chassi <= 20) {
		categoria = "Fragata";
		velocidade = 3 + nave.mk_impulso*1;
	} else if (chassi > 20 && chassi <= 50) {
		categoria = "Destroier";
		velocidade = 2 + nave.mk_impulso*1;
	} else if (chassi > 50 && chassi <= 100) {
		categoria = "Cruzador";
		velocidade = 1 + nave.mk_impulso*1;
	} else if (chassi > 100 && chassi <= 200) {
		categoria = "Nave de Guerra";
		velocidade = nave.mk_impulso*1;
	} else if (chassi > 200 && chassi <= 300) {
		categoria = "Nave de Batalha";
		velocidade = 1;
	} else if (chassi > 300 && chassi <= 500) {
		categoria = "Couraçado";
		velocidade = 1;
	} else if (chassi > 500 && chassi <= 1000) {
		categoria = "Dreadnought";
		velocidade = 1;
	} else if (chassi > 1000 && chassi <= 5000) {
		categoria = "Nave-Mãe";
		velocidade = 1;
	} else {
		categoria = "????????";
		velocidade = 1;
	}

	if (nave.qtd_estacao_orbital != 0) {
		categoria = "Estação Orbital";
		velocidade = 0;
	}


	industrializaveis = nave.qtd_bombas*10 + custo_estacao_orbital*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil 
	+ qtd_impulso*nave.mk_impulso + qtd_dobra*nave.mk_dobra + nave.qtd_combustivel*1 + custo_blindagem*1 + custo_escudos*1 + nave.qtd_pesquisa*1 + nave.qtd_slots_extra*1;
	
	energium = Math.ceil(custo_estacao_orbital/4) + nave.qtd_laser*1 + nave.qtd_torpedo*1 + nave.qtd_combustivel*1 + energium_escudos*1 + qtd_impulso*1;
	dillithium = qtd_dobra*nave.mk_dobra;
	duranium = duranium*1 + nave.qtd_projetil*1;
	
	if (exibe_resultados) {
		if (texto_especiais != "") {
			texto_especiais_div.innerHTML = "Especiais: " + texto_especiais;
		}
		
		dados_div.innerHTML = "Tamanho: "+chassi+"; Velocidade: "+velocidade+"; Alcance: "+alcance+";<br>" 
		+"pdf Laser: "+pdf_laser+"/ pdf Torpedo: "+pdf_torpedo+"/ pdf Projétil: "+pdf_projetil+"; Blindagem: "+blindagem+"/ Escudos: "+escudos+"; HP: "+hp;
		chassi_div.innerHTML = "Chassi: "+chassi+" - Categoria: "+categoria;
		custos_div.innerHTML = "Industrializáveis: "+industrializaveis+" | Enérgium: "+energium+" | Dillithium: "+dillithium+" | Duranium: "+ duranium + texto_nor_duranium + texto_trillithium 
		+ texto_tritanium + texto_neutronium + texto_tricobalto;
	
		/***
		texto_partes_nave_div.innerHTML = nave.qtd_laser+"="+nave.mk_laser+";"+nave.qtd_torpedo+"="+nave.mk_torpedo+";"+nave.qtd_projetil+"="+nave.mk_projetil+";"
		+qtd_blindagem+"="+nave.mk_blindagem+";"+qtd_escudos+"="+nave.mk_escudos+";"
		+qtd_impulso+"="+nave.mk_impulso+";"+qtd_dobra+"="+nave.mk_dobra+";"+nave.qtd_combustivel+"=1;"
		+qtd_pesquisa+"=1;"+nave.qtd_estacao_orbital+"=1;"+nave.qtd_tropas+"=1;"+nave.qtd_bombas+"=1;"+nave.qtd_slots_extra+"=1;"+nave.qtd_hp_extra+"=1;"
		+tritanium+"=1;"+neutronium+"=1;"+tricobalto+"=1";
		***/
		
		texto_partes_nave_div.innerHTML = JSON.stringify(nave);
	}
	
	let dados_nave = {
		'tamanho' : chassi,
		'velocidade' : velocidade,
		'alcance': alcance,
		'pdf_laser' : pdf_laser,
		'pdf_torpedo' : pdf_torpedo,
		'pdf_projetil' : pdf_projetil,
		'blindagem' : blindagem,
		'escudos' : escudos,
		'hp' : hp,
		'categoria' : categoria,
		'especiais' : texto_especiais,
		'pesquisa' : nave.qtd_pesquisa,
		'nivel_estacao_orbital' : nave.qtd_estacao_orbital,
		'qtd_bombas' : nave.qtd_bombas,
		'qtd_tropas': nave.qtd_tropas
	}

	let custos = {
		'industrializaveis': industrializaveis,
		'energium': energium,
		'Dillithium': dillithium,
		'Duranium': duranium,
		'nor_duranium': nor_duranium,
		'Trillithium': trillithium,
		'Corasita': corasita,
		'Tritanium': tritanium,
		'Neutronium': neutronium,
		'Tricobalto': tricobalto
	};
	
	//console.log(custos);
	
	let custos_calculados = {
		'dados_nave' : dados_nave,
		'custo' : custos
	};
	
	return custos_calculados;
}

/******************
function processa_string(evento, objeto)
--------------------
Processa a string com os dados da nave
******************/	
function processa_string(evento, objeto) {
	let input_string_nave = document.getElementById('input_string_construcao').value;
	
	try {
		var nave = JSON.parse(input_string_nave);
	} catch (err) {
		alert('Erro! JSON inválido!');
		
		evento.preventDefault();
		return false;
	}
	/***
		let nave = {
		"qtd_laser":"0",
		"qtd_torpedo":"0",
		"qtd_projetil":"0",
		"tritanium_blindagem":false,
		"neutronium_blindagem":false,
		"tricobalto_torpedo":false,
		"qtd_combustivel":"0",
		"qtd_pesquisa":false,
		"qtd_estacao_orbital":"0",
		"qtd_tropas":"0",
		"qtd_bombas":"0",
		"qtd_slots_extra":"0",
		"qtd_hp_extra":"0",
		"mk_laser":"1",
		"mk_torpedo":"1",
		"mk_projetil":"1",
		"mk_blindagem":"0",
		"mk_escudos":"0",
		"mk_impulso":"1",
		"mk_dobra":"1"
		};
	***/
	
	let nave_elementos = {
		'qtd_laser' : document.getElementById('qtd_laser'),
		'qtd_torpedo' : document.getElementById('qtd_torpedo'),
		'qtd_projetil' : document.getElementById('qtd_projetil'),
		'tritanium_blindagem' : document.getElementById('tritanium'),
		'neutronium_blindagem' : document.getElementById('neutronium'),
		'tricobalto_torpedo' : document.getElementById('tricobalto'),
		'qtd_combustivel' : document.getElementById('qtd_combustivel'),	
		'qtd_pesquisa' : document.getElementById('qtd_pesquisa'),
		'qtd_estacao_orbital' : document.getElementById('qtd_estacao_orbital'),
		'qtd_tropas' : document.getElementById('qtd_tropas'),
		'qtd_bombas' : document.getElementById('qtd_bombas'),
		'qtd_slots_extra' : document.getElementById('qtd_slots_extra'),
		'qtd_hp_extra' : document.getElementById('qtd_hp_extra'),
		'mk_laser' : document.getElementById('mk_laser'),
		'mk_torpedo' : document.getElementById('mk_torpedo'),
		'mk_projetil' : document.getElementById('mk_projetil'),
		'mk_blindagem' : document.getElementById('mk_blindagem'),
		'mk_escudos' : document.getElementById('mk_escudos'),
		'mk_impulso' : document.getElementById('mk_impulso'),
		'mk_dobra' : document.getElementById('mk_dobra')
	}

	
	for (const property in nave_elementos) {
		if (typeof(nave[property]) == "boolean") {
			nave_elementos[property].checked = nave[property];
		} else {
			nave_elementos[property].value = nave[property];
		}
	}
	
	/*** CÓDIGO ANTIGO ANTES DO JSON ***
	let qtd = [qtd_laser, qtd_torpedo, qtd_projetil, qtd_blindagem, qtd_escudos, qtd_impulso, qtd_dobra, qtd_combustivel, qtd_pesquisa, qtd_estacao_orbital, qtd_tropas, qtd_bombas, qtd_slots_extra, qtd_hp_extra, tritanium, neutronium, tricobalto];
	let mk = [mk_laser, mk_torpedo, mk_projetil, mk_blindagem, mk_escudos, mk_impulso, mk_dobra];

	let partes_nave = input_string_construcao.split(";");
	
	for (let index=0; index<partes_nave.length; index++) {
		let subparte = partes_nave[index].split("=");
		if (qtd[index] != undefined) {
			if (qtd[index].type == "checkbox") {
				if (subparte[0] > 0) {
					qtd[index].checked = true;
				} else {
					qtd[index].checked = false;
				}
			} else {
				qtd[index].value = subparte[0];
			}
		}
		
		if (mk[index] !== undefined) {
			mk[index].value = subparte[1];
		}
	}
	/**************************************/
	
	let calcula = calcula_custos(evento, objeto, nave);
	
	evento.preventDefault();
	return false;
}

/******************
function processa_string_admin
--------------------
Processa uma string de nave na área de Frotas (menu_admin)
******************/
function processa_string_admin (evento, objeto) {
	let confirma = confirm("Ao processar a String TODOS os dados serão perdidos. Deseja continuar?");
	
	if (!confirma) {
		evento.preventDefault();
		return false;
	}
	
	let input_string_nave = document.getElementById('string_nave').value;
	try {
		var nave = JSON.parse(input_string_nave);
	} catch (err) {
		alert('Erro! JSON inválido!');
		
		evento.preventDefault();
		return false;
	}
	
	let calcula_nave = calcula_custos(evento, objeto, nave, false);
	
	/**********
	let dados_nave = {
		'chassi' : chassi,
		'velocidade' : velocidade,
		'alcance': alcance,
		'pdf_laser' : pdf_laser,
		'pdf_torpedo' : pdf_torpedo,
		'pdf_projetil' : pdf_projetil,
		'blindagem' : blindagem,
		'escudos' : escudos,
		'hp' : hp,
		'categoria' : categoria,
		'especiais' : texto_especiais
		'pesquisa' : nave.qtd_pesquisa,
		'nivel_estacao_orbital' : nave.qtd_estacao_orbital,
		'qtd_bombas' : nave.qtd_bombas,
		'qtd_tropas': nave.qtd_tropas		
	}
	*********/

	let nave_elementos = {
		'tamanho' : document.getElementById('tamanho'),
		'velocidade' : document.getElementById('velocidade'),
		'alcance' : document.getElementById('alcance'),
		'pdf_laser' : document.getElementById('pdf_laser'),
		'pdf_torpedo' : document.getElementById('pdf_torpedo'),
		'pdf_projetil' : document.getElementById('pdf_projetil'),
		'blindagem' : document.getElementById('blindagem'),
		'escudos' : document.getElementById('escudos'),
		'hp' : document.getElementById('hp'),
		'categoria' : document.getElementById('categoria'),
		'especiais' : document.getElementById('especiais'),
		'pesquisa' : document.getElementById('pesquisa'),
		'nivel_estacao_orbital' : document.getElementById('nivel_estacao_orbital'),
		'qtd_bombas' : document.getElementById('qtd_bombas'),
		'qtd_tropas' : document.getElementById('qtd_tropas'),
		'custo' : document.getElementById('custo')
	};

	for (const property in calcula_nave.dados_nave) {
		if (typeof(calcula_nave.dados_nave[property]) == "boolean") {
			nave_elementos[property].checked = calcula_nave.dados_nave[property];
		} else {
			nave_elementos[property].value = calcula_nave.dados_nave[property];
		}
	}
	
	nave_elementos.custo.value = JSON.stringify(calcula_nave.custo);
	
	evento.preventDefault();
	return false;
}