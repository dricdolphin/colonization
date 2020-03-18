/******************
function gerenciar_objeto(objeto)
--------------------
Abre a página de gerenciamento de informações acessórias de um objeto
objeto -- objeto sendo editado
redireciona -- Se é para redirecionar para outra página
******************/
function gerenciar_objeto(evento, objeto, redireciona = "") {
	//alert(typeof objeto);
	var linha=pega_ascendente(objeto,"TR");;
	var inputs=linha.getElementsByTagName("INPUT");
	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	var vars = window.location.href.split("?");
	
	if (redireciona == "") {
		redireciona = vars[1];
	}
	
	var url_gerencia = vars[0]+"?"+redireciona+"&id="+id_objeto;
	url_gerencia = url_gerencia.replace("#","");
	window.location = url_gerencia;
	
	evento.preventDefault();
	return false;
}

/******************
function copia_objeto(objeto)
--------------------
Copia um objeto na última linha
objeto -- objeto sendo editado
******************/
function copiar_objeto(evento, objeto, id_imperio) {
	var tabela = document.getElementsByTagName('TABLE');
	
	for (var index_tabelas = 0; index_tabelas < tabela.length; index_tabelas++) {
		if (tabela[index_tabelas].getAttribute('data-id-imperio') == id_imperio) {
			tabela = tabela[index_tabelas];
			break;
		}
	}
	
	var linha = pega_ascendente(objeto,"TR");;
	var celulas = linha.getElementsByTagName("TD");
	var inputs = [];
	
	var linha_nova = tabela.insertRow(-1);
	
	var celula = linha_nova.insertCell(0); //A primeira célula é diferente!
	var nome = celulas[0].getElementsByTagName("DIV");
	nome = nome[0].innerHTML;
	
	celula.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'>"+nome+"</div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	
	for (var index = 1; index < celulas.length; index++) {
		celula = linha_nova.insertCell(index);
		celula.innerHTML = celulas[index].innerHTML;
		inputs = celula.getElementsByTagName("INPUT");
		
		/*
		for (var index_input = 0; index_input < inputs.length; index_input++) {
			if (inputs[index_input].getAttribute('data-atributo') == "id" || inputs[index_input].getAttribute('data-atributo') == "where_value") {
				inputs[index_input].value = "";
				inputs[index_input].setAttribute('data-valor-original',"");
			}
		}*/
	}

	
	var retorno = edita_objeto(evento, celula);
	
	celulas = linha_nova.getElementsByTagName("TD");
	celulas[0].innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
	+"<input type='hidden' data-atributo='id_imperio' data-ajax='true' data-valor-original='"+id_imperio+"' value='"+id_imperio+"'></input>"
	+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
	+"<input type='hidden' data-atributo='where_value' value=''></input>"
	+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
	+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir esta Frota?'></input>"
	+"<div data-atributo='nome' data-editavel='true' data-valor-original='' data-style='width: 100px;'><input type='text' data-atributo='nome' data-ajax='true' style='width: 100px;' value='"+nome+"'></input></div>"
	+"<div><a href='#' onclick='return salva_objeto(event, this);'>Salvar</a> | <a href='#' onclick='return cancela_edicao(event, this);'>Cancelar</a></div>";
	
	evento.preventDefault();
	return false;
}

function desbloquear_turno(evento) {
	var confirma = confirm("Tem certeza que deseja desbloquear o Turno?");

	
}