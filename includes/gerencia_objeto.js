/******************
function gerenciar_objeto(objeto)
--------------------
Abre a página de gerenciamento de informações acessórias de um objeto
objeto -- objeto sendo editado
******************/
function gerenciar_objeto(objeto) {
	var linha=pega_ascendente(objeto,"TR");;
	var inputs=linha.getElementsByTagName("INPUT");
	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	var url_gerencia = window.location.href+"&id="+id_objeto;
	
	window.location.assign(url_gerencia);
}