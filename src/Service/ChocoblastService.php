<?php

namespace App\Service;

use App\Entity\Chocoblast;
use App\Repository\ChocoblastRepository;
use App\Service\interface\ServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ChocoblastService implements ServiceInterface{
    public function __construct(private readonly ChocoblastRepository $chocoblastRepository,private readonly EntityManagerInterface $entityManager){}
    public function create(object $object):void
    {
        if(!$object instanceof Chocoblast){
            throw new \InvalidArgumentException('Invalid object type');
        }
        else{
            $object->setCreateAt(new \DateTime());
            $this->entityManager->persist($object);
            $this->entityManager->flush();
        }
    }
    public function update(object $object):void
    {
        if(!$object instanceof Chocoblast){
            throw new \InvalidArgumentException('Invalid object type');
        }
        else{
            $existing = $this->chocoblastRepository->findOneBy(['id' => $object->getId()]);
            if($existing){
                $existing->setTitle($object->getTitle());
                $existing->setStatus($object->isStatus());
                $existing->setAuthor($object->getAuthor());
                $existing->setTarget($object->getTarget());
                $this->entityManager->flush();
            }
            else{
                throw new \InvalidArgumentException('Chocoblast not found');
            }
        }
    }
    public function delete(int $id):void
    {
        if(!$object = $this->chocoblastRepository->findOneBy(['id' => $id])){
            throw new \InvalidArgumentException('Chocoblast not found');
        }
        else{
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }
    }
    public function find(int $id):object
    {
        if(!$object = $this->chocoblastRepository->findOneBy(['id' => $id])){
            throw new \InvalidArgumentException('Chocoblast not found');
        }
        else{
            return $object;
        }
    }
    public function findAll(): array
    {
        if(!$objects = $this->chocoblastRepository->findAll()){
            throw new \InvalidArgumentException('No Chocoblast found');
        }
        else{
            return $objects;
        }
    }
}