<?php

	// CORS - Servidor define quais domínios diferentes podem acessar recursos da aplicação. Neste exemplo, qualquer domínio (*)
	header("Access-Control-Allow-Origin: *");
	
	// CORS - Servidor define os métodos permitidos
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
	
	// Define o tipo do retorno
	header("Content-Type: application/json; charset=UTF-8");

	// Api1.php
    // Manter um cadastro de alunos

	/*
	URL+Método e status code
	=======================

	Consultar objeto
	----------------
	Método: Get (um ou vários objetos)
	URL: /recurso		Todos
	URL: /recurso/id 	Um
	200 ok
	404 not found

	Criar objeto
	------------
	Método: Post
	URL: /recurso
	201 create
	503 service unvailable
	400 bad request - dados inválidos

	Atualizar objeto
	----------------
	Método: Put
	URL: /recurso
	200 ok
	503 service unvaliable
	400 bad request - dados inválidos

	Excluir objeto
	--------------
	Método: Delete
	URL: /recurso
	200 ok
	503 service unvaliable
	*/

	const hostDb = "mysql:host=localhost;dbname=ExemploAPI";
  	const usuario = "root";
  	const senha = "";

	if ($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		// select - ler todos os alunos da tabela alunos e enviar na resposta

		// simulando acesso ao banco de dados
		//$alunos = [ ['id'=>1,'nome'=>'joao'] , ['id'=>2,'nome'=>'maria'] ];

		// Instancia objeto PDO e conecta no BD
		$pdo = new PDO(hostDb,usuario,senha);

		// Configura o comportamento no caso de erros: levanta exceção.
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try{

			//throw(new Exception('xxx')); // forçar falha

			$cmm = $pdo->prepare('select id, nome from aluno');

			$cmm->execute();

			$alunos = $cmm->fetchAll(PDO::FETCH_ASSOC);

			http_response_code(200); // sucesso

			echo json_encode($alunos);  // envia JSON

		} catch (Exception $ex) {

			http_response_code(404); // falha

			echo '[]';
		}
	}
	else if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// insert na tabela alunos

		$json = file_get_contents("php://input");

		// insert novo aluno com os dados vindos no json
		// {"id":3,"nome":"pedro"}    - json
		$aux = json_decode($json);  

		$pdo = new PDO(hostDb,usuario,senha);

		// Configura o comportamento no caso de erros: levanta exceção.
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try{

			$cmm = $pdo->prepare('insert into aluno values(:pid,:pnome)');

			$cmm->execute([  ':pid' => $aux->id  , ':pnome'  => $aux->nome  ]);

			http_response_code(201);

			echo $json;

		} catch (PDOException $ex) {

			http_response_code(400);  // bad request

			echo '{}';

		} catch (Exception $ex) {
			
			http_response_code(503);  // service unvailable

			echo '{}';
		}

	}
	else if ($_SERVER['REQUEST_METHOD'] == 'PUT')
	{
		// update na tabela alunos

		$json = file_get_contents("php://input");

		// update de um aluno existente com os dados vindos no json
		$aux = json_decode($json);  

		$pdo = new PDO(hostDb,usuario,senha);

		// Configura o comportamento no caso de erros: levanta exceção.
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try{

			$cmm = $pdo->prepare('UPDATE aluno SET nome=:pnome WHERE id=:pid');

			$cmm->execute([  ':pid' => $aux->id  , ':pnome'  => $aux->nome  ]);

			http_response_code(200);

			echo $json;
		} catch (PDOException $ex) {

			http_response_code(503);  // bad request

			echo '{}';

		}

	}
	else if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
	{
		// excluir uma da tabela alunos

		$json = file_get_contents("php://input");

		// exclusão de aluno conforme os dados vindos no json
		$aux = json_decode($json);  

		$aux = json_decode($json);  

		$pdo = new PDO(hostDb,usuario,senha);

		// Configura o comportamento no caso de erros: levanta exceção.
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		try{

			$cmm = $pdo->prepare('DELETE FROM aluno WHERE id =:pid');

			$cmm->execute([':pid' => $aux->id]);

			http_response_code(200);

			echo $json;

		} catch (PDOException $ex) {

			http_response_code(503);  // bad request

			echo '{}';

		}
}
?>