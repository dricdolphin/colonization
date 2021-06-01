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
	'mk_laser' : 0,
	'mk_torpedo' : 0,
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
	
	chassi = nave.qtd_bombardeamento*10 + nave.qtd_tropas*7 + nave.qtd_slots_extra*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil + qtd_pesquisa*1;
	
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
	
	pdf_laser = nave.qtd_laser*(nave.mk_laser*2-1);
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
		if (chassi > 50) {//Corrige o consumo de recursos para Blindagem e Escudos para naves acima de 50 slots
			corasita = (Math.trunc(50/5,0)+1)*nave.mk_escudos + Math.trunc(Math.pow((chassi-50)/2,(1/3)))*nave.mk_escudos;
		}		
		energium_escudos = 0;
		texto_corasita = " | Corasita: "+corasita;
	}
	
	if (nave.mk_camuflagem*1 > 3) {
		corasita = corasita + nave.mk_camuflagem*1;
		texto_corasita = " | Corasita: "+corasita;
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

	industrializaveis = nave.qtd_bombardeamento*nave.mk_bombardeamento*10 + custo_estacao_orbital*1 + nave.qtd_laser*nave.mk_laser + nave.qtd_torpedo*nave.mk_torpedo + nave.qtd_projetil*nave.mk_projetil
	+ qtd_impulso*nave.mk_impulso + qtd_dobra*nave.mk_dobra + nave.qtd_combustivel*1 + custo_blindagem*1 + custo_escudos*1 + nave.qtd_pesquisa*1 + nave.qtd_slots_extra*1 + nave.qtd_tropas*1  + nave.mk_camuflagem*1;
	
	energium = Math.ceil(custo_estacao_orbital/4) + nave.qtd_laser*1 + nave.qtd_torpedo*1 + nave.qtd_combustivel*1 + energium_escudos*1 + qtd_impulso*1;
	dillithium = qtd_dobra*nave.mk_dobra;
	duranium = duranium*1 + nave.qtd_projetil*1;
	
	let texto_trillithium = "";
	let trillithium = 0;
	if (nave.mk_dobra*1 > 2) {
		trillithium = dillithium;
		texto_trillithium = " | Trillithium: "+ trillithium;
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
		'Tricobalto': tricobalto*1
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
		+"pdf Laser: "+pdf_laser+"/ pdf Torpedo: "+pdf_torpedo+"/ pdf Projétil: "+pdf_projetil+"; Blindagem: "+blindagem+"/ Escudos: "+escudos+"; HP: "+hp;
		chassi_div.innerHTML = "Chassi: "+chassi+" - Categoria: "+categoria;
		custos_div.innerHTML = "Industrializáveis: "+industrializaveis+" | Enérgium: "+energium+" | Dillithium: "+dillithium+" | Duranium: "+ duranium + texto_nor_duranium + texto_corasita + texto_trillithium 
		+ texto_tritanium + texto_neutronium + texto_tricobalto;
	
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
	if (input_string_construcao !== undefined) {
		for(let index = 0; index < partes_nave.length; index++) {
			if (partes_nave[index].id != "input_string_construcao") {
				objeto_nave[partes_nave[index].id] = 0;
				if (partes_nave[index].type == "checkbox") {
					partes_nave[index].checked = false;
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
		if (typeof(nave[property]) == "boolean") {
			nave_elementos[property].checked = nave[property];
		} else {
			nave_elementos[property].value = nave[property];
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
function salvar_nave
--------------------
Salva uma nave
******************/
function salvar_nave(evento, objeto, id_imperio) {
	//let inputs = document.getElementById("simulador_nave").getElementsByTagName("INPUT");
	if (objeto_em_salvamento) {
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
	
	let dados_ajax = "post_type=POST&action=salva_objeto&tabela=colonization_modelo_naves&id_imperio="+dados['id_imperio']+"&nome_modelo="+dados['nome_modelo']
	+"&string_nave="+dados['string_nave']+"&texto_nave="+dados['texto_nave']+"&texto_custo="+dados['texto_custo']+"&turno="+turno_atual;
	
	dados_nave = JSON.parse(dados['string_nave']);
	if (dados_nave['id'] != undefined) {
		//let salva_edicao = "&where_clause=id&where_value="+dados_nave['id'];
		//console.log(salva_edicao);
		let confirma = confirm("Para SOBRESCREVER o modelo carregado, clique em OK.\nPara salvar um NOVO modelo, clique em CANCELAR.");
		if (confirma) {
			dados_ajax = dados_ajax + "&where_clause=id&where_value="+dados_nave['id'];
		}
	}
	
	let resposta = new Promise((resolve, reject) => {
		objeto_em_salvamento = true;
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
	
	evento.preventDefault();
	return false;
}

/******************
function deleta_nave
--------------------
Deleta uma nave
******************/
function deleta_nave(evento, objeto, id_nave) {
	let confirma = confirm("Tem certeza que deseja excluir essa nave?");
	
	if (!confirma) {
		evento.preventDefault();
		return false;		
	}
	
	let dados_ajax = "post_type=POST&action=deletar_nave&id=" + id_nave;
	
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
function carrega_nave
--------------------
Carrega a string de uma nave salva e a processa
******************/
function carrega_nave(evento, objeto, id_imperio) {
	let tabela = pega_ascendente(objeto,"TABLE");
	let linha = pega_ascendente(objeto,"TR");
	let inputs_linha = linha.getElementsByTagName("INPUT");
	let string_nave = "";
	
	let link_salvar_nave = document.getElementById("link_salvar_nave");
	link_salvar_nave.setAttribute("data-id_imperio",id_imperio);
	
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