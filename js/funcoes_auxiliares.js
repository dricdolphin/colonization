/******************
function calcula_distancia
--------------------
Calcula a distância entre duas estrelas
******************/
function calcula_distancia(exibe=true, estrela_origem_id=0, estrela_destino_id=0) {
	let estrela_origem = document.getElementById('estrela_origem');
	let estrela_destino = document.getElementById('estrela_destino');
	let distancia_div = document.getElementById('distancia');

	let select_estrela_origem = estrela_origem.childNodes[1];
	let select_estrela_destino = estrela_destino.childNodes[1];

	
	if (estrela_origem_id == 0) {
		estrela_origem_id = select_estrela_origem.value;
	}

	if (estrela_destino_id == 0) {
		estrela_destino_id = select_estrela_destino.value;
	}
	
	estrela_origem_x = lista_x_estrela[estrela_origem_id];
	estrela_origem_y = lista_y_estrela[estrela_origem_id];
	estrela_origem_z = lista_z_estrela[estrela_origem_id];
	
	estrela_destino_x = lista_x_estrela[estrela_destino_id];
	estrela_destino_y = lista_y_estrela[estrela_destino_id];
	estrela_destino_z = lista_z_estrela[estrela_destino_id];
	
	distancia = Math.ceil(Math.sqrt(Math.pow((estrela_origem_x-estrela_destino_x),2)+Math.pow((estrela_origem_y-estrela_destino_y),2)+Math.pow((estrela_origem_z-estrela_destino_z),2))*10)/10;
	
	if (exibe) {
		distancia_div.innerHTML = "<b>Distância:</b> "+distancia.toFixed(1);
	} else {
		return distancia.toFixed(1);
	}
}

