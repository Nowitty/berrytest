<?php

namespace Db;

use App\Entities\Post;

class PostManager
{
    private $repo;
    private $posts;

    public function __construct()
    {
        $this->repo = new Repository('articles');
    }

    private function makePost($data)
    {
        return new Post($data);
    }

    public function getAll()
    {
        $data = $this->repo->All();
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->makePost($item);
        }

        return $result;
    }

    public function getPage($page)
    {
        $data = $this->repo->getPage($page);
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->makePost($item);
        }

        return $result;
    }

    public function getById(int $id)
    {
        return new Post($this->repo->findBy('id', $id));
    }

    public function save($data)
    {
        $data = $this->sanitize($data);
        $prepareData = [
            'description' => $data['description'],
            'body' => str_replace(PHP_EOL, '</br>', $data['body']),
            'title' => $data['title'],
            'author' => $data['author'],
        ];

        return $this->repo->insert($prepareData);
    }

    public function sanitize($data)
    {
        return array_map('\Utilities\clean', $data);
    }

    public function validate($data)
    {
        return array_filter($this->sanitize($data), function ($item) {
            return empty($item);
        });
    }

    public function count()
    {
        return $this->repo->count();
    }
}
