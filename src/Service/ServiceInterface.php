<?php

namespace App\Service;

interface ServiceInterface{
    public function create();
    public function update(Object $object);
    public function delete(int $id);
    public function find(int $id);
    public function findAll();

}