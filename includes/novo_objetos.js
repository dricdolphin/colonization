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
		var tabela_imperios = document.getElementsByTagName('TABLE');
		tabela_imperios = tabela_imperios[0];
		var linha_nova = tabela_imperios.insertRow(-1);
		var dados_jogador = linha_nova.insertCell(0);
		var nome_imperio = linha_nova.insertCell(1);
		var populacao = linha_nova.insertCell(2);
		var pontuacao = linha_nova.insertCell(3);
		
		var lista_jogadores = lista_jogadores_html(); //Pega a lista de usuários do Fórum
		
		dados_jogador.innerHTML = "<input type='hidden' data-atributo='id_jogador' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id_jogador'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_imperio'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value=''></input>"
		+"<div data-atributo='nome_jogador'>"
		+lista_jogadores+"</div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		nome_imperio.innerHTML = "<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>";
		populacao.innerHTML = "<div></div>";
		pontuacao.innerHTML = "<div></div>";
}

/******************
function nova_estrela
--------------------
Insere uma nova estrela na lista
******************/
function nova_estrela() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela_estrelas = document.getElementsByTagName('TABLE');
		tabela_estrelas = tabela_estrelas[0];
		var linha_nova = tabela_estrelas.insertRow(-1);
		
		var nome_estrela = linha_nova.insertCell(0);
		var estrela_x = linha_nova.insertCell(1);
		var estrela_y = linha_nova.insertCell(2);
		var estrela_z = linha_nova.insertCell(3);
		var estrela_tipo = linha_nova.insertCell(4);
		
		nome_estrela.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_estrela'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		estrela_x.innerHTML = "<div data-atributo='X' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='X' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_y.innerHTML = "<div data-atributo='Y' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Y' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_z.innerHTML = "<div data-atributo='Z' data-style='width: 100%;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='Z' data-ajax='true' style='width: 100%;'></input></div>";
		estrela_tipo.innerHTML = "<div data-atributo='tipo' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tipo' data-ajax='true'></input></div>";
}

/******************
function novo_planeta
--------------------
Insere um novo Planeta na lista
******************/
function novo_planeta() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros objetos
		var tabela = document.getElementsByTagName('TABLE')[0];
		var linha_nova = tabela.insertRow(-1);
		var nome = linha_nova.insertCell(0);
		var estrela = linha_nova.insertCell(1);
		var posicao = linha_nova.insertCell(2);
		var classe = linha_nova.insertCell(3);
		var subclasse = linha_nova.insertCell(4);
		var tamanho = linha_nova.insertCell(5);
		
		var lista_estrelas = lista_estrelas_html(); //Pega a lista de usuários do Fórum
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='id_estrela' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<input type='hidden' data-atributo='mensagem_exclui_objeto' value='Tem certeza que deseja excluir este planeta e todas suas ligações (recursos, instalações etc)?'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		estrela.innerHTML = "<div data-atributo='nome_estrela'>"+lista_estrelas+"</div>";
		posicao.innerHTML = "<div data-atributo='posicao' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='posicao' data-ajax='true' style='width: 30px;'></input></div>";
		classe.innerHTML = "<div data-atributo='classe' data-editavel='true' data-valor-original=''><input type='text' data-atributo='classe' data-ajax='true'></input></div>";
		subclasse.innerHTML = "<div data-atributo='subclasse' data-editavel='true' data-valor-original=''><input type='text' data-atributo='subclasse' data-ajax='true'></input></div>";
		tamanho.innerHTML = "<div data-atributo='tamanho' data-style='width: 30px;' data-editavel='true' data-valor-original=''><input type='text' data-atributo='tamanho' data-ajax='true' style='width: 30px;'></input></div>";		
}


/******************
function novo_recurso
--------------------
Insere um novo recurso
******************/
function novo_recurso() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela = document.getElementsByTagName('TABLE');
		tabela = tabela[0];
		var linha_nova = tabela.insertRow(-1);
		
		var nome = linha_nova.insertCell(0);
		var descricao = linha_nova.insertCell(1);
		var acumulavel = linha_nova.insertCell(2);
		
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
		acumulavel.innerHTML = "<div data-atributo='acumulavel' data-type='checkbox' data-editavel='true' data-valor-original='1'><input type='checkbox' data-atributo='acumulavel' data-ajax='true' checked></input></div>";
}

/******************
function nova_instalacao
--------------------
Insere uma nova instalação
******************/
function nova_instalacao() {
	if (objeto_em_edicao) {
		alert('Já existe um objeto em edição!');
		return false;
	}
		
		objeto_em_edicao = true; //Bloqueia a edição de outros Impérios
		var tabela = document.getElementsByTagName('TABLE');
		tabela = tabela[0];
		var linha_nova = tabela.insertRow(-1);
		
		var nome = linha_nova.insertCell(0);
		var descricao = linha_nova.insertCell(1);
		var gerencia = linha_nova.insertCell(2);
		
		var url_atual = window.location.href;
		console.log(url_atual);
				
		nome.innerHTML = "<input type='hidden' data-atributo='id' data-valor-original='' value=''></input>"
		+"<input type='hidden' data-atributo='where_clause' value='id'></input>"
		+"<input type='hidden' data-atributo='where_value' value=''></input>"
		+"<input type='hidden' data-atributo='funcao_validacao' value='valida_generico'></input>"
		+"<div data-atributo='nome' data-editavel='true' data-valor-original=''><input type='text' data-atributo='nome' data-ajax='true'></input></div>"
		+"<div><a href='#' onclick='salva_objeto(this);'>Salvar</a> | <a href='#' onclick='cancela_edicao(this);'>Cancelar</a></div>";
		descricao.innerHTML = "<div data-atributo='descricao' data-editavel='true' data-valor-original=''><input type='text' data-atributo='descricao' data-ajax='true'></input></div>";
		gerencia.innerHTML = "<div data-atributo='gerenciar' data-valor-original=''>&nbsp;</div>";
}