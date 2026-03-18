<?php

namespace Resouce\Controllers;

class UserController
{
    public function index($id)
    {
        return  "user id {$id}";
    }
    public function store()
    {
        return "user saved";
    }
    public function update()
    {
        return "user updated";
    }
    public function delete()
    {
        return "user deleted";
    }
}
