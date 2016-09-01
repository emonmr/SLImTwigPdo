<?php
session_start();
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/', function (Request $request, Response $response) {
    //$this->logger->addInfo("Ticket list");
    $this->view->render($response, 'login.twig');
    return $response;
});

$app->get('/home', function (Request $request, Response $response) {
    //$this->logger->addInfo("Ticket list");
    $this->view->render($response, 'home.twig');
    return $response;
});

$app->post('/auth', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $mapper = new \App\EmployeeMapper($this->db);
    $user=$mapper->getUser($data['name'],$data['password']);
    if(array_key_exists('id' ,$user)){
        $_SESSION['user']=$user;
        if($_SESSION['user']['role']=='admin'){
            return $this->view->render($response, "home.twig");
        }else{
            return $this->view->render($response, "login.twig");
        }
    }else{
        return $this->view->render($response, "login.twig");
    }

});


$app->get('/logout', function (Request $request, Response $response) {
    $mapper = new \App\EmployeeMapper($this->db);
    unset($_SESSION["user"]['id']);
    unset($_SESSION["user"]['name']);
    session_destroy();
    return $response->withRedirect('/');

});

$app->get('/employee', function (Request $request, Response $response) {
    $this->logger->addInfo("Ticket list");
    $mapper = new \App\EmployeeMapper($this->db);
    $employee = $mapper->getEmployee();
    $delete_message = $this->flash->getMessages();

    //var_dump($delete_message); die();
    $this->view->render($response, 'employee.twig' , ['emp' => $employee,'msg'=>$delete_message]);
    return $response;
});

$app->get('/add', function (Request $request, Response $response) {
    $mapper = new \App\EmployeeMapper($this->db);
    $mapper_dept = new \App\DepartmentMapper($this->db);
    $dept=$mapper_dept->getDepartment();
    $mapper_desi = new \App\DesignationMapper($this->db);
    $desi=$mapper_desi->getDesignation();
    //var_dump($desi);
    $this->view->render($response, "insert.twig",['dept'=>$dept,'desi'=>$desi]);
});

$app->post('/insert', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $mapper = new \App\EmployeeMapper($this->db);
    $id=$mapper->addEmployee($data);
    //$this->flash->addMessage('message', 'Successfuly employee added !!!');
    return $response->withRedirect('/details/'.$id);
});

$app->get('/details/{id}', function(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $mapper = new \App\EmployeeMapper($this->db);
    $details_data = $mapper->getDetails($id);
    //var_dump($details_data); die();
    $messages = $this->flash->getMessages();
    //var_dump($messages);die();
    $response = $this->view->render($response, "details.twig",['details'=>$details_data,'msg'=>$messages]);
    return $response;
});

$app->get('/update/{id}', function(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $mapper = new \App\EmployeeMapper($this->db);
    $update_data = $mapper->getbyId($id);
    $mapper_dept = new \App\DepartmentMapper($this->db);
    $dept=$mapper_dept->getDepartment();
    $mapper_desi = new \App\DesignationMapper($this->db);
    $desi=$mapper_desi->getDesignation();
    $response = $this->view->render($response, "update.twig",['update_data'=>$update_data,'dept'=>$dept,'desi'=>$desi]);
    return $response;
});

$app->post('/update', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    //var_dump($data); die();
    $mapper = new \App\EmployeeMapper($this->db);
    $sql=$mapper->editEmployee($data);
    $this->flash->addMessage('update_message', 'Update! Successfuly Updated!!!');
    //$this->flash->addMessage('update_message', 'Successfuly updated !!!');
    return $response->withRedirect('/details/'.$sql);
});

$app->get('/delete/{id}', function(Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $mapper = new \App\EmployeeMapper($this->db);
    $mapper->empDelete($id);
    $this->flash->addMessage('delete_message', 'Employee Deleted!!!');
    return $response->withRedirect('/employee');
});

$app->get('/attendence', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $mapper = new \App\EmployeeMapper($this->db);
    $response = $this->view->render($response, "attendence.twig");

});




