/******************
function calcula_distancia
--------------------
Calcula a distância entre duas estrelas
******************/
function calcula_distancia(exibe=true, estrela_origem_id=0, estrela_destino_id=0) {
	let estrela_origem = document.getElementById('estrela_origem');
	let estrela_destino = document.getElementById('estrela_destino');
	let distancia_div = document.getElementById('distancia');
	
	if (estrela_origem_id == 0) {
		var select_estrela_origem = estrela_origem.childNodes[1];
		estrela_origem_id = select_estrela_origem.value;
	}

	if (estrela_destino_id == 0) {
		var select_estrela_destino = estrela_destino.childNodes[1];
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
function calcula_distancia_reabastece
--------------------
Calcula a distância entre duas estrelas, considerando a possibilidade de reabastecer
******************/
function calcula_distancia_reabastece(evento, objeto, id_nave) {
	let linha = pega_ascendente(objeto,"TR");
	let tabela = pega_ascendente(objeto,"TABLE");
	let tabela_selects = tabela.getElementsByTagName("SELECT");
	let divs = linha.getElementsByTagName("DIV");
	let destinos_select = "";
	let alcance = 0;
	
	for (let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute('data-atributo') == "nome_estrela") {
			for (let index_child=0; index_child<divs[index].childNodes.length; index_child++) {
				if (divs[index].childNodes[index_child].tagName == "SELECT") {
					var id_estrela = divs[index].childNodes[index_child].value;
					destinos_select = divs[index].childNodes[index_child];
					
					estrela_destino_id = destinos_select.value;
					alcance = destinos_select.getAttribute('data-alcance');
					//destinos_select.disabled = true;
				}
			}
		}
	}
	
	let estrela_origem_id = 0;
	for (let index_nave=0; index_nave < tabela_selects.length; index_nave++) {
		if (tabela_selects[index_nave] == destinos_select) {
			estrela_origem_id = id_estrela_atual[index_nave];
			break;
		}
	}
	
	
	//Verifica se está num ponto de reabastecimento. Se não estiver, verifica a distância do local atual até o ponto de reabastecimento mais próximo.
	//O motivo é para verificar quanto de combustível a nave ainda tem. Ela sempre sai de tanque cheio!
	let distancia_pontos_reabastecimento = [];
	if (lista_estrelas_colonia[id_imperio_atual][estrela_origem_id] != undefined || lista_estrelas_reabastece[id_imperio_atual][estrela_origem_id] != undefined) {
		combustivel_restante = alcance;
	} else {
		
		let lista_estrelas_reabastece_imperio = [];
		lista_estrelas_colonia[id_imperio_atual].forEach( id_estrela => {
			lista_estrelas_reabastece_imperio.push(id_estrela);
		});
		
		lista_estrelas_reabastece[id_imperio_atual].forEach( id_estrela => {
			lista_estrelas_reabastece_imperio.push(id_estrela);
		});
		
		let distancia_ate_reabastecimento = lista_estrelas_reabastece_imperio.map(id_estrela => {
			let distancia = calcula_distancia(false, estrela_origem_id, id_estrela);
			return {'id_estrela': id_estrela, 'distancia': distancia }
		});
		
		distancia_ate_reabastecimento.sort(function(a, b) {
			return a.distancia - b.distancia;
		});
		
		combustivel_restante = Math.floor(alcance - distancia_ate_reabastecimento[0].distancia);
	}
	
	//Verifica a distância atual até a estrela destino
	//Se a distância for maior do que o combustível da nave, precisamos reabastecer para chegar lá!
	if (calcula_distancia(false, estrela_origem_id, estrela_destino_id) > combustivel_restante) {
		
	} else {//É só gastar o combustível!
		
	}
	
	let string_resposta = calcula_distancia(false, estrela_origem_id, estrela_destino_id);
	string_resposta = string_resposta + " -> " + alcance;
	
	evento.preventDefault();
	return false;
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
function array_estrelas
--------------------
Retorna um Array com as das estrelas possíveis de serem alcançadas
******************/
function array_estrelas (alcance_nave,alcance_extendido=2,reabastece=true,estrela_atual='capital') {

	let estrelas_imperio = [];
	let estrelas_destino = [];
	let mapped_estrelas_reabastece = []

	if (alcance_nave == 0) {
		return estrelas_destino;
	}
	
	if (estrela_atual == 'capital') {
		estrela_atual = estrela_capital[id_imperio_atual];
	}
	
	let mapped_estrelas_colonia = lista_estrelas_colonia[id_imperio_atual].map(function(el, i) {
		return { index: i, value: el };
	});

	mapped_estrelas_colonia.forEach(
	function(valor, chave, mapa) {
		//A chave será o id_estrela_origem
		estrelas_imperio[chave] = true;
	});

	if (alcance_extendido == 2) {//Alcance das Naves. Para o Alcance Logístico, não considere os pontos de reabastecimento.
		mapped_estrelas_reabastece = lista_estrelas_reabastece[id_imperio_atual].map(function(el, i) {
			return { index: i, value: el };
		});

		mapped_estrelas_reabastece.forEach(
		function(valor_reabastece, chave_reabastece, mapa_reabastece) {
			estrelas_imperio[chave_reabastece] = true;
		});
	}
	
	let caminho_completo = "";
	//Verifica qual estrela do Império (incluindo pontos de Reabastecimento), à partir da estrela atual, a nave consegue chegar
	let mapped_estrelas_imperio= estrelas_imperio.map(function(el, i) {
		return { index: i, value: el };
	});	

	let tem_ponto_reabastece = false;
	mapped_estrelas_imperio.forEach(
	function(valor_estrelas_imperio, id_estrela_destino, mapa_estrelas_imperio) {
		distancia_parsecs = calcula_distancia(false, estrela_atual, id_estrela_destino);
		alcance_real = alcance_nave*alcance_extendido;
		if (alcance_real*1 >= distancia_parsecs*1 && !estrelas_destino.hasOwnProperty(id_estrela_destino) ) {
			estrelas_destino[id_estrela_destino] = true;
			caminho_completo = caminho_completo + lista_nome_estrela[estrela_atual] + "->" + lista_nome_estrela[id_estrela_destino] + "("+distancia_parsecs+")\n";
			if (reabastece) {
				tem_ponto_reabastece = true;
			} else if (id_estrela_destino == estrela_atual) {
				tem_ponto_reabastece = true;
			}
		}
	});

	let estrelas_destino_temp = [];
	let mapped_estrelas_origem = "";
	let mapped_estrelas_destino = "";
	let mapped_estrelas_temp = "";
	let startDate = new Date();
	
	while (tem_ponto_reabastece) {
		tem_ponto_reabastece = false;
		estrelas_destino_temp = [];
	
		mapped_estrelas_origem = estrelas_destino.map(function(el, i) {
			return { index: i, value: el };
		});
		
		mapped_estrelas_destino = lista_x_estrela.map(function(el, i) {
			return { index: i, value: el };
		});
		
		mapped_estrelas_origem.forEach(
		function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
			mapped_estrelas_destino.forEach(
			function(valor_estrelas_imperio, id_estrela_destino, mapa_estrelas_imperio) {
				distancia_parsecs = calcula_distancia(false, id_estrela_origem, id_estrela_destino);
				alcance_real = alcance_nave*1;
				if (estrelas_imperio[id_estrela_destino] === true) {
					alcance_real = alcance_nave*alcance_extendido;
				}
				//Caso a distância entre a estrela_origem e a estrela_destino esteja no alcance da nave e caso a estrela_origem faça parte dos pontos de reabastecimento do Império, pode colocar e estrela destino como um novo destino
				if (alcance_real*1 >= distancia_parsecs*1 && estrelas_destino[id_estrela_destino] !== true && estrelas_imperio[id_estrela_origem] === true) {
					estrelas_destino_temp[id_estrela_destino] = true;
					caminho_completo = caminho_completo + lista_nome_estrela[id_estrela_origem] + "->" + lista_nome_estrela[id_estrela_destino] + "("+distancia_parsecs+")\n";
					
					if (estrelas_imperio[id_estrela_destino] === true) {
						if (reabastece) {
							tem_ponto_reabastece = true;
						}
					}
				}
			});
		});
		
		mapped_estrelas_temp = estrelas_destino_temp.map(function(el, i) {
			return { index: i, value: el };
		});

		mapped_estrelas_temp.forEach(
		function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
			estrelas_destino[id_estrela_origem] = true;
		});	
	}
	let endDate   = new Date();
	let miliseconds = (endDate.getTime() - startDate.getTime());	
	console.log("Buscando os Caminhos: "+miliseconds*1+"ms");
	
	//estrelas_destino.sort();
	mapped_estrelas_origem = estrelas_destino.map(function(el, i) {
		return { index: i, value: el, id_estrela: i };
	});
	
	estrelas_destino = [];
	mapped_estrelas_origem.forEach(
	function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
		if (valor_estrelas_imperio.id_estrela != estrela_atual) {
			estrelas_destino[valor_estrelas_imperio.id_estrela] = true;
		}
	});	
	
	console.log(caminho_completo);
	return estrelas_destino;
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
	
	let html_lista = "";
	let reabastece = true;
	estrelas_destino = array_estrelas(alcance_nave, alcance_extendido, reabastece);
	
	var mapped_estrelas_destino = estrelas_destino.map(function(el, i) {
		return { index: i, value: el, id_estrela: i, nome_estrela: lista_nome_estrela[i], posicao_estrela: " ("+lista_x_estrela[i]+";"+lista_y_estrela[i]+";"+lista_z_estrela[i]+")" };
	});	
	
	mapped_estrelas_destino.sort(function(firstEl, secondEl) {
		if (firstEl.nome_estrela.toLowerCase() < secondEl.nome_estrela.toLowerCase()) {
		return -1;
		}
		if (firstEl.nome_estrela.toLowerCase() > secondEl.nome_estrela.toLowerCase()) {
		return 1;
		}
		// a must be equal to b
		return 0;
	});
	
	let estrela_atual = estrela_capital[id_imperio_atual];
	mapped_estrelas_destino.forEach(function(valor_destino, chave_destino, mapa_destino) {
		//distancia_parsecs = calcula_distancia(false, estrela_atual, valor_destino.id_estrela);
		distancia_parsecs = "";
		html_lista = html_lista + valor_destino.nome_estrela +" "+  valor_destino.posicao_estrela +" "+ distancia_parsecs +"<br>";
	});
	
	div_lista_distancia.innerHTML = html_lista;
	
	return false;
}