/******************
function calcula_pulos_hyperdrive
--------------------
Cria o caminho dos pulos de hyperdrive
******************/
function calcula_pulos_hyperdrive() {
	let estrela_origem = document.getElementById('estrela_origem_h');
	let estrela_destino = document.getElementById('estrela_destino_h');
	let distancia_div = document.getElementById('distancia_h');

	let select_estrela_origem = estrela_origem.childNodes[1];
	let select_estrela_destino = estrela_destino.childNodes[1];
	
	estrela_origem_id = select_estrela_origem.value;
	estrela_destino_id = select_estrela_destino.value;
	
	estrela_origem_x = lista_x_estrela[estrela_origem_id];
	estrela_origem_y = lista_y_estrela[estrela_origem_id];
	estrela_origem_z = lista_z_estrela[estrela_origem_id];
	
	estrela_destino_x = lista_x_estrela[estrela_destino_id];
	estrela_destino_y = lista_y_estrela[estrela_destino_id];
	estrela_destino_z = lista_z_estrela[estrela_destino_id];	
	
	//Vetor P0P1
	var i = estrela_destino_x - estrela_origem_x;
	var j = estrela_destino_y - estrela_origem_y;
	var k = estrela_destino_z - estrela_origem_z;
	
	//Plano perpendicular ao vetor P0P1
	var d = [];
	var t = [];
	
	//Ponto da reta perpendicular à estrela
	var x_reta = [];
	var y_reta = [];
	var z_reta = [];
	
	var d_reta = [];
	
	var a = [];
	var b = [];
	var c = [];
	var r = [];
	var calculo = 0;
	
	/*******************************************************
	Iterage entre as Estrelas para ir da Origem ao Destino
	********************************************************/
	var estrela_atual = estrela_origem_id;
	var pegou_estrela = false;
	var html = "<b>Caminho do Hyperdrive:</b><br>";
	var repeticoes = 0;
	
	while(estrela_atual != estrela_destino_id) {
		html = html+lista_nome_estrela[estrela_atual]+" ("+lista_x_estrela[estrela_atual]+";"+lista_y_estrela[estrela_atual]+";"+lista_z_estrela[estrela_atual]+")<br>";
		estrela_origem_x = lista_x_estrela[estrela_atual];
		estrela_origem_y = lista_y_estrela[estrela_atual];
		estrela_origem_z = lista_z_estrela[estrela_atual];

		//Vetor P0P1
		i = estrela_destino_x - estrela_origem_x;
		j = estrela_destino_y - estrela_origem_y;
		k = estrela_destino_z - estrela_origem_z;
	
		//Plano perpendicular ao vetor P0P1
		d = [];
		t = [];
	
		//Ponto da reta perpendicular à estrela
		x_reta = [];
		y_reta = [];
		z_reta = [];
	
		d_reta = [];
	
		a = [];
		b = [];
		c = [];
		r = [];

		//Pega os pontos da reta que sejam perpendiculares às estrelas
		lista_x_estrela.forEach( function (value, index, array) {
			d[index] = i*lista_x_estrela[index] + j*lista_y_estrela[index] + k*lista_z_estrela[index];
			if (isNaN(d[index])) {
				d[index] = 0;
			}
			t[index] = (d[index] - i*estrela_origem_x - j*estrela_origem_y - k*estrela_origem_z)/(Math.pow(i,2)+Math.pow(j,2)+Math.pow(k,2));
			x_reta[index] = estrela_origem_x + i*t[index];
			y_reta[index] = estrela_origem_y + j*t[index];
			z_reta[index] = estrela_origem_z + k*t[index];

			d_reta[index] = Math.ceil(Math.sqrt(Math.pow((estrela_origem_x-x_reta[index]),2)+Math.pow((estrela_origem_y-y_reta[index]),2)+Math.pow((estrela_origem_z-z_reta[index]),2))*1000)/1000;		

			a[index] = Math.pow(i,2) + Math.pow(j,2) + Math.pow(k,2);
			b[index] = -2*(i*(lista_x_estrela[index]-estrela_origem_x)+j*(lista_y_estrela[index]-estrela_origem_y)+k*(lista_z_estrela[index]-estrela_origem_z));

			if (d_reta[index] <3) {
				r[index] = d_reta[index];
			} else {
				r[index] = 3;
			}
			
			c[index] = Math.pow((lista_x_estrela[index]-estrela_origem_x),2)+Math.pow((lista_y_estrela[index]-estrela_origem_y),2)+Math.pow((lista_z_estrela[index]-estrela_origem_z),2)-Math.pow(r[index],2);
			
			//distancia_para_reta[index] = Math.ceil(Math.sqrt(Math.pow((lista_x_estrela[index]-x_reta[index]),2)+Math.pow((lista_y_estrela[index]-y_reta[index]),2)+Math.pow((lista_z_estrela[index]-z_reta[index]),2))*10)/10;
		});
		

		// array temporário que armazena os objetos com o índice e o valor para ordenação
		var mapped = d_reta.map(function(el, i) {
			return { index: i, value: el };
		});

		// ordenando o array mapeado
		mapped.sort(function(a, b) {
			return a.value - b.value;
		});

		mapped.forEach(function(valor, chave, mapa) {
			if (!pegou_estrela && valor.index != estrela_atual && t[valor.index] > 0 && t[valor.index] <=1) {
				calculo = Math.pow(b[valor.index],2)-4*a[valor.index]*c[valor.index];
				if (calculo >= 0) {
					estrela_atual = valor.index;
					pegou_estrela = true;
					console.log("ID: "+valor.index+" - "+lista_nome_estrela[valor.index]+" - d_reta: "+d_reta[valor.index]+" - calc: "+b[valor.index]+"^2-4*"+a[valor.index]+"*"+c[valor.index]+" = "+calculo);
				}
				
			}
			//console.log("ID: "+valor.index+" - "+lista_nome_estrela[valor.index]+" - "+valor.value);
		});
		
		pegou_estrela = false;
		repeticoes++;
		if (repeticoes > 100) {
			break;
		}
	}
	html = html+lista_nome_estrela[estrela_atual]+" ("+lista_x_estrela[estrela_atual]+";"+lista_y_estrela[estrela_atual]+";"+lista_z_estrela[estrela_atual]+")";
	
	distancia_div.innerHTML = html;
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
	
	var input_string_construcao = document.getElementById('string_nave').value;

	let qtd_laser = "";
	let qtd_torpedo = "";
	let qtd_projetil = "";
	let qtd_blindagem = "";
	let qtd_escudos = "";
	let qtd_impulso = "";
	let qtd_dobra = "";
	let qtd_combustivel = "";
	let qtd_pesquisa = document.getElementById('pesquisa');
	let qtd_estacao_orbital = document.getElementById('nivel_estacao_orbital');
	let qtd_tropas = document.getElementById('qtd_tropas');
	let qtd_bombas = document.getElementById('qtd_bombas');
	let qtd_slots_extra = "";
	
	let mk_laser = "";
	let mk_torpedo = "";
	let mk_projetil = "";
	let mk_blindagem = "";
	let mk_escudos = "";
	let mk_impulso = "";
	let mk_dobra = "";

	let qtd = [qtd_laser, qtd_torpedo, qtd_projetil, qtd_blindagem, qtd_escudos, qtd_impulso, qtd_dobra, qtd_combustivel, qtd_pesquisa, qtd_estacao_orbital, qtd_tropas, qtd_bombas, qtd_slots_extra];
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
		} else if (qtd[index].type == "text") {
			qtd[index].value = subparte[0];
		} else {
			qtd[index] = subparte[0];
		}
		
		if (mk[index] !== undefined) {
			mk[index] = subparte[1];
		}
	}

	qtd_laser = qtd[0];
	qtd_torpedo = qtd[1];
	qtd_projetil = qtd[2];
	qtd_blindagem = qtd[3];
	qtd_escudos = qtd[4];
	qtd_impulso = qtd[5];
	qtd_dobra = qtd[6];
	qtd_combustivel = qtd[7];
	qtd_slots_extra = qtd[12];

	mk_laser = mk[0];
	mk_torpedo = mk[1];
	mk_projetil = mk[2];
	mk_blindagem = mk[3];
	mk_escudos = mk[4];
	mk_impulso = mk[5];
	mk_dobra = mk[6];

	let chassi = 0;
	let categoria = "Corveta";

	let pdf_laser = 0;
	let pdf_torpedo = 0;
	let pdf_projetil = 0;
	let blindagem = 0;
	let escudos = 0;

	let tamanho = 0;
	let velocidade = 0;
	let alcance = 0;
	let hp = 0;

	let custo_estacao_orbital = 20*qtd_estacao_orbital.value;

	if (qtd_pesquisa.checked) {
		qtd_pesquisa = 1
	} else {
		qtd_pesquisa = 0
	}

	chassi = qtd_bombas.value*10 + qtd_tropas.value*5 + qtd_slots_extra*1 + Math.ceil(custo_estacao_orbital/1.5) 
	+ qtd_laser*mk_laser + qtd_torpedo*mk_torpedo + qtd_projetil*mk_projetil 
	+ qtd_dobra*1 + qtd_impulso*1 
	+ qtd_blindagem*1 + qtd_escudos*1 + qtd_combustivel*1 + qtd_pesquisa*1;
	
	velocidade = Math.floor((qtd_impulso*mk_impulso/chassi)*10);
	hp = chassi*10;
	
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

	let chassi_input = document.getElementById('tamanho');
	let categoria_input = document.getElementById('categoria');
	let pdf_laser_input = document.getElementById('PDF_laser');
	let pdf_torpedo_input = document.getElementById('PDF_torpedo');
	let pdf_projetil_input = document.getElementById('PDF_projetil');
	let blindagem_input = document.getElementById('blindagem');
	let escudos_input = document.getElementById('escudos');
	let alcance_input = document.getElementById('alcance');
	let velocidade_input = document.getElementById('velocidade');

	chassi_input.value = chassi;
	categoria_input.value = categoria;
	pdf_laser_input.value = pdf_laser;
	pdf_torpedo_input.value = pdf_torpedo;
	pdf_projetil_input.value = pdf_projetil;
	blindagem_input.value = blindagem;
	escudos_input.value = escudos;
	alcance_input.value = alcance;
	velocidade_input.value = velocidade;
	
	evento.preventDefault();
	return false;
}

