/******************
function pega_estrelas_ajax()
--------------------
Pega as estrelas do banco de dados
******************/	
function pega_estrelas_ajax() {
	var dados_ajax = "post_type=POST&action=lista_estrelas";

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			var resposta = JSON.parse(this.responseText);
			console.log(resposta);
			carrega_dados_estrela(resposta);
			desenha_mapa_estelar();
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(dados_ajax);

	return false;
}


/***
function processa_estrelas(evento)
-------------
Cria as estelas

***/
function processa_estrelas(evento) {
	let dim_X = document.getElementById("dim_X").value;
	let dim_Y = document.getElementById("dim_Y").value;
	let dim_Z = document.getElementById("dim_Z").value;
 
	let num_estrelas = document.getElementById("num_estrelas").value;
	let pares = retorna_pares_unicos(dim_X,dim_Y,num_estrelas);
	let estrelas = [];
	let nomes_estrela = [];
	let pares_string = "";
	data_externa = [];
	pares.forEach(element => {
		let nome_estrela = "";
		do {
			nome_estrela = nome_aleatorio(); 
		} while (nomes_estrela.find(element => element == nome_estrela) !== undefined)
		nomes_estrela.push(nome_estrela);
		let string_x = element.split(",")[0];
		let string_y = element.split(",")[1];
		let string_z = randomInt(0, dim_Z);
		let tipo_estrela_string = tipo_estrela();
		estrelas.push(
			{
				nome: nome_estrela,
				tipo: tipo_estrela_string,
				X: string_x,
				Y: string_y,
				Z: string_z
			}
		);
		pares_string = pares_string + "INSERT INTO colonization_estrela SET nome='" + nome_estrela + "', tipo='" + tipo_estrela_string +"', X="+ string_x + ", Y=" + string_y + ", Z=" + string_z +";<br>";
	});
	
	//let ponto_estrela = [0, 0, 'Estrela', 'point { size: 1; fill-color: white; }', 'Estrela (0,0,0) \n Branca'];
	//data_externa.push(ponto_estrela);
	carrega_dados_estrela(estrelas);
	drawChart();
	document.getElementById("resultados").innerHTML = pares_string;
	evento.preventDefault();
}

/***
function carrega_dados_estrela(array_estrelas)

Carrega os dados das estrelas na array que será usada pelo Google Charts
***/
function carrega_dados_estrela(array_estrelas) {
	array_estrelas.forEach(element => {
		let string_style = 'point { size: ' + tamanho_estrela_em_pontos(element.tipo) + '; fill-color:' + cor_estrela_RGB(element.tipo)+ '; }';
		let ponto_estrela = [
			element.X*1, element.Y*1, 
			element.nome, 
			string_style,
			element.nome + ' (' + element.X + ',' + element.Y + ',' + element.Z + ')' + '\n' + element.tipo 
		];
		data_externa.push(ponto_estrela);
	});
}


/***
retorna_pares_unicos (x, y, num_pares)
-------------
Retorna um array com pares X e Y sem duplicação

X e Y -- tamanho do array
num_pares -- qtd de pares a serem retornados
***/
function retorna_pares_unicos(x, y, num_pares) {
	//Limita o número de pares, se necessário
	if (num_pares > x*y) {
		num_pares = x*y;
	}

	let matrix_escolhidos =  [];
	for (let index = 0; index <= x; index++) {
		matrix_escolhidos[index] = [];
	}
	
	let resultado = [];

	for (let index = 0; index < num_pares; index++) {
		let x_resultado = 0;
		let y_resultado = 0;
		do {
			x_resultado = randomInt(0, x);
			y_resultado = randomInt(0, y);
			//console.log (matrix_escolhidos[x_resultado][y_resultado])
		} while (matrix_escolhidos[x_resultado][y_resultado] !== undefined)
		matrix_escolhidos[x_resultado][y_resultado] = true;
		let resultado_string = x_resultado + "," + y_resultado;		
		resultado.push(resultado_string)
	}
	
	return resultado;
}

/***
Function nome_aleatorio()
-------------

Retorna um nome aleatório para uma estrela, com até 3 palavras
***/
function nome_aleatorio() {
	//Nomes que só podem ser usados em estrelas com DOIS ou TRÊS nomes
	let lista_nomes_prefixos = [
		"Alpha",
		"Beta",
		"Gamma",
		"Epsilon",
		"Zeta",
		"Iota",
		"Kappa",
		"Mu",
		"Omicron",
		"Rho",
		"Sigma",
		"Tau"
	];
	
	let lista_nomes_proprios = [
		"Crux",
		"Eridanus",
		"Kalleph",
		"Leo",
		"Draconnis",
		"Taurini",
		"Lyra",
		"Cygnus",
		"Corvus",
		"Cepheus",
		"Gemini",
		"Auriga",
		"Scorpius",
		"Corona",
		"Borealis",
		"Vela",
		"Aquila",
		"Triangulum",
		"Australe",
		"Cetus",
		"Ophiuchus",
		"Fornax",
		"Deneb",
		"Diadem",
		"Geminorom",
		"Apus",
		"Mintaka",
		"Manorum",
		"Magellan",
		"Volar",
		"Farfal",
		"Balthus",
		"Duor",
		"Piscis",
		"Majoris",
		"Minoris",
		"Sagitari",
		"Gomide",
		"Natron",
		"Larpa",
		"Oriane",
		"Remus",
		"Parlate",
		"Ellorum",
		"Maxima",
		"Ioni",
		"Capella",
		"Dolore",
		"Cephei",
		"Kolm"
	];
	
	let lista_total = lista_nomes_prefixos.concat(lista_nomes_proprios);
	//console.log(lista_total.length);

	let chances = randomInt(0,100);
	let index_inicio = randomInt(0, lista_total.length-1);
	let index_meio = randomInt(0, lista_nomes_proprios.length-1);
	let index_fim = randomInt(0, lista_total.length-1);


	if (chances < 10) {//Nome com uma só palavra
		return lista_nomes_proprios[index_meio];
	} else if (chances > 95) {//Nome com três palavras
		return lista_total[index_inicio] + " " + lista_nomes_proprios[index_meio] + " " + lista_total[index_fim];
	} else {//Nome com duas palavras
		return lista_total[index_inicio] + " " + lista_nomes_proprios[index_meio];
	}
}


