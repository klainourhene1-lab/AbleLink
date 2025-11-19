<?php
class Comment {
    private $repository;

    public function __construct() { 
        $this->repository = new CommentRepository();
    }

    public function getByStory($storyId) {
        return $this->repository->getByStory($storyId);
    }

    public function create($storyId, $userName, $content) {
        return $this->repository->create($storyId, $userName, $content);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }

    public function countAll() {
        return $this->repository->countAll();
    }
}