/******************
function lista_distancia
--------------------
Popula a lista com as estrelas onde uma nave do Império escolhido possa chegar
******************/
function lista_distancia() {
	let div_lista_distancia = document.getElementById('lista_distancia');
	let alcance_nave = document.getElementById('alcance_nave').value;
	let tech_logistica = document.getElementById('tech_logistica');
	
	let alcance_extendido = 2;
	if (tech_logistica.checked) {
		alcance_extendido = 1;
	}
	
	if (alcance_nave == 0) {
		div_lista_distancia.innerHTML = "";
		return false;
	}
	
	//var lista_estrelas_colonia=[];
	//var lista_estrelas_reabastece=[];	
	
	let distancia = [];
	var estrelas_imperio = [];
	var estrelas_destino = [];
	
	var mapped_estrelas_colonia = lista_estrelas_colonia[id_imperio_atual].map(function(el, i) {
		return { index: i, value: el };
	});

	mapped_estrelas_colonia.forEach(
	function(valor, chave, mapa) {
		//A chave será o id_estrela_origem
		distancia[chave] = [];
		estrelas_imperio[chave] = valor;
	});

	var mapped_estrelas_reabastece = lista_estrelas_reabastece[id_imperio_atual].map(function(el, i) {
		return { index: i, value: el };
	});

	
	mapped_estrelas_reabastece.forEach(
	function(valor_reabastece, chave_reabastece, mapa_reabastece) {
		//Só adicionamos os pontos de reabastecimento se alguma Estrela que seja do Império estiver dentro do alcance da nave
		mapped_estrelas_colonia.forEach(
		function(valor_colonia, chave_colonia, mapa_colonia) {
			distancia_parsecs = calcula_distancia(false, chave_reabastece, chave_colonia);
			alcance_real = alcance_nave*alcance_extendido;
			if (distancia_parsecs*1 <= alcance_real*1) {
				distancia[chave_reabastece] = [];
				estrelas_imperio[chave_reabastece] = chave_reabastece;
			}
		});
	});
	

	var mapped_estrelas = lista_x_estrela.map(function(el, i) {
		return { index: i, value: el };
	});	

	var mapped_estrelas_imperio= estrelas_imperio.map(function(el, i) {
		return { index: i, value: el };
	});	
	
	mapped_estrelas_imperio.forEach(
	function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
		mapped_estrelas.forEach(
		function(valor_estrelas, id_estrela_destino, mapa_estrelas) {
			distancia_parsecs = calcula_distancia(false, id_estrela_origem, id_estrela_destino);
			if (estrelas_imperio[id_estrela_destino] == id_estrela_destino) {
				alcance_real = alcance_nave*alcance_extendido;
			} else {
				alcance_real = alcance_nave*1;
			}
			
			if 	(distancia_parsecs*1 <= alcance_real*1 && id_estrela_origem != id_estrela_destino) {
				distancia[id_estrela_origem][id_estrela_destino] = false;
				estrelas_destino[id_estrela_destino] = false;
			}
		});	
	});	
	
	var mapped_origem = distancia.map(function(el, i) {
		return { index: i, value: el };
	});	

	let html_lista = "&nbsp;";
	var mapped_estrelas_destino = estrelas_destino.map(function(el, i) {
		return { index: i, value: el };
	});	
	
	
	mapped_estrelas_destino.forEach(function(valor_destini, chave_destino, mapa_destino) {
		html_lista = html_lista + lista_nome_estrela[chave_destino] +" ("+lista_x_estrela[chave_destino]+";"+lista_y_estrela[chave_destino]+";"+lista_z_estrela[chave_destino]+")<br>";
		distancia[chave_destino] = true;
	});
	
	/***
	mapped_origem.forEach(function(valor_origem, chave_origem, mapa_origem) {
		let mapped_destino = distancia[chave_origem].map(function(el, i) {
			return { index: i, value: el };
		});	
		mapped_destino.forEach(function(valor_destino, chave_destino, mapa_destino) {		
			if (distancia[chave_origem][chave_destino] === false) {
				html_lista = html_lista + lista_nome_estrela[chave_origem] + " -> " + lista_nome_estrela[chave_destino] + "<br>";
				distancia[chave_origem][chave_destino] = true;
			}
		});
	});
	***/
	
	div_lista_distancia.innerHTML = html_lista;
	
	return false;
}