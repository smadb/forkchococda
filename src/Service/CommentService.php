<?php


namespace App\Service;

use App\Entity\Commentary;
use App\Repository\CommentaryRepository;
use App\Service\interface\ServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class CommentService implements ServiceInterface{

    public function __construct(private EntityManagerInterface $entityManager,private CommentaryRepository $commentaryRepository){}

    public function create(object $object):void
    {
        if(!$object instanceof Commentary){
            throw new \InvalidArgumentException('Invalid object type');
        }
        else{
            $this->entityManager->persist($object);
            $this->entityManager->flush();
        }
    }
    public function update(object $object):void
    {
        if(!$object instanceof Commentary){
            throw new \InvalidArgumentException('Invalid object type');
        }
        else{
            $existing = $this->commentaryRepository->findBy(['commentary' => $object]);
            if($existing){
                $this->entityManager->persist($object);
                $this->entityManager->flush();
            }
            else{
                throw new \InvalidArgumentException('Commentary does not exist');
            }
        }
    }
    public function delete(int $id):void
    {
        if(!$object = $this->commentaryRepository->find($id)){
            throw new \InvalidArgumentException('Commentary does not exist');
        }
        else{
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }
    }
    public function find(int $id):object
    {
        if(!$object = $this->commentaryRepository->find($id)){
            throw new \InvalidArgumentException('Commentary does not exist');
        }
        else{
            return $object;
        }
    }
    public function findAll(): array
    {
        if(!$objects = $this->commentaryRepository->findAll()){
            throw new \InvalidArgumentException('Commentary does not exist');
        }
        else{
            return $objects;
        }
    }

}