/******************
function popula_selects_estrelas_frotas
--------------------
Popula os selects com as lista das estrelas onde uma nave do Império escolhido possa chegar
******************/
function popula_selects_estrelas_frotas() {
	let selects = document.getElementsByTagName('SELECT');
	let icone_pesquisa = '';
	
	let posicao_nave_na_lista = 0;
	for (let index=0; index < selects.length; index++) {
		if (selects[index].getAttribute('data-atributo') == 'id_estrela') {
			let alcance_nave = selects[index].getAttribute('data-alcance');
			let alcance_estendido = 2;
			let reabastece = true;
			let estrela_atual = id_estrela_atual[posicao_nave_na_lista]
			let estrelas_destino = array_estrelas(alcance_nave,alcance_estendido,reabastece,estrela_atual);
			//console.log('Index: '+index+'; '+estrela_atual);
			let alcance_local = selects[index].getAttribute('data-alcance-local');
			reabastece = false;
			alcance_estendido = 1;
			let estrelas_destino_local = array_estrelas(alcance_local,alcance_estendido,reabastece,estrela_atual);
			
			mapped_estrelas_destino_local = estrelas_destino_local.map(function(el, i) {
				return { index: i, value: el };
			});
			
			mapped_estrelas_destino_local.forEach(
			function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
				estrelas_destino[id_estrela_origem] = true;
			});						
				
			mapped_estrelas_buraco_de_minhoca = buracos_de_minhoca[posicao_nave_na_lista].map(function(el, i) {
				return { index: i, value: el };
			});
			
			mapped_estrelas_buraco_de_minhoca.forEach(
			function(valor_estrelas_imperio, id_estrela_origem, mapa_estrelas_imperio) {
				estrelas_destino[valor_estrelas_imperio.value] = true;
			});	
			
			var mapped_estrelas_destino = estrelas_destino.map(function(el, i) {
				return { index: i, value: el, id_estrela: i, nome_estrela: lista_nome_estrela[i], posicao_estrela: ' ('+lista_x_estrela[i]+';'+lista_y_estrela[i]+';'+lista_z_estrela[i]+')' };
			});	
			
			mapped_estrelas_destino.sort(function(firstEl, secondEl) {
				if (firstEl.nome_estrela.toLowerCase() < secondEl.nome_estrela.toLowerCase()) {
					return -1;
				}
				if (firstEl.nome_estrela.toLowerCase() > secondEl.nome_estrela.toLowerCase()) {
					return 1;
				}
				// a must be equal to b
				return 0;
			});
			
			html_lista = '';
			mapped_estrelas_destino.forEach(function(valor_destino, chave_destino, mapa_destino) {
				let selecionado = '';
				if (id_estrela_destino[posicao_nave_na_lista] == 0) {
					id_estrela_destino[posicao_nave_na_lista] = estrela_capital[id_imperio_atual];
				}
					if (valor_destino.id_estrela == id_estrela_destino[posicao_nave_na_lista]) {
					selecionado = 'selected';
				}
				if (id_estrela_pesquisa[valor_destino.id_estrela] == 1) {
					icone_pesquisa = '&#xf002; ';
				} else {
					icone_pesquisa = '';
				}
				
				if (id_estrela_nunca_visitada[valor_destino.id_estrela] == 1) {
					icone_nunca_visitado = '&#xf60b; ';
				} else {
					icone_nunca_visitado = '';
				}
				
				distancia = Math.ceil(calcula_distancia(false, estrela_atual, valor_destino.id_estrela));
				html_lista = html_lista + '<option value=\"'+valor_destino.id_estrela+'\" '+selecionado+'>'+ icone_nunca_visitado + icone_pesquisa + valor_destino.nome_estrela +' '+ valor_destino.posicao_estrela + ' - ' + distancia + 'pc</option>';
				
				//distancia[chave_destino] = true;
			});
			
			selects[index].innerHTML = html_lista;
			posicao_nave_na_lista++;
		}
	}
}

