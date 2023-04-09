<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // create fake authors
        $listAuthors = [];
        for ($i = 1; $i < 10; $i++) {
            $author = new Author;
            $author->setFirstname('firstname ' . $i);
            $author->setLastname('lastname : ' . $i);
            $manager->persist($author);
            $listAuthors[] = $author;
        }

        // create fake post
        for ($i = 1; $i < 20; $i++) {
            $post = new Post;
            $post->setTitle('title ' . $i);
            $post->setDescription('description : ' . $i);
            $post->setAuthor($listAuthors[array_rand($listAuthors)]);
            $manager->persist($post);
        }
        $manager->flush();
    }
}