/***
Function tipo_estrela()
-------------

Retorna o tipo da estrela
***/
function tipo_estrela() {
	let tipo_estrela = [
		"Vermelha",
		"Laranja",
		"Amarela",
		"Azulada",
		"Azul",
		"Branca",
		"Anã Branca"
	];
	
	let tamanho_estrela = [
		"Sub-Anã",
		"Anã",
		"Pequena",
		"",
		"Grande",
		"Sub-Gigante",
		"Gigante",
		"Super Gigante"
	];
	
	let probabilidade_estrela = [
		20,
		20,
		30,
		10,
		5,
		5,
		10
	];
	
	let probabilidade_tamanho = [];
	
	probabilidade_tamanho[0] = [
		0,
		0,
		5,
		25,
		10,
		10,
		40,
		10
	];
	
	probabilidade_tamanho[1] = [
		0,
		1,
		10,
		20,
		39,
		10,
		10,
		10
	];
	
	probabilidade_tamanho[2] = [
		0,
		5,
		20,
		25,
		30,
		19,
		1,
		0
	];
	
	probabilidade_tamanho[3] = [
		5,
		5,
		10,
		45,
		20,
		10,
		5,
		0
	];

	probabilidade_tamanho[4] = [
		5,
		10,
		10,
		55,
		15,
		5,
		0,
		0
	];

	probabilidade_tamanho[5] = [
		15,
		0,
		50,
		35,
		0,
		0,
		0,
		0
	];

	probabilidade_tamanho[6] = [
		0,
		0,
		0,
		100,
		0,
		0,
		0,
		0
	];	
	
	let estrela_string = "";
	let pega_estrela = popula_array(tipo_estrela,probabilidade_estrela);
	let tipo_estrela_string = pega_estrela[randomInt(0,99)];
	
	let index_estrela = tipo_estrela.findIndex(element => element == tipo_estrela_string);
	let pega_tamanho_estrela = popula_array(tamanho_estrela,probabilidade_tamanho[index_estrela]);
	let tamanho_estrela_string = pega_tamanho_estrela[randomInt(0,99)];
	
	estrela_string = tipo_estrela_string + " " + tamanho_estrela_string;
	
	return estrela_string.trim();
}

/***
Function tamanho_estrela_em_pontos(tipo_estrela)
-------------
Define o tamanho da estrela em pontos

tipo_estrela -- tipo da estrela
***/
function tamanho_estrela_em_pontos(tipo_estrela) {
	let tamanho_estrela = [
		"Sub-Anã",
		"Anã",
		"Pequena",
		"",
		"Grande",
		"Sub-Gigante",
		"Gigante",
		"Super Gigante"
	];

	let tamanho_estrela_pontos = 4;
	tamanho_estrela.forEach(function (element, index) {
		if (element !== "") {
			if (tipo_estrela.search(element) !== -1) {
				tamanho_estrela_pontos = index+1;
			}
		}
	});
	
	return tamanho_estrela_pontos;
}


function cor_estrela_RGB(tipo_estrela) {
	let cor_estrela = [
		"Vermelha",
		"Laranja",
		"Amarela",
		"Azulada",
		"Azul",
		"Branca",
		"Anã Branca"
	];	
	
	let cor_RGB = [
		'#CC0000',
		'#FF7B00',
		'#FFCE00',
		'#00DEFF',
		'#61FFFF',
		'#EEE',
		'#FFF'
	]
	
	let index_cor_RGB = 3;
	let cor_estrela_RGB = '#EEE';
	cor_estrela.forEach(function (element, index) {
		if (tipo_estrela.search(element) !== -1) {
				index_cor_RGB = index;
			}
	});
	
	return cor_RGB[index_cor_RGB];
}

/***
Function popula_array(array, array_probabilidade)
-------------

Popula uma array para pegar um valor baseado na probabilidade

array -- array com os dados
array_probabilidade -- array com a probabilidade
***/
function popula_array(array, array_probabilidade) {
	let array_preparada = [];
	
	array.forEach(function(element, index) {
		for (let probabilidade = 0; probabilidade < array_probabilidade[index]; probabilidade++) {
			array_preparada.push(element);
		}
	});
	
	return array_preparada;
}

/***
Function randomInt(min, max)
-------------

min e max -- argumentos do valor máximo e mínimo
***/
function randomInt(min, max) {
	return min + Math.floor((max - min) * Math.random());
}