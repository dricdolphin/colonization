var objeto_em_edicao = false; //Define se está no modo de edição ou não

/******************
function novo_imperio
--------------------
Insere um novo Império na lista
******************/
function novo_imperio() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela_imperios = document.getElementById('tabela_imperios');
		var linha_nova = tabela_imperios.insertRow(-1);
		var dados_jogador = linha_nova.insertCell(0);
		var nome_imperio = linha_nova.insertCell(1);
		var populacao = linha_nova.insertCell(2);
		var pontuacao = linha_nova.insertCell(3);
		
		var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
		
		dados_jogador.innerHTML = "<div>"+lista_jogadores+"</div><div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <span style='color: #DD0000;'><a href='#' onclick='cancela_edicao(this);'>Cancelar</a></span></div>";
		nome_imperio.innerHTML = "<input type='text' id=\"dados_objeto['nome_imperio']\"></input>";
		populacao.innerHTML = "";
		pontuacao.innerHTML = "";
}	


/******************
function cancela_edicao
--------------------
Cancela a edição do objeto
******************/	
function cancela_edicao(objeto) {
	objeto_em_edicao = false;
	
	var parent_node = objeto.parentNode;
	//Retroage até achar a tabela
	while(parent_node.tagName != "TABLE") {
		parent_node = parent_node.parentNode;
	}
	
	var tabela_objetos = parent_node;
	tabela_objetos.deleteRow(-1);
}

/******************
function edita_objeto(id_linha)
--------------------
Edita um objeto
******************/	
function edita_objeto(linha) {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
	
	objeto_em_edicao = true;
	var parent_node = linha.parentNode;
	//Retroage até achar a linha
	while(parent_node.tagName != "TR") {
		parent_node = parent_node.parentNode;
	}

	var linha = parent_node;
	var celulas = linha.cells;
	
	//Pega cada uma das células e altera para o modo de edição, caso seja editável
	for (index = 0; index < celulas.length; index++) {
		console.log("index="+index);
		celula = celulas[index];
		divs = celula.getElementsByTagName('div');
		inputs_hidden = celula.getElementsByTagName('input');
		if (index == 0) { //A linha 0 é tratada à parte
			//A primeira linha normalmente contém um dado (usualmente o nome do objeto) e um "div" com as opções de "Editar" e "Deletar" o objeto, que será alterado para "Salvar" e "Cancelar" a edição
			atributo = inputs_hidden[0].id;
			valor_atributo = divs[0].innerHTML;
			celula.innerHTML = "<div>"+valor_atributo+"</div><div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <span style='color: #DD0000;'><a href='#' onclick='salva_objeto(this,true);'>Cancelar</a></span></div>";
		} else {
			divs = celula.getElementsByTagName('div');
			inputs_hidden = celula.getElementsByTagName('input');
			console.log(inputs_hidden);
			if (typeof inputs_hidden.length > 0) { //Se tem um input, então é editável.
				atributo = inputs_hidden[0].id;
				valor_atributo = divs[0].innerHTML;
				if (inputs_hidden[0].value == "editavel") {
					celula.innerHTML = "<div><input type='text' id='"+atributo+"' value='"+valor_atributo+"'></input><input type='hidden' id='valor_original' value='"+valor_atributo+"'></input></div>";
				}
			}
		}	
	}

}

/******************
function confirma_excluir_imperio(nome_imperio)
--------------------
Mensagem de confirmação ao excluir um Império
nome_imperio - nome do Império
******************/	
function confirma_excluir_imperio(nome_imperio) {
	return confirm('Tem certeza que deseja deletar o Império "'+nome_imperio+'"?');
}

