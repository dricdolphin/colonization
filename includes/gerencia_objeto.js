/******************
function gerenciar_objeto(objeto)
--------------------
Abre a página de gerenciamento de informações acessórias de um objeto
objeto -- objeto sendo editado
******************/
function gerenciar_objeto(objeto) {
	//alert(typeof objeto);
	var linha=pega_ascendente(objeto,"TR");;
	var inputs=linha.getElementsByTagName("INPUT");
	
	for (var index = 0; index < inputs.length; index++) {
		if (inputs[index].getAttribute('data-atributo') == "id") {
			var id_objeto = inputs[index].value;
		}
	}
	
	var url_gerencia = window.location.href+"&id="+id_objeto;
	url_gerencia = url_gerencia.replace("#","");
	window.location = url_gerencia;
}