/******************
function destacar_recurso
--------------------
Destava todos os DIVs que tenham esse recurso
******************/
function destacar_recurso(objeto) {
	let post_message = objeto.parentNode.parentNode;
	let divs = post_message.getElementsByTagName("DIV");
	
	let recurso_selecionado = objeto.options[objeto.selectedIndex];
	for (let index=0; index<divs.length; index++) {
		if (divs[index].innerHTML.includes(recurso_selecionado.innerText) && divs[index].getAttribute("data-atributo") == "recurso_planeta" && recurso_selecionado.innerText != "") {
			divs[index].style.border = "2px solid #FF0000";
		} else if(divs[index].getAttribute("data-atributo") == "recurso_planeta") {
			divs[index].style.border = 'none';
		}
	}
}

/******************
function lista_techs_ocultas_html_(id=0)
--------------------
Cria a lista de Techs
id -- qual ID está selecionado
******************/
function lista_techs_ocultas_html(id=0) {
	let retorno = new Promise((resolve, reject) => {
		let dados_ajax = "post_type=POST&action=lista_techs_ocultas";
		resolve(processa_xhttp_resposta(dados_ajax));
	});
	
	return retorno.then((successMessage) => {
		let lista_nome=successMessage.lista_nome;
		let lista_id=successMessage.lista_id;
		
		let lista_select = document.createElement("select");
		lista_select.setAttribute("data-atributo","id_tech");
		lista_select.style.width = "100%";
		for (let index = 0; index < lista_nome.length; index++) {
			let select_option = document.createElement("option");
			select_option.value = lista_id[index]; 
			select_option.text = lista_nome[index];
			if (lista_id[index] == id) {
				select_option.selected = true;
			}
			lista_select.appendChild(select_option);
		}
		return lista_select;
	});

	//return retorno;
}

