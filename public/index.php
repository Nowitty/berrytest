<?php

namespace App;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
require_once(file_exists($autoloadPath1)) ? $autoloadPath1 : $autoloadPath2;

use function App\Renderer\render;

$app = new Application();

$app->get('/', function () {
    $paginator = ['current' => 1, 'count' => $this->articles->count()];
    return response(render('index', [
        'title'     => 'Главная страница',
        'articles'  => $this->articles->getPage(1),
        'pages'     => $paginator,
        ]));
});
$app->get('/new', function () {
    return response(render('new', ['title' => 'Добавить новость']));
});
$app->get('/page/:page', function ($request, $attributes) {
    $current = (int) $attributes['page'];
    if ($current < 2) {
        return response()->redirect('/');
    }
    $paginator = ['current' => $attributes['page'], 'count' => $this->articles->count()];
    return response(render('index', [
        'articles'  => $this->articles->getPage($current),
        'pages'     => $paginator,
        'title'     => "Новости, страница {$current}",
        ]));
});

$app->get('/article/:id', function ($request, $attributes) {
    $id = (int) $attributes['id'];

    $article = $this->articles->getById($id);

    return response(render('show.article', [
        'title'     => $article->getTitle(),
        'article'   => $article,
        'comments'  => $this->comments->getTree($id),
        'countComments' => $this->comments->count($id),
        ]));
});
$app->post('/articles', function ($request) {
    $formData = $request->getQueryParam('article');
    $_SESSION['author'] = $formData['author'];

    /*
    $errors = $this->articles->validate($formData);
    if (!empty($errors)) {
        return [];
        return response(render('new', [
            'title'     => 'Добавить новость',
            'formData'  => $formData,
            'errors'    => $errors, ]))->withStatus(400);
    }
     */
    try {
        $newId = $this->articles->save($formData);
        return response()->redirect("/article/{$newId}");
    } catch (\Throwable $th) {
        return response(render('new', [
            'title'     => 'Добавить новость',
            'formData'  => $formData,
            'errors'    => [$th->getMessage()],
            ]))
            ->withStatus(400);
    }
});

// create Comment
$app->post('/comments', function ($request) {
    $formData = $request->getQueryParam('comment');
    $_SESSION['author'] = $formData['author'];
    $errors = $this->comments->validate($formData);
    if ($errors) {
        return response(json_encode($errors))->withStatus(400);
    }

    $id = $this->comments->save($formData);

    return $request->getHeader('X-Requested-With')
    ? response(json_encode($this->comments->getById($id)))
    : response()->redirect("/article/{$formData['article_id']}");
});

$app->run();
