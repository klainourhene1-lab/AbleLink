<?php
class Story {
    private $repository;

    public function __construct() { 
        $this->repository = new StoryRepository();
    }

    public function all($onlyPublished = true) { 
        return $this->repository->all($onlyPublished);
    }

    public function allForAdmin() {
        return $this->repository->allForAdmin();
    }

    public function countAll() {
        return $this->repository->countAll();
    }
    
    public function find($id) { 
        return $this->repository->find($id);
    }

    public function create($d, $f) {
        $payload = array(
            'user_name' => isset($d['user_name']) ? $d['user_name'] : 'Anonyme',
            'user_email' => isset($d['user_email']) ? $d['user_email'] : '',
            'title' => isset($d['title']) ? $d['title'] : '',
            'content' => isset($d['content']) ? $d['content'] : '',
            'image' => $this->upload(isset($f['image']) ? $f['image'] : null),
            'status' => isset($d['status']) ? $d['status'] : 'pending'
        );
        return $this->repository->create($payload);
    }

    public function update($id, $d, $f) {
            $img = $this->upload(isset($f['image']) ? $f['image'] : null);
            $old = $this->find($id);
            if ($old && $img && !empty($old['image'])) {
                @unlink(__DIR__ . "/../../public/uploads/" . $old['image']);
            }

            $sql = $img ? "UPDATE stories SET title=?, content=?, image=? WHERE id=?" 
                        : "UPDATE stories SET title=?, content=? WHERE id=?";
            $params = $img ? array(
                isset($d['title']) ? $d['title'] : '', 
                isset($d['content']) ? $d['content'] : '', 
                $img, 
                $id
            ) : array(
                isset($d['title']) ? $d['title'] : '', 
                isset($d['content']) ? $d['content'] : '', 
                $id
            );
        return $this->repository->update($sql, $params);
    }

    public function delete($id) {
            $e = $this->find($id);
            if ($e && !empty($e['image'])) {
                @unlink(__DIR__ . "/../../public/uploads/" . $e['image']);
            }
        return $this->repository->delete($id);
    }

    public function addLike($storyId, $userIp) {
        return $this->repository->addLike($storyId, $userIp);
    }

    public function getLikesCount($storyId) {
        return $this->repository->getLikesCount($storyId);
    }

    public function countLikes() {
        return $this->repository->countLikes();
    }

    public function hasLiked($storyId, $userIp) {
        return $this->repository->hasLiked($storyId, $userIp);
    }

    public function updateStatus($id, $status) {
        return $this->repository->updateStatus($id, $status);
    }

    public function topStory() {
        return $this->repository->getTopStory();
    }

    private function upload($file) {
        if (!$file || !is_array($file)) {
            return null;
        }
        if (isset($file['error']) && $file['error'] === 0 && isset($file['tmp_name']) && isset($file['name'])) {
            $uploadDir = __DIR__ . "/../../public/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $n = time().'_'.uniqid().'.'.$extension;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $n)) {
                return $n;
            }
        }
        return null;
    }
}