/******************
function abre_div_planetas()
--------------------
Abre o div com os planetas de uma estrela
id_estrela -- qual ID está selecionado
******************/
function abre_div_planetas(evento, objeto) {
	//let text_id_estrela = "id_estrela_" + id_estrela;
	//let div = document.getElementById(text_id_estrela);
	let div = objeto.nextElementSibling;
	//console.log(div);
	
	if (div !== null) {
		if (div.style.display == "none" || div.style.display == "") {
			div.style.display = "block";
		} else {
			div.style.display = "none";
		}
	}
}

/******************
function altera_tipo_estrela(id=0)
--------------------
Atualiza o tipo_estrela com o tipo da estrela selecionado
******************/
function altera_tipo_estrela(objeto) {
	let div_parent = pega_ascendente(objeto,"DIV");	
	let divs = div_parent.getElementsByTagName("DIV");
	
	for(let index=0; index < divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "tipo_estrela") {
			divs[index].innerHTML = "Estrela " + lista_tipo_estrela[objeto.options[objeto.selectedIndex].value];
			break;
		}
	}
}

/******************
function troca_classe_select(objeto)
--------------------
Troca a classe de um objeto
******************/
function troca_classe_select(objeto) {
	if (objeto.className != "select_lista_planetas") {
		let classes = objeto.className.split(" ");
		for (let index = 0; index < classes.length; index++) {
			if (classes[index] != "select_lista_planetas") {
				objeto.classList.remove(classes[index]);	
			}
		}
	}
	
	if (objeto.options[objeto.selectedIndex].className != "") {
		objeto.classList.add(objeto.options[objeto.selectedIndex].className);
	}
}

