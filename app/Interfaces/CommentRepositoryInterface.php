<?php

namespace App\Interfaces;

interface CommentRepositoryInterface
{
    public function getCommentsByPost(int $postId);
    public function getUnconfirmedComments();
    public function getRelatedComments();
    public function getAllComments();
    public function create(array $data);
    public function update(array $data , int $id);
    public function confirm(int $id);
}
