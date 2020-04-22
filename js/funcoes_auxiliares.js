/******************
function pega_id_estrela
--------------------
Pega o id de uma estrela e define suas variaveis
******************/
function calcula_distancia() {
	let estrela_origem = document.getElementById('estrela_origem');
	let estrela_destino = document.getElementById('estrela_destino');
	let distancia_div = document.getElementById('distancia');

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
	
	distancia = Math.ceil(Math.sqrt(Math.pow((estrela_origem_x-estrela_destino_x),2)+Math.pow((estrela_origem_y-estrela_destino_y),2)+Math.pow((estrela_origem_z-estrela_destino_z),2))*10)/10;
	
	distancia_div.innerHTML = "<b>Distância:</b> "+distancia.toFixed(1);
}

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