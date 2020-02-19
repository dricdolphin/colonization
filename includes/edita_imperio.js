var edicao_objeto = false; //Define se está no modo de edição ou não

/******************
function novo_imperio
--------------------
Insere um novo Império na lista
******************/
function novo_imperio() {
	if (edicao_objeto) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		edicao_objeto = true; //Bloqueia a edição de outros Impérios
		var tabela_imperios = document.getElementById('tabela_imperios');
		var linha = tabela_imperios.insertRow(-1);
		var cell_1 = linha.insertCell(0);
		var cell_2 = linha.insertCell(1);
		var cell_3 = linha.insertCell(2);
		var cell_4 = linha.insertCell(3);
		
		//TODO - Pegar a lista de jogadores
		var lista_jogadores = lista_jogadores_html();
		
		cell_1.innerHTML = "<div>"+lista_jogadores+"</div><div><a href='#' onclick='salva_objeto();'>Salvar</a> | <span style='color: #DD0000;'><a href='#' onclick='cancela_edicao();'>Cancelar</a></span></div>";
		cell_2.innerHTML = "<input type='text' id='nome_imperio'></input>";
		cell_3.innerHTML = "";
		cell_4.innerHTML = "";
}	


//Essa função será populada pelo plugin
/******************
function lista_jogadores_html(id_imperio =0)
--------------------
Cria a lista de jogadores
id_imperio = 0 -- define qual jogador está selecionado
******************
function lista_jogadores_html(id_imperio = 0) {
	//TODO -- Pegar a lista de jogadores do WordPress
	var $html = "<select id='id_jogador'>"
		+"<option value='imperio_1'>XPTO 1</option>"
		+"<option value='imperio_2'>XPTO 2</option>"
		+"<option value='imperio_3'>XPTO 3</option>"
		+"<option value='imperio_4'>XPTO 4</option>"
		+"</select>";
		
	return $html;
}
***/


/******************
function cancela_edicao
--------------------
Cancela a edição do objeto
******************/	
function cancela_edicao() {
	edicao_objeto = false;
	
	var tabela_imperios = document.getElementById('tabela_imperios');
	tabela_imperios.deleteRow(-1);
}

/******************
function edita_imperio(id_imperio)
--------------------
Edita um Império específico
******************/	
function edita_imperio(id_imperio) {
	if (edicao_objeto) {
		alert('Já existe um objeto em edição!');
		return false;
	}
	
	edicao_objeto = true;
	var linha_imperio = document.getElementById('imperio_'+id_imperio);
	var cell_1 = linha_imperio.cells[0];
	var cell_2 = linha_imperio.cells[1];
	
	var divs = linha_imperio.cells[0].getElementsByTagName('div');
	
	var nome_jogador = divs[0].innerHTML;
	var nome_imperio = cell_2.innerHTML;
	
	cell_1.innerHTML = "<div>"+nome_jogador+"</div><div><a href='#' onclick='salva_objeto("+id_imperio+");'>Salvar</a> | <span style='color: #DD0000;'><a href='#' onclick='this.disabled=true; salva_objeto("+id_imperio+",true);'>Cancelar</a></span></div>";
	cell_2.innerHTML = "<input type='text' id='nome_imperio' value='"+nome_imperio+"'></input><input type='hidden' id='nome_imperio_original' value='"+nome_imperio+"'></input>";
}

/******************
function excluir_objeto(id_imperio)
--------------------
Exclui um Império escolhido
id_imperio -- id do Império a ser deletado
******************/	
function excluir_objeto(id_imperio) {
	if (edicao_objeto) {
		alert('Não é possível deletar um objeto enquanto outro está em edição!');
		return false;
	}
	
	var linha_imperio = document.getElementById('imperio_'+id_imperio);
	var nome_imperio = linha_imperio.cells[1].innerHTML
	var confirma = confirm('Tem certeza que deseja deletar o Império "'+nome_imperio+'"?');
	//Se for mesmo deletar, remove a linha
	if (confirma) {
		//Envia a chamada de AJAX para remover o usuário
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (this.responseText == "DELETADO!") {
					linha_imperio.remove(); //Remove a linha
					edicao_objeto = false;
				} else {
					alert(this.responseText);
					edicao_objeto = false;
				}
			}
		};
		xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhttp.send("post_type=POST&action=deleta_imperio&tabela=colonization_imperio&id_jogador="+id_imperio+"&where_clause=id_jogador&where_value="+id_imperio);
		edicao_objeto = true;
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
	linha_imperio.cells[0].innerHTML = "<div>"+nome_jogador+"</div><div><a href='#' onclick='edita_imperio("+id_imperio+");'>Editar</a> | <a href='#' onclick='excluir_objeto("+id_imperio+");'>Excluir</a></div>";
	linha_imperio.cells[1].innerHTML = nome_imperio;
	linha_imperio.cells[2].innerHTML = populacao;
	linha_imperio.cells[3].innerHTML = pontuacao;
}

/******************
function salva_objeto(id_imperio = 0)
--------------------
Salva o Império sendo editado.
id_imperio = 0 -- id do Império sendo editado. Se for 0, então é um novo Império
cancela = false -- Define se é para salvar ou apenas cancelar a edição
******************/	
function salva_objeto(id_imperio = 0, cancela = false) {
	if (id_imperio == 0) {//Caso esteja salvando um Império novo
		var tabela_imperios = document.getElementById('tabela_imperios');
		var ultima_coluna = tabela_imperios.rows.length - 1;
		var linha_imperio = tabela_imperios.rows[ultima_coluna]; //O Império novo sempre fica na última linha da tabela
		
		var id_jogador = lista_jogadores.options[lista_jogadores.selectedIndex].value;	
		var nome_jogador = lista_jogadores.options[lista_jogadores.selectedIndex].text;
		var nome_imperio = document.getElementById('nome_imperio').value;
		id_imperio = id_jogador.substr(8); //O ID do Império é o mesmo do ID do jogador
	} else {
		var linha_imperio = document.getElementById('imperio_'+id_imperio);
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
		edicao_objeto = false;		
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

				edicao_objeto = false;
			} else {
				alert(this.responseText);
			}
		}
	};
	xhttp.open("POST", ajaxurl, true); //A variável "ajaxurl" contém o caminho que lida com o AJAX no WordPress
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("post_type=POST&action=salva_objeto&tabela=colonization_imperio&id_jogador="+id_imperio+"&nome="+nome_imperio+"&where_clause=id_jogador&where_value="+id_imperio);
	edicao_objeto = true;
}