/******************
function altera_tipo_estrela(evento, id_planeta)
--------------------
Scroll para o planeta
******************/
function ir_para_planeta(evento, id_planeta) {
	let texto_id_planeta = 'planeta_' + id_planeta;
	let div_destino = document.getElementById(texto_id_planeta);
	
	console.log(texto_id_planeta);
	div_destino.scrollIntoView({behavior: "smooth", block: "center", inline: "start"});

	evento.preventDefault();
	return false;
}

/******************
function atualiza_lista_estrela_colonia(evento, objeto)
--------------------
Atualiza os dados X, Y e Z da Frota
******************/
function atualiza_lista_estrela_colonia(evento, objeto) {
	let div_tabela_frota = document.getElementById("tabela_frota");
	let divs = div_tabela_frota.getElementsByTagName("DIV");
	
	for (let index = 0; index<divs.length; index++) {
		if (div[index].getAttribute("data-atributo") == "X" || div[index].getAttribute("data-atributo") == "Y" || div[index].getAttribute("data-atributo") == "Z" ) {
			let texto_atributo = "data-" + div[index].getAttribute("data-atributo");
			div[index].innerHTML = objeto.getAttribute(texto_atributo); 
		}
	}
	
	evento.preventDefault();
	return false;
}

/******************
function atualiza_lista_estrela_colonia(evento, objeto)
--------------------
Atualiza os dados X, Y e Z da Frota
******************/
function atualiza_lista_estrela_colonia(evento, objeto) {
	let div_tabela_frota = document.getElementById("tabela_frota");
	let divs = div_tabela_frota.getElementsByTagName("DIV");
	
	for (let index = 0; index<divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "X" || divs[index].getAttribute("data-atributo") == "Y" || divs[index].getAttribute("data-atributo") == "Z" ) {
			let texto_atributo = "data-" + divs[index].getAttribute("data-atributo");
			let valor = objeto.options[objeto.selectedIndex].getAttribute(texto_atributo)
			divs[index].innerHTML = valor; 
			divs[index].setAttribute("data-valor-original", valor); 
		}
	}
	
	evento.preventDefault();
	return false;
}

/******************
function atualiza_nave_modelo(evento, objeto)
--------------------
Atualiza os dados de um Modelo de Nave
******************/
function atualiza_nave_modelo(evento, objeto) {
	let div_tabela_frota = document.getElementById("tabela_frota");
	let divs = div_tabela_frota.getElementsByTagName("DIV");
	let div_texto_nave = document.getElementById("texto_nave");
	let div_texto_custo = document.getElementById("texto_custo");
	
	for (let index = 0; index<divs.length; index++) {
		if (divs[index].getAttribute("data-atributo") == "string_nave") {
			let string_nave = objeto.options[objeto.selectedIndex].getAttribute("data-string-nave").replaceAll("\\","");
			divs[index].innerHTML = string_nave;
			divs[index].setAttribute("data-valor-original", string_nave);
		}
	}
	
	div_texto_nave.innerHTML = "<b>Dados da Nave:</b>" + objeto.options[objeto.selectedIndex].getAttribute("data-texto-nave");
	div_texto_custo.innerHTML = "<b>Custo:</b>" + objeto.options[objeto.selectedIndex].getAttribute("data-texto-custo");
	
	evento.preventDefault();
	return false;
}