var industrializaveis = 0;
var energium = 0;
var dillithium = 0;
var duranium = 0;

var chassi = 0;
var categoria = "Corveta";

var pdf_laser = 0;
var pdf_torpedo = 0;
var pdf_projetil = 0;
var pdf_bombardeamento = 0;
var blindagem = 0;
var escudos = 0;
var alcance = 0;
var velocidade = 0;
var tamanho = 0;
var hp = 0;

var nave_template = {
	'qtd_laser' : 0,
	'qtd_torpedo' : 0,
	'qtd_projetil' :0,
	'qtd_plasma' :0,
	'mk_laser' : 0,
	'mk_torpedo' : 0,
	'mk_plasma' : 0,
	'tricobalto_torpedo' : 0,
	'mk_projetil' : 0,
	'mk_blindagem' : 0,
	'tritanium_blindagem' : 0,
	'neutronium_blindagem' : 0,
	'mk_escudos' : 0,
	'mk_impulso' : 0,
	'mk_dobra' : 0,
	'qtd_combustivel' : 0,
	'qtd_pesquisa' : 0,
	'nivel_estacao_orbital' : 0,
	'qtd_tropas' : 0,
	'qtd_baia_de_torpedeiros' : 0,
	'qtd_bombardeamento' : 0,
	'qtd_slots_extra' : 0,
	'qtd_hp_extra' : 0,
	'mk_bombardeamento' : 0,
	'mk_camuflagem' : 0
};

var descricao_parte = [];

