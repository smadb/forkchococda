<?php

namespace App\Service;

interface ServiceInterface{
    public function create(Object $object);
    public function update(Object $object);
    public function delete(int $id);
    public function find(int $id);
    public function findAll():array;

}