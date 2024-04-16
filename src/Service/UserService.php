<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements ServiceInterface
{

    public function __construct(private readonly UserRepository $userRepository, private readonly EntityManagerInterface $entityManager){}

    /**
     * Create a new object
     *
     * @param object $object The object to create
     *
     * @return void
     * @throws \InvalidArgumentException When the object type is invalid or a user with the same email already exists
     *
     */
    public function create(object $object): void
    {
        if (!$object instanceof User) {
                throw new \InvalidArgumentException('Invalid object type. User object expected.');
            }
        else{
            if (!$this->userRepository->findOneBy(['email' => $object->getEmail()])) {
                $this->entityManager->persist($object);
                $this->entityManager->flush();
            }
            else {
                throw new \InvalidArgumentException('User with the same email already exists.');
            }
        }
    }

    /**
     * Update an object
     *
     * @param object $object The object to update
     *
     * @return void
     * @throws \InvalidArgumentException When the object type is invalid or the user is not found
     *
     */
    public function update(object $object):void
    {
        if (!$object instanceof User) {
            throw new \InvalidArgumentException('Invalid object type. User object expected.');
        } else {
            $existingUser = $this->userRepository->findOneBy(['email' => $object->getEmail()]);
            if ($existingUser) {
                $this->entityManager->persist($object);
                $this->entityManager->flush();
            } else {
                throw new \InvalidArgumentException('User not found.');
            }
        }
    }
    /**
     * Deletes a user by their ID.
     *
     * @param int $id The ID of the user to delete.
     * @throws \InvalidArgumentException If the user is not found.
     */
    public function delete(int $id)
    {
        if($this->userRepository->findOneBy(['id' => $id])) {
            $this->entityManager->remove($this->userRepository->find($id));
            $this->entityManager->flush();
        }
        else{
            throw new \InvalidArgumentException('User not found.');
        }
    }

    /**
     *
     * @param int $id The ID of the user to find
     * @return User|null The found user or null if not found
     * @throws \InvalidArgumentException If the user is not found
     */
    public function find(int $id):User
    {
        return $this->userRepository->find($id)??throw new \InvalidArgumentException('User not found.');
    }

    /**
     *
     * Retrieves all users from the database.
     *
     * @return array An array of User objects
     *
     * @throws \InvalidArgumentException If no users are found
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll()??throw new \InvalidArgumentException('No User found.');
    }
}