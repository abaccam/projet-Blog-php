<?php

use Slim\Http\Request;
use Slim\Http\Response;
use simplon\entities\User;
use simplon\entities\Article;
use simplon\dao\DaoUser;
use simplon\dao\DaoArticle;

// Routes

// User

$app->get('/logout', function (Request $request, Response $response, array $args) {
    session_destroy();
    $redirectUrl = $this->router->pathFor('index');
    return $response->withRedirect($redirectUrl);
})->setName('logout');

$app->get('/inscription', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
   
    // Render index view
    return $this->view->render($response, 'inscription.twig', [
        'args' => $args
    ]);
})->setName('inscription');

$app->post('/inscription', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    $form = $request->getParsedBody();

    //On crée une Person à partir de ces données
    $newUser = new User($form['name'], $form['email'], $form['password']);
    $_SESSION['isLogged'] = true;
    //On instancie le DAO
    
    $_SESSION['name']=$form['name'];
    $dao = new DaoUser();
    //On utilise la méthode add du DAO en lui donnant la Person qu'on vient de créer
    $dao->add($newUser);
    $_SESSION['user'] = $newUser;

    // Render index view
    return $this->view->render($response, 'profil.twig', [
        'user'=> $_SESSION['user'], 'name'=>$_SESSION['name'],
        'isLogged'=>$_SESSION['isLogged']
        
    ]);
})->setName('inscription');



$app->get('/profil', function (Request $request, Response $response, array $args) {
    $dao = new DaoUser();
    $art = new DaoArticle();
    if($_SESSION['isLogged']){

        $articles = $art->getAll($_SESSION['user']->getId());
        return $this->view->render($response, 'profil.twig', [
            'user'=> $_SESSION['user'],
            'articles'=> $_SESSION['articles']
        ]);
    } else{
        $redirectUrl = $this->router->pathFor('index');
        //On redirige l'utilisateur sur la page d'accueil
        return $response->withRedirect($redirectUrl);
    }
    
    
})->setName('profil');


$app->post('/profil', function (Request $request, Response $response, array $args) {

    $dao = new DaoUser();
    $form = $request->getParsedBody();
    $newArticle = new Article($form['title'], $form['content']);
    //var_dump($newArticle);
    $daoArt = new DaoArticle();
    
    $user = $dao->getByEmail($form['email']);
    
    $_SESSION['isLogged'] = (!empty($user) && $form['password'] === $user->getPassword() && $form['email'] === $user->getEmail());
    // $_SESSION['isLogged']=$form['isLogged'];
    
    $_SESSION['user']= $user;
    $_SESSION['name']= $user->getName();

    if ($_SESSION['isLogged']) {
    //   session_start();
      $daoArt->add($newArticle, $_SESSION['user']->getId());
      $article= $daoArt->getAll($_SESSION['user']->getId());
    // var_dump($_SESSION['user']);
    return $this->view->render($response, 'profil.twig', [
        'isLogged' => $_SESSION['isLogged'],
        'name'=>$_SESSION['name'],
        'newArticle' => $newArticle,
        'article' => $article
    ]);

     }else{
        $redirectUrl = $this->router->pathFor('index');
        //On redirige l'utilisateur sur la page d'accueil
        return $response->withRedirect($redirectUrl);
    }

})->setName('profil');


//Article

$app->get('/', function (Request $request, Response $response, array $args) {
    //On instancie le dao
    $dao = new DaoArticle();
    //On récupère les Persons via la méthode getAll
    $articles = $dao->getAll(1);
    //On passe les persons à la vue index.twig
    return $this->view->render($response, 'index.twig', [
        'articles' => $articles
    ]);
})->setName('index');


$app->post('/addarticle', function (Request $request, Response $response, array $args) {
    //On récupère les données du formulaire
    $form = $request->getParsedBody();
   
    $newArticle = new Article($form['title'], $form['content']);
    // var_dump($newArticle);
    $dao = new DaoArticle();
   
    $dao->add($newArticle, $_SESSION ['user']->getId());
    $article= $dao->getAll($_SESSION ['user']->getId());

    return $this->view->render($response, 'profil.twig', [
        'isLogged' => $_SESSION['isLogged'],
        'newArticle' => $newArticle,
        'article' => $article,
        'idArti' => $_SESSION['idArti']
    ]);
        
})->setName('addarticle');

$app->get('/updatearticle/{id}', function (Request $request, Response $response, array $args) {
    $dao = new DaoArticle;
    $article = $dao->getById($args['id']);
    return $this->view->render($response, 'updatearticle.twig', [
        'article' => $article
    ]);
    
})->setName('updatearticle');


$app->post('/updatearticle/{id}', function (Request $request, Response $response, array $args) {
    $dao = new DaoArticle;
    $postData = $request->getParsedBody();
    $article = $dao->getById($args['id']);
    $article->setTitle($postData['title']);
    $article->setContent($postData['content']);
    $dao->update($article);
    $redirectUrl = $this->router->pathFor('index');
    return $response->withRedirect($redirectUrl);
})->setName('updatearticle');



$app->get('/deleteArticle/{id}', function (Request $request, Response $response, array $args) {
    echo('lololo');
    $dao = new DaoArticle();
    $art= $dao->getById($args['id']);
    $dao->delete($art);
    $redirectUrl = $this->router->pathFor('profil');
    return $response->withRedirect($redirectUrl);
  
})->setName('deleteArticle');