/******************
function calcula_custos(evento, objeto)
--------------------
Calcula os custos de uma nave
******************/	
function calcula_custos(evento, objeto, nave={}, exibe_resultados = true) {
	if (objeto.type == "number") {
		if (objeto.max != "") {
			if (objeto.value*1 > objeto.max*1) {
				objeto.value = objeto.max;
			}
		}
		if (objeto.min != "") {
			if (objeto.value*1 < objeto.min*1) {
				objeto.value = objeto.min;
			}
		}		
		if (objeto.value*1 < 0) {
			objeto.value = 0;
		}
	}
	
	if (Object.keys(nave).length == 0) {//Se não tem uma nave em JSON, então pega os dados da nave do Formulário
		let partes_nave = document.getElementById("simulador_nave").getElementsByTagName("INPUT");
		let objeto_nave = {};
		let input_string_construcao = document.getElementById("input_string_construcao");
		if (input_string_construcao !== undefined) {
			for(let index = 0; index < partes_nave.length; index++) {
				if (partes_nave[index].id != "input_string_construcao") {
					objeto_nave[partes_nave[index].id] = 0;
				}
			}
			nave_template = objeto_nave;
		}
		
		for (const property in nave_template) {
			if (document.getElementById(property).type == "checkbox") {
				nave[property] = document.getElementById(property).checked;
			} else {
				nave[property] = document.getElementById(property).value;
			}
		}
	}

	let custos_div = document.getElementById('custos');
	let chassi_div = document.getElementById('chassi');
	let dados_div = document.getElementById('dados');
	let texto_especiais_div = document.getElementById('texto_especiais');
	let texto_partes_nave_div = document.getElementById('texto_partes_nave');
	
	let especiais = 1;
	let custo_estacao_orbital = 0;
	let texto_especiais = "";

	if (nave.nivel_estacao_orbital != 0) {
		custo_estacao_orbital = 20*nave.nivel_estacao_orbital;
		
		if (exibe_resultados) {//Estações Orbitais só podem ter Armas, Blindagens e Escudos. O resto tem que ser 0.
			let div_especiais = document.getElementById('especiais');
			let inputs_div_especiais = div_especiais.getElementsByTagName("INPUT");
			for (let index=0; index < inputs_div_especiais.length; index++) {
				if (inputs_div_especiais[index].id != "nivel_estacao_orbital" && inputs_div_especiais[index].getAttribute("data-descricao") == undefined) {
					if (inputs_div_especiais[index].type == "checkbox") {
						inputs_div_especiais[index].checked = false;
					} else if(inputs_div_especiais[index].id .search("mk_") === -1) {
						inputs_div_especiais[index].value = 0;
					}
				}
			}
			
			let mk_impulso = document.getElementById('mk_impulso');
			let mk_dobra = document.getElementById('mk_dobra');
			mk_dobra.value = 1;
			mk_impulso.value = 1;
		}
		
		//Recalcula os valores da Nave
		if (exibe_resultados) {
			for (const property in nave_template) {
				if (document.getElementById(property).type == "checkbox") {
					nave[property] = document.getElementById(property).checked;
				} else {
					nave[property] = document.getElementById(property).value;
				}
			}
		}
	
		let categoria_estacao_orbital = "";
		switch(nave.nivel_estacao_orbital*1) {
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
		
		if (especiais == 1) {
			texto_especiais = "(1) - Permite produzir " + categoria_estacao_orbital;
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Permite produzir " + categoria_estacao_orbital;
			especiais++;
		}
	}	

	if (nave.qtd_pesquisa) {
		texto_especiais = "(1) - Pode realizar Pesquisas";
		qtd_pesquisa = 1;
		especiais++;
	} else {
		qtd_pesquisa = 0;
	}
	
	if (nave.qtd_tropas != 0) {
		if (especiais == 1) {
			texto_especiais = "(1) - Carrega "+nave.qtd_tropas+" Tropas de Invasão";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Carrega "+nave.qtd_tropas+" Tropas de Invasão";
			especiais++;
		}
	}
	
	if (nave.qtd_baia_de_torpedeiros != 0) {
		if (especiais == 1) {
			texto_especiais = "(1) - Carrega "+nave.qtd_baia_de_torpedeiros+" Baias de Torpedeiros";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Carrega "+nave.qtd_baia_de_torpedeiros+" Baias de Torpedeiros";
			especiais++;
		}
	}	
	
	chassi = nave.qtd_bombardeamento*10 + nave.qtd_tropas*7 + nave.qtd_baia_de_torpedeiros*1 + nave.qtd_slots_extra*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil + nave.qtd_plasma*nave.mk_plasma + qtd_pesquisa*1;
	
	let capacidade_dobra = nave.mk_dobra*5;
	let capacidade_impulso = nave.mk_impulso*5;
	
	let qtd_dobra = 1 + Math.trunc(chassi/capacidade_dobra,0);
	let qtd_impulso = 1 + Math.trunc(chassi/capacidade_impulso,0);
	
	if (nave.nivel_estacao_orbital != 0) {
		alcance = 0;
		velocidade = 0;
		qtd_dobra = 0;
		qtd_impulso = 0;
		nave.qtd_combustivel = 0;		
	}
	
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
	let chassi_especial = 0;
	for (let property in nave) {
		if (descricao_parte[property] !== undefined && nave[property]) {
			chassi_especial++;
			if (especiais == 1) {
				texto_especiais = "(1) - " + descricao_parte[property];
				especiais++;
			} else {
				texto_especiais = texto_especiais + "; ("+especiais+") - " + descricao_parte[property];
				especiais++;
			}
		}
	}
	
	chassi = chassi + qtd_dobra*1 + qtd_impulso*1 + nave.qtd_combustivel*1 + nave.nivel_estacao_orbital*20 + chassi_especial*1;
	hp = chassi*10 + nave.qtd_hp_extra*1;
	
	pdf_laser = nave.qtd_laser*(nave.mk_laser*2-1) + nave.qtd_plasma*(nave.mk_plasma*2-1);
	pdf_torpedo = nave.qtd_torpedo*(nave.mk_torpedo*2-1);
	pdf_projetil = nave.qtd_projetil*(nave.mk_projetil*2-1);
	pdf_bombardeamento = nave.qtd_bombardeamento*(nave.mk_bombardeamento*2-1);

	if (nave.qtd_bombardeamento != 0) {
		if (especiais == 1) {
			texto_especiais = "(1) - Pode realizar Bombardeamento Planetário (PdF "+pdf_bombardeamento+")";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Pode realizar Bombardeamento Planetário (PdF "+pdf_bombardeamento+")";
			especiais++;
		}
	}
	
	if (nave.qtd_plasma*nave.mk_plasma > 0) {
		if (especiais == 1) {
			texto_especiais = "(1) - Armas de Plasma";
			especiais++;
		} else {
			texto_especiais = texto_especiais + "; ("+especiais+") - Armas de Plasma";
			especiais++;
		}
	}
	
	let custo_blindagem = (Math.trunc(chassi/10,0)+1)*nave.mk_blindagem;
	let custo_escudos = (Math.trunc(chassi/5,0)+1)*nave.mk_escudos;

	let qtd_blindagem = custo_blindagem;
	let qtd_escudos = custo_escudos;
	
	if (chassi > 50) {//Corrige o consumo de recursos para Blindagem e Escudos para naves acima de 50 slots
		qtd_blindagem = (Math.trunc(50/10,0)+1)*nave.mk_blindagem + Math.trunc(Math.pow(chassi-50,(1/3)))*nave.mk_blindagem;
		qtd_escudos = (Math.trunc(50/5,0)+1)*nave.mk_escudos + Math.trunc(Math.pow(chassi-50,(1/3)))*nave.mk_escudos;
	}
	
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
		if (chassi > 50) {//Corrige o consumo de recursos para Blindagem e Escudos para naves acima de 50 slots
			tritanium = (Math.trunc(50/10,0)+1)*nave.mk_blindagem + Math.trunc(Math.pow(chassi-50,(1/3)))*nave.mk_blindagem;
		}
		blindagem = blindagem*3;
		texto_tritanium = " | Tritanium: "+tritanium;
	}

	let neutronium = 0;
	let texto_neutronium = "";
	if (nave.neutronium_blindagem) {
		neutronium = 1*qtd_blindagem;
		if (chassi > 50) {//Corrige o consumo de recursos para Blindagem e Escudos para naves acima de 50 slots
			neutronium = (Math.trunc(50/10,0)+1)*nave.mk_blindagem + Math.trunc(Math.pow(chassi-50,(1/3)))*nave.mk_blindagem;
		}
		texto_neutronium = " | Neutronium: "+neutronium;
		if (tritanium) {
			blindagem = blindagem*5;
		} else {
			blindagem = blindagem*10;
		}
	}

	
	let tricobalto = 0;
	let corasita = 0;
	let aureum = 0;
	let capirotum = 0;
	let trillithium = 0;
	let protomateria = 0;
	let texto_tricobalto = "";
	let texto_corasita = "";
	let texto_aureum = "";
	let texto_capirotum = "";
	let texto_trillithium = "";
	let texto_protomateria = "";
	
	let energium_escudos = qtd_escudos*1;
	if (energium_escudos < qtd_escudos) {
		energium_escudos = qtd_escudos;
	}
	
	if (nave.mk_escudos*1 > 2) {
		corasita = energium_escudos;
		if (chassi > 50) {//Corrige o consumo de recursos para Blindagem e Escudos para naves acima de 50 slots
			corasita = (Math.trunc(50/5,0)+1)*nave.mk_escudos + Math.trunc(Math.pow((chassi-50)/2,(1/3)))*nave.mk_escudos;
		}		
		energium_escudos = 0;
		texto_corasita = " | Corasita: "+corasita;
	}
	
	if (nave.mk_escudos*1 > 4) {
		texto_corasita = " | Corasita: "+corasita;
		aureum = Math.trunc(corasita/3);
		if (aureum < 1) {
			aureum = 1;
		}
		texto_aureum = " | Aureum: "+aureum;
	}
	
	if (nave.mk_camuflagem*1 > 3) {
		corasita = corasita + nave.mk_camuflagem*1;
		texto_corasita = " | Corasita: "+corasita;
	};

	if (nave.mk_camuflagem*1 > 4) {
		if (nave.mk_escudos*1 > 4) {
			aureum = aureum + nave.mk_camuflagem*1 - 4;
			texto_aureum = " | Aureum: "+aureum;
		} else {
			alert('Para instalar Camuflagem Mk V ou melhor é necessário ter Escudos Mk V instalados na nave.');
			if (objeto.type == "number") {
				objeto.value = 4;
			}
			nave.mk_camuflagem = 4;
		}
	};
	
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

	if (nave.nivel_estacao_orbital != 0) {
		categoria = "Estação Orbital";
		velocidade = 0;
		alcance = 0;
	}

	industrializaveis = nave.qtd_bombardeamento*nave.mk_bombardeamento*10 + custo_estacao_orbital*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil + nave.qtd_plasma*nave.mk_plasma
	+ qtd_impulso*nave.mk_impulso + qtd_dobra*nave.mk_dobra + nave.qtd_combustivel*1 + custo_blindagem*1 + custo_escudos*1 + nave.qtd_pesquisa*1 + nave.qtd_slots_extra*1 + nave.qtd_tropas*1  + nave.mk_camuflagem*1 + nave.qtd_baia_de_torpedeiros*1;
	
	energium = Math.ceil(custo_estacao_orbital/4) + nave.qtd_laser*1 + nave.qtd_torpedo*1 + nave.qtd_plasma*1 + nave.qtd_combustivel*1 + energium_escudos*1 + qtd_impulso*1 + nave.qtd_baia_de_torpedeiros*1;
	dillithium = qtd_dobra*nave.mk_dobra;
	duranium = duranium*1 + nave.qtd_projetil*1;

	if (nave.mk_dobra*1 > 2) {
		trillithium = trillithium + dillithium;
		texto_trillithium = " | Trillithium: "+ trillithium;
	}	
	
	if (nave.tricobalto_torpedo) {
		tricobalto = tricobalto+ 1*nave.qtd_torpedo;
		pdf_torpedo = pdf_torpedo*3;
		texto_tricobalto = " | Tricobalto: "+tricobalto;
	}
	
	if (nave.hasOwnProperty('torpedos_subespaciais')) {
		if (nave.torpedos_subespaciais) {
			aureum = aureum + 1*nave.qtd_torpedo;
			texto_aureum = " | Aureum: "+aureum;
		}	
	}

	if (nave.hasOwnProperty('capacitores_capirotum')) {
		if (nave.capacitores_capirotum) {
			capirotum = nave.qtd_laser*1;
			texto_capirotum = " | Capirotum: "+capirotum;
			pdf_laser = pdf_laser*10;
		}
	}
	

	if (nave.hasOwnProperty('enxame_abordagem')) {
		if (nave.enxame_abordagem) {
			trillithium = trillithium + (Math.trunc(chassi*1/5,0)+1);
			texto_trillithium = " | Trillithium: "+trillithium;
		}
	}

	if (nave.hasOwnProperty('bomba_implosao_estelar')) {
		if (nave.bomba_implosao_estelar) {
			protomateria = protomateria + 1;
			industrializaveis = industrializaveis + 1000;
			aureum = aureum + 1;
			corasita = corasita + 1;
			texto_aureum = " | Aureum: "+aureum;
			texto_corasita = " | Corasita: "+corasita;
			texto_protomateria = " | Protomatéria: "+trillithium;
		}
	}
	
	if (nave.hasOwnProperty('drones_de_defesa')) {
		if (nave.drones_de_defesa) {
			industrializaveis = industrializaveis + chassi*1;
		}
	}

	let dados_nave = {
		'tamanho' : chassi,
		'velocidade' : velocidade,
		'alcance': alcance,
		'pdf_laser' : pdf_laser,
		'pdf_torpedo' : pdf_torpedo,
		'pdf_projetil' : pdf_projetil,
		'pdf_bombardeamento' : pdf_bombardeamento,
		'blindagem' : blindagem,
		'escudos' : escudos,
		'hp' : hp,
		'categoria' : categoria,
		'especiais' : texto_especiais,
		'pesquisa' : nave.qtd_pesquisa,
		'nivel_estacao_orbital' : nave.nivel_estacao_orbital,
		'qtd_tropas': nave.qtd_tropas,
		'qtd_baia_de_torpedeiros': nave.qtd_baia_de_torpedeiros,
		'mk_camuflagem' : nave.mk_camuflagem
	}

	let custos = {
		'Industrializáveis': industrializaveis*1,
		'Enérgium': energium*1,
		'Dillithium': dillithium*1,
		'Duranium': duranium*1,
		'Nor-Duranium': nor_duranium*1,
		'Trillithium': trillithium*1,
		'Corasita': corasita*1,
		'Tritanium': tritanium*1,
		'Neutronium': neutronium*1,
		'Tricobalto': tricobalto*1,
		'Aureum': aureum*1,
		'Capirotum': capirotum*1,
		'Protomatéria': protomateria*1
	};

	//Remove os itens em branco ou zerados
	for (let property in nave) {
		if (nave[property] == 0) {
			if (property.search("qtd_") !== -1) {
				let mk_property = "mk_" + property.substr(property.search("qtd_")+4);
				
				if (typeof nave[mk_property] !== "undefined") {
					delete nave[mk_property];
				}
			}
			delete nave[property];
		}
	}

	for (let property in custos) {
		if (custos[property] == 0) {
			delete custos[property];
		}
	}	

	if (exibe_resultados) {
		texto_especiais_div.innerHTML = "Especiais: " + texto_especiais;
		
		dados_div.innerHTML = "Tamanho: "+chassi+"; Velocidade: "+velocidade+"; Alcance: "+alcance+";<br>" 
		+"pdf Laser: "+pdf_laser+"; pdf Torpedo: "+pdf_torpedo+"; pdf Projétil: "+pdf_projetil+"; Blindagem: "+blindagem+"; Escudos: "+escudos+"; HP: "+hp;
		chassi_div.innerHTML = "Chassi: "+chassi+" - Categoria: "+categoria;
		custos_div.innerHTML = "Industrializáveis: "+industrializaveis+" | Enérgium: "+energium+" | Dillithium: "+dillithium+" | Duranium: "+ duranium 
		+ texto_nor_duranium + texto_corasita + texto_trillithium + texto_tritanium + texto_neutronium + texto_tricobalto + texto_aureum + texto_capirotum + texto_protomateria;
	
		/***
		texto_partes_nave_div.innerHTML = nave.qtd_laser+"="+nave.mk_laser+";"+nave.qtd_torpedo+"="+nave.mk_torpedo+";"+nave.qtd_projetil+"="+nave.mk_projetil+";"
		+qtd_blindagem+"="+nave.mk_blindagem+";"+qtd_escudos+"="+nave.mk_escudos+";"
		+qtd_impulso+"="+nave.mk_impulso+";"+qtd_dobra+"="+nave.mk_dobra+";"+nave.qtd_combustivel+"=1;"
		+qtd_pesquisa+"=1;"+nave.nivel_estacao_orbital+"=1;"+nave.qtd_tropas+"=1;"+nave.qtd_bombardeamento+"=1;"+nave.qtd_slots_extra+"=1;"+nave.qtd_hp_extra+"=1;"
		+tritanium+"=1;"+neutronium+"=1;"+tricobalto+"=1";
		***/
		
		texto_partes_nave_div.innerHTML = JSON.stringify(nave);
	}

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
	let partes_nave = document.getElementById("simulador_nave").getElementsByTagName("INPUT");
	let objeto_nave = {};
	let input_string_construcao = document.getElementById("input_string_construcao");
	console.log("Processando a string...");
	if (input_string_construcao !== undefined) {
		for(let index = 0; index < partes_nave.length; index++) {
			if (partes_nave[index].id != "input_string_construcao") {
				objeto_nave[partes_nave[index].id] = 0;
				if (partes_nave[index].type == "checkbox") {
					partes_nave[index].checked = false;
				} else if (partes_nave[index].type == "number") {
					if (partes_nave[index].max != "") {
						if (partes_nave[index].value*1 > partes_nave[index].max*1) {
							partes_nave[index].value = partes_nave[index].max;
						}
					}
					if (partes_nave[index].min != "") {
						if (partes_nave[index].value*1 < partes_nave[index].min*1) {
							partes_nave[index].value = partes_nave[index].min;
						}
					}		
					if (partes_nave[index].value*1 < 0) {
						partes_nave[index].value = 0;
					}
				}
			}
		}
		nave_template = objeto_nave;
	}

	try {
		var nave = JSON.parse(input_string_construcao.value);
	} catch (err) {
		alert('Erro! JSON inválido!');
		
		evento.preventDefault();
		return false;
	}
	
	let nave_elementos = {};
	for (const property in nave_template) {
		nave_elementos[property] = document.getElementById(property);
	}

		/***
		'qtd_laser' : document.getElementById('qtd_laser'),
		'qtd_torpedo' : document.getElementById('qtd_torpedo'),
		'qtd_projetil' : document.getElementById('qtd_projetil'),
		'tritanium_blindagem' : document.getElementById('tritanium_blindagem'),
		'neutronium_blindagem' : document.getElementById('neutronium_blindagem'),
		'tricobalto_torpedo' : document.getElementById('tricobalto_torpedo'),
		'qtd_combustivel' : document.getElementById('qtd_combustivel'),	
		'qtd_pesquisa' : document.getElementById('qtd_pesquisa'),
		'nivel_estacao_orbital' : document.getElementById('nivel_estacao_orbital'),
		'qtd_tropas' : document.getElementById('qtd_tropas'),
		'qtd_bombardeamento' : document.getElementById('qtd_bombardeamento'),
		'qtd_slots_extra' : document.getElementById('qtd_slots_extra'),
		'qtd_hp_extra' : document.getElementById('qtd_hp_extra'),
		'mk_laser' : document.getElementById('mk_laser'),
		'mk_torpedo' : document.getElementById('mk_torpedo'),
		'mk_projetil' : document.getElementById('mk_projetil'),
		'mk_blindagem' : document.getElementById('mk_blindagem'),
		'mk_escudos' : document.getElementById('mk_escudos'),
		'mk_impulso' : document.getElementById('mk_impulso'),
		'mk_dobra' : document.getElementById('mk_dobra') 
		/***/
	
	for (const property in nave_elementos) {
		if (typeof(nave[property]) === "undefined") {
			nave[property] = 0;
			if (property.search("qtd_laser") !== -1) {
				nave["mk_laser"] = 1;
			} else if (property.search("qtd_torpedo") !== -1) {
				nave["mk_torpedo"] = 1;
			} else if (property.search("qtd_projetil") !== -1) {
				nave["mk_projetil"] = 1;
			} else if (property.search("qtd_plasma") !== -1) {
				nave["mk_plasma"] = 1;
			} else if (property == "id" || property == "nome_modelo") {
				nave[property] = "";
			}
		} 
		
		if (nave_elementos[property].type == "checkbox") {
			nave_elementos[property].checked = nave[property];
		} else {
			nave_elementos[property].value = nave[property];
			if (nave_elementos[property].type == "number") {
				if (nave_elementos[property].max != "") {
					if (nave_elementos[property].value*1 > nave_elementos[property].max*1) {
						nave_elementos[property].value = nave_elementos[property].max;
					}
				}
				if (nave_elementos[property].min != "") {
					if (nave_elementos[property].value*1 < nave_elementos[property].min*1) {
						nave_elementos[property].value = nave_elementos[property].min;
					}
				}		
				if (nave_elementos[property].value*1 < 0) {
					nave_elementos[property].value = 0;
				}			
			}
		}		
	}
	
	/*** CÓDIGO ANTIGO ANTES DO JSON ***
	let qtd = [qtd_laser, qtd_torpedo, qtd_projetil, qtd_blindagem, qtd_escudos, qtd_impulso, qtd_dobra, qtd_combustivel, qtd_pesquisa, nivel_estacao_orbital, qtd_tropas, qtd_bombardeamento, qtd_slots_extra, qtd_hp_extra, tritanium, neutronium, tricobalto];
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
function processa_string_admin (evento, objeto, jogador=false) {
	if (!jogador) {
		let confirma = confirm("Ao processar a String TODOS os dados serão perdidos. Deseja continuar?");
		
		if (!confirma) {
			evento.preventDefault();
			return false;
		}
	}
	
	let input_string_nave = document.getElementById('string_nave').value;
	try {
		var nave = JSON.parse(input_string_nave);
	} catch (err) {
		alert('Erro! JSON inválido!');
		
		evento.preventDefault();
		return false;
	}
	
	let nave_elementos = nave_template;
	
	for (const property in nave_elementos) {
		if (typeof nave[property] === "undefined") {
			nave[property] = 0;
			if (property.search("qtd_laser") !== -1) {
				nave["mk_laser"] = 1;
			} else if (property.search("qtd_torpedo") !== -1) {
				nave["mk_torpedo"] = 1;
			} else if (property.search("qtd_projetil") !== -1) {
				nave["mk_projetil"] = 1;
			}
		}
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
		'pdf_bombardeamento' : pdf_bombardeamento,
		'blindagem' : blindagem,
		'escudos' : escudos,
		'hp' : hp,
		'categoria' : categoria,
		'especiais' : texto_especiais
		'pesquisa' : nave.qtd_pesquisa,
		'nivel_estacao_orbital' : nave.nivel_estacao_orbital,
		'qtd_bombardeamento' : nave.qtd_bombardeamento,
		'qtd_tropas': nave.qtd_tropas		
	}
	*********/

	nave_elementos = {
		'tamanho' : document.getElementById('tamanho'),
		'velocidade' : document.getElementById('velocidade'),
		'alcance' : document.getElementById('alcance'),
		'pdf_laser' : document.getElementById('pdf_laser'),
		'pdf_torpedo' : document.getElementById('pdf_torpedo'),
		'pdf_projetil' : document.getElementById('pdf_projetil'),
		'pdf_bombardeamento' : document.getElementById('pdf_bombardeamento'),
		'blindagem' : document.getElementById('blindagem'),
		'escudos' : document.getElementById('escudos'),
		'hp' : document.getElementById('hp'),
		'categoria' : document.getElementById('categoria'),
		'especiais' : document.getElementById('especiais'),
		'pesquisa' : document.getElementById('pesquisa'),
		'nivel_estacao_orbital' : document.getElementById('nivel_estacao_orbital'),
		'qtd_tropas' : document.getElementById('qtd_tropas'),
		'qtd_baia_de_torpedeiros' : document.getElementById('qtd_baia_de_torpedeiros'),
		'mk_camuflagem' : document.getElementById('mk_camuflagem'),
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


/******************
function salva_modelo_nave
--------------------
Salva um modelo de uma nave
******************/
function salva_modelo_nave(evento, objeto, modelo_em_uso = false) {
	//let inputs = document.getElementById("simulador_nave").getElementsByTagName("INPUT");
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		
		evento.preventDefault();
		return false;
	}
	
	let dados = {};
	dados['nome_modelo'] = encodeURIComponent(document.getElementById("nome_modelo").value);
	dados['string_nave'] = document.getElementById("texto_partes_nave").innerText;
	dados['texto_nave'] = document.getElementById("dados").innerText;
	dados['texto_custo'] = document.getElementById("custos").innerText;
	dados['id_imperio'] = objeto.getAttribute("data-id_imperio");
	
	for (const property in dados) {
		console.log(dados[property]);
		if (dados[property] == "") {
			alert('Nenhum dado pode ser deixado em branco!');
			return false;
		}
	}

	calcula_custos(evento, objeto);
	//let input_string_construcao = document.getElementById("input_string_construcao");	
	//input_string_construcao.value = dados['string_nave'];
	//processa_string(evento, objeto);
	
	dados_nave = JSON.parse(dados['string_nave']);
	dados_ajax_where = "";
	if (dados_nave['id'] != undefined) {
		if (modelo_em_uso) {
		//Remove o ID e o Nome do Modelo antes de salvar
			delete dados_nave.id;
			delete dados_nave.nome_modelo;
			dados['string_nave'] = JSON.stringify(dados_nave);
		} else {
			dados_ajax_where = "&where_clause=id&where_value="+dados_nave['id'];
		}
		//let salva_edicao = "&where_clause=id&where_value="+dados_nave['id'];
		//console.log(salva_edicao);
		//let confirma = confirm("Para SOBRESCREVER o modelo carregado, clique em OK.\nPara salvar um NOVO modelo, clique em CANCELAR.");
		//if (confirma) {
		//	dados_ajax = dados_ajax + "&where_clause=id&where_value="+dados_nave['id'];
		//}
	}
	
	let dados_ajax = "post_type=POST&action=salva_objeto&tabela=colonization_modelo_naves&id_imperio="+dados['id_imperio']+"&nome_modelo="+dados['nome_modelo']
	+"&string_nave="+dados['string_nave']+"&texto_nave="+dados['texto_nave']+"&texto_custo="+dados['texto_custo']+"&turno="+turno_atual
	+ dados_ajax_where;
	
	let valida_modelo_nave = new Promise((resolve, reject) => {
		objeto_em_edicao = true;
		let dados_ajax_valida = "post_type=POST&action=valida_modelo_nave&id_imperio="+dados['id_imperio']+"&nome_modelo="+dados['nome_modelo']
		+"&string_nave="+dados['string_nave']+dados_ajax_where;
		resolve(processa_xhttp_resposta(dados_ajax_valida));
		//resolve(dados_ajax);
	});
	
	valida_modelo_nave.then((successMessage) => {
		if (successMessage.resposta_ajax == "OK!") {
			let resposta = new Promise((resolve, reject) => {
				objeto_em_edicao = true;
				resolve(processa_xhttp_resposta(dados_ajax));
				//resolve(dados_ajax);
			});
			
			resposta.then((successMessage) => {
				console.log(successMessage);
				if (successMessage.resposta_ajax != "SALVO!") {
					alert(successMessage.resposta_ajax);
				} else {
					document.location.reload();
				}
			});		
		} else {
			alert(successMessage.resposta_ajax);
			objeto_em_edicao = false;
		}
	});
	
	
	evento.preventDefault();
	return false;
}

/******************
function deleta_modelo_nave
--------------------
Deleta uma nave
******************/
function deleta_modelo_nave(evento, objeto, id_nave) {
	let confirma = confirm("Tem certeza que deseja excluir essa nave?");
	
	if (!confirma) {
		evento.preventDefault();
		return false;		
	}
	
	let dados_ajax = "post_type=POST&action=deleta_modelo_nave&id=" + id_nave;
	
	let resposta = new Promise((resolve, reject) => {
		resolve(processa_xhttp_resposta(dados_ajax));
	});
	
	resposta.then((successMessage) => {
		if (successMessage.resposta_ajax != "OK!") {
			alert(successMessage);
		} else {
			let linha = pega_ascendente(objeto,"TR");
			linha.remove();
		}
	});

	evento.preventDefault();
	return false;
}


/******************
function reseta_modelo
--------------------
Reseta os dados para um modelo em branco
******************/
function reseta_modelo(evento, objeto) {
	let confirma = confirm('Todos os dados modificados serão PERDIDOS. Deseja continuar?');
	if (!confirma) {
		evento.preventDefault();
		return false;
	}

	let input_string_construcao = document.getElementById('input_string_construcao');
	let nome_modelo = document.getElementById('nome_modelo');
	let link_salva_modelo_nave = document.getElementById("link_salva_modelo_nave");
	let link_salva_novo_modelo_nave = document.getElementById("link_salva_novo_modelo_nave");	
	
	input_string_construcao.value = '{"mk_impulso":"1","mk_dobra":"1"}';
	nome_modelo.value = '';
	link_salva_modelo_nave.style.display = "inline";
	link_salva_novo_modelo_nave.style.display = "none";	
	processa_string(evento,objeto);
	
	evento.preventDefault();
	return false;
	
}

/******************
function repara_nave
--------------------
Repara uma nave
******************/
function repara_nave(evento, objeto, id_nave, custo_reparo) {
	let texto_industrializaveis = "Industrializáveis";
	if (custo_reparo == 1) {
		texto_industrializaveis = "Industrializável";
	}
	
	let confirma = confirm("O custo para reparar essa nave será de " + custo_reparo + " " + texto_industrializaveis + ".\nPodemos concluir a operação de reparo?");
	
	if (!confirma) {
		evento.preventDefault();
		return false;		
	}
	
	let dados_ajax = "post_type=POST&action=repara_nave&id=" + id_nave;
	
	let resposta = new Promise((resolve, reject) => {
		resolve(processa_xhttp_resposta(dados_ajax));
	});
	
	resposta.then((successMessage) => {
		if (successMessage.resposta_ajax != "OK!") {
			alert(successMessage.resposta_ajax);
		} else {
			window.setTimeout(function(){document.location.reload();},1000);
		}
	});

	evento.preventDefault();
	return false;
}

/******************
function carrega_modelo_nave
--------------------
Carrega a string de uma nave salva e a processa
******************/
function carrega_modelo_nave(evento, objeto, id_imperio) {
	let tabela = pega_ascendente(objeto,"TABLE");
	let linha = pega_ascendente(objeto,"TR");
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let string_nave = "";
	
	let link_salva_modelo_nave = document.getElementById("link_salva_modelo_nave");
	let link_salva_novo_modelo_nave = document.getElementById("link_salva_novo_modelo_nave");
	link_salva_modelo_nave.setAttribute("data-id_imperio",id_imperio);
	link_salva_novo_modelo_nave.setAttribute("data-id_imperio",id_imperio);
	link_salva_novo_modelo_nave.style.display = "inline";
	
	console.log(objeto.getAttribute("data-modelo_em_uso"));
	if (objeto.getAttribute("data-modelo_em_uso") == "true") {
		link_salva_modelo_nave.style.display = "none";
	} else {
		link_salva_modelo_nave.style.display = "inline";
	}
	
	for (let index=0; index<inputs_linha.length; index++) {
		if (inputs_linha[index].getAttribute("data-atributo") == "string_nave") {
			string_nave = inputs_linha[index].value.replaceAll("\\","");
			break;
		}
	}
	
	let input_string_nave = document.getElementById('input_string_construcao');
	input_string_nave.value = string_nave;
	calcula_custos(evento, objeto);
	processa_string(evento, objeto);
	calcula_custos(evento, objeto);
	input_string_nave.scrollIntoView({behavior: "smooth", block: "center", inline: "start"});
	
	evento.preventDefault();
	return false;
}




/******************
function carrega_nave
--------------------
Carrega uma nave do banco de dados, adicionando-a à tabela de naves
******************/
function carrega_nave(id_nave) {
	let dados_ajax = "post_type=POST&action=carrega_nave&id=" + id_nave;
	
	let resposta = new Promise((resolve, reject) => {
		resolve(processa_xhttp_resposta(dados_ajax));
	});
	
	return resposta.then((successMessage) => {
		if (successMessage.resposta_ajax == "OK!") {
			console.log(successMessage.lista_dados);
			return successMessage.lista_dados;
		} else {
			alert(successMessage.resposta_ajax);
		}
	});
}

/******************
function copiar_nave(evento, objeto, id_imperio, upgrade=false)
--------------------
Copia um objeto na última linha
objeto -- objeto sendo editado
id_imperio -- Império dono dessa nave
upgrade = false -- se é um upgrade, edita os dados necessários, salva e cria uma nova nave
******************/
function copiar_nave(evento, objeto, id_imperio, upgrade=false) {
	//let tabela = document.getElementsByTagName('TABLE');
	let tabela = pega_ascendente(objeto, "TABLE");
	
	for (let index_tabelas = 0; index_tabelas < tabela.length; index_tabelas++) {
		if (tabela[index_tabelas].getAttribute('data-id-imperio') == id_imperio) {
			tabela = tabela[index_tabelas];
			break;
		}
	}
	
	let linha = pega_ascendente(objeto,"TR");
	let celulas = linha.getElementsByTagName("TD");
	let inputs = [];

	if (upgrade) {
		let objeto_editado = edita_objeto(evento, objeto);
		let input = [];
		
		inputs = linha.getElementsByTagName("INPUT");
		let HP = "";
		let tamanho = "";
		
		for (let index_input = 0; index_input < inputs.length; index_input++) {
			if (inputs[index_input].getAttribute('data-atributo') == "custo" || inputs[index_input].getAttribute('data-atributo') == "turno_destruido"
				|| inputs[index_input].getAttribute('data-atributo') == "X" || inputs[index_input].getAttribute('data-atributo') == "Y" || inputs[index_input].getAttribute('data-atributo') == "Z") {
				input[inputs[index_input].getAttribute('data-atributo')] = inputs[index_input];
			} else if (inputs[index_input].getAttribute('data-atributo') == "HP") {
				HP = inputs[index_input];
			} else if (inputs[index_input].getAttribute('data-atributo') == "tamanho") {
				tamanho = inputs[index_input];
			}
		}
		
		if (input['turno_destruido'].value != 0) {
			alert('Não é possível realizar o upgrade de uma nave que já tenha sido destruída!');
			salva_objeto(evento, objeto, true);
			
			evento.preventDefault();
			return false;
		} else if (HP.value*1 < tamanho.value*10) {
			alert('Não é possível realizar o upgrade de uma nave danificada!');
			salva_objeto(evento, objeto, true);
			
			evento.preventDefault();
			return false;			
		}
		
		processa_string_admin(evento, objeto, true); //Processa a string da nave para corrigir eventuais diferenças de custo
		let custo_nave = JSON.parse(input['custo'].value);
		custo_nave['upgrade'] = true;
		input['custo'].value = JSON.stringify(custo_nave);
		input['turno_destruido'].value = turno_atual;
		
		objeto_em_edicao = false;
		objeto_em_edicao = false;
		
		let retorno_salva_objeto = new Promise((resolve, reject) =>	{
			resolve(salva_objeto(evento, objeto));
		});
		
		return retorno_salva_objeto.then((successMessage) => {
			//console.log("Nave foi destruída e custos devolvidos!");
			if (successMessage) {
				console.log("Nave foi removida com sucesso, criando nova linha para receber os dados do upgrade!");
				let nova_nave = copiar_nave(evento, objeto, id_imperio);
				let inputs_nave_upgrade = nova_nave.getElementsByTagName("INPUT");
				
				for (let index_input = 0; index_input < inputs_nave_upgrade.length; index_input++) {
					if (inputs_nave_upgrade[index_input].getAttribute('data-atributo') == "turno") {
						inputs_nave_upgrade[index_input].value = turno_atual;
					} else if (inputs_nave_upgrade[index_input].getAttribute('data-atributo') == "turno_destruido") {
						inputs_nave_upgrade[index_input].value = 0;
					}
				}
				objeto.remove();
				return nova_nave;
			} else {
				salva_objeto(evento, objeto, true);
				return false;
			}
			//nova_nave.scrollIntoView({behavior: "smooth", block: "center", inline: "start"});
		});
		
		//return false;
	}
	
	let linha_nova = tabela.insertRow(-1);
	
	let celula = "";
	for (let index = 0; index < celulas.length; index++) {
		celula = linha_nova.insertCell(index);
		celula.innerHTML = celulas[index].innerHTML;
	}

	let retorno = edita_objeto(evento, celula);

	celulas = linha_nova.getElementsByTagName("TD");
	inputs = linha_nova.getElementsByTagName("INPUT");
	ahrefs = linha_nova.getElementsByTagName("A");

	for (let index_input = 0; index_input < inputs.length; index_input++) {
		if (inputs[index_input].getAttribute('data-atributo') == "id" || inputs[index_input].getAttribute('data-atributo') == "where_value") {
			inputs[index_input].value = "";
			inputs[index_input].setAttribute('data-valor-original',"");
		} else if(inputs[index_input].getAttribute('data-atributo') == "custo") {
			let custo_nave = JSON.parse(inputs[index_input].value);
			delete custo_nave.upgrade;
			inputs[index_input].value = JSON.stringify(custo_nave);
		}  else if (id_imperio !=0) {
			//if (inputs[index_input].getAttribute('data-atributo') == "X" 
			//	|| inputs[index_input].getAttribute('data-atributo') == "Y" || inputs[index_input].getAttribute('data-atributo') == "Z") {
			//	let string_data = 'data-' + inputs[index_input].getAttribute('data-atributo');
			//	inputs[index_input].value = tabela.getAttribute(string_data);
			//} else if (inputs[index_input].getAttribute('data-atributo') == "turno") {
			if (inputs[index_input].getAttribute('data-atributo') == "turno") {
				inputs[index_input].value = turno_atual;
			}
		}
	}
	
	for (let index_anchors = 0; index_anchors < ahrefs.length; index_anchors++) {
		//console.log(ahrefs[index_anchors].text);
		if (ahrefs[index_anchors].text == "Cancelar" ) {
			ahrefs[index_anchors].setAttribute('onclick','return cancela_edicao(event, this);');
		} else if (ahrefs[index_anchors].text == "Criar cópia" || ahrefs[index_anchors].text == "Upgrade") {
			ahrefs[index_anchors].style.visibility = "hidden";
		}
	}		
	
	evento.preventDefault();
	linha_nova.scrollIntoView({behavior: "smooth", block: "center", inline: "start"});
	return linha_nova;
}