/******************
function excluir_objeto(id_imperio)
--------------------
Exclui um objeto
id_objeto - id do objeto
******************/	
function excluir_objeto(objeto, funcao_confirmacao) {
	if (objeto_em_edicao) {
		alert('Não é possível deletar um objeto enquanto outro está em edição!');
		return false;
	}
	
	var linha_objeto = document.getElementById(id_objeto);
	
	var nome_imperio = linha_imperio.cells[1].innerHTML
	var confirma = funcao_confirmacao.apply(this,valor_confirma); 
	//Se for mesmo deletar, remove a linha
	if (confirma) {
		//Envia a chamada de AJAX para remover o usuário
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (this.responseText == "DELETADO!") {
					linha_imperio.remove(); //Remove a linha
					objeto_em_edicao = false;
				} else {
					alert(this.responseText);
					objeto_em_edicao = false;
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("post_type=POST&action=deleta_imperio&tabela=colonization_imperio&id_jogador="+id_objeto+"&where_clause=id_jogador&where_value="+id_objeto);
		objeto_em_edicao = true;
	}

}

/******************
function desabilita_edicao_imperio(linha_imperio, id_imperio, nome_jogador, populacao, pontuacao)
--------------------
Desabilita os objetos de edição e atualiza os dados
linha_imperio = linha da tabela com os dados do Império
id_imperio = id do Império 
nome_jogador = nome do jogador 
nome_imperio = nome do Império
populacao = população do Império 
pontuacao = pontuação do Império
******************/	
function desabilita_edicao_imperio(linha_imperio, id_imperio, nome_jogador, nome_imperio, populacao, pontuacao) {
	linha_imperio.setAttribute("id", "imperio_"+id_imperio);
	linha_imperio.cells[0].innerHTML = "<div>"+nome_jogador+"</div><div><a href='#' onclick='edita_objeto(this);'>Editar</a> | <a href='#' onclick='excluir_objeto(this);'>Excluir</a></div>";
	linha_imperio.cells[1].innerHTML = nome_imperio;
	linha_imperio.cells[2].innerHTML = populacao;
	linha_imperio.cells[3].innerHTML = pontuacao;
}

/******************
function salva_objeto(id_objeto = 0)
--------------------
Salva o Império sendo editado.
id_objeto = 0 -- id do Império sendo editado. Se for 0, então é um novo Império
cancela = false -- Define se é para salvar ou apenas cancelar a edição
******************/	
function salva_objeto(id_objeto = 0, cancela = false) {
	if (id_objeto == 0) {//Caso esteja salvando um Império novo
		var tabela_objetos = document.getElementById('tabela_imperios');
		var ultima_coluna = tabela_objetos.rows.length - 1;
		var linha_objeto = tabela_objetos.rows[ultima_coluna]; //Um objeto novo sempre fica na última linha
		
		var dados_linha
		var id_jogador = lista_jogadores.options[lista_jogadores.selectedIndex].value;	
		var nome_jogador = lista_jogadores.options[lista_jogadores.selectedIndex].text;
		var nome_imperio = document.getElementById('nome_imperio').value;
		id_objeto = id_jogador.substr(8); //O ID do Império é o mesmo do ID do jogador
	} else {
		var linha_imperio = document.getElementById('imperio_'+id_objeto);
		var divs = linha_imperio.cells[0].getElementsByTagName('div');

		//Armazena os dados do Império, para o caso do jogador decidir cancelar a edição
		var id_jogador = id_imperio;
		var nome_jogador = divs[0].innerHTML;
		var nome_imperio = document.getElementById('nome_imperio').value;

		var populacao = linha_imperio.cells[2].innerHTML;
		var pontuacao = linha_imperio.cells[3].innerHTML;
	}

	if (cancela) {
		nome_imperio = document.getElementById('nome_imperio_original').value; //Se for para cancelar a edição, mantém os dados originais
		var desabilita = desabilita_edicao_imperio(linha_imperio, id_imperio, nome_jogador, nome_imperio, populacao, pontuacao);
		objeto_em_edicao = false;		
		return false;
	}

	//Valida os dados

	//Verifica se o jogador já tem um Império cadastrado. Cada jogador pode ter apenas um Império.
	var imperio_existe = document.getElementById(id_jogador);
	if (imperio_existe !== null) {
		alert('O jogador selecionado já tem um Império cadastrado! Por favor, escolha outro jogador!');
		return false;
	}
	//Verifica se o nome do Império está preenchido
	if (nome_imperio == "") {
		alert('O nome do Império não pode ser deixado em branco!');
		return false;
	}

	//TODO -- Calcula População e Pontuação
	var populacao = 999;
	var pontuacao = 999;

	//Envia a chamada de AJAX para remover o usuário
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText == "SALVO!") {
				//Após salvar os dados, remove os "inputs" e transforma a linha em texto, deixando o Império passível de ser editado
				desabilita_edicao_imperio(linha_imperio, id_imperio, nome_jogador, nome_imperio, populacao, pontuacao);

				objeto_em_edicao = false;
			} else {
				alert(this.responseText);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("post_type=POST&action=salva_objeto&tabela=colonization_imperio&id_jogador="+id_imperio+"&nome="+nome_imperio+"&where_clause=id_jogador&where_value="+id_imperio);
	objeto_em_edicao = true;
}