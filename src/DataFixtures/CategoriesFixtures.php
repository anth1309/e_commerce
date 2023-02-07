<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter = 1; //cree un cmteur pour renvoyer addreference a ProductFix

    public function __construct(private SluggerInterface $slugger) //voir lg42
    {
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', null, 1, $manager);

        $this->createCategory('Cartes méres', $parent, 3, $manager);
        $this->createCategory('Souris', $parent, 5, $manager);
        $this->createCategory('Disques dur', $parent, 4, $manager);
        $this->createCategory('Cartes graphique', $parent, 2, $manager);


        $parent = $this->createCategory('Rc électrique', null, 6, $manager);

        $this->createCategory('Camions militaire', $parent, 7, $manager);
        $this->createCategory('Voitures scales 1/10', $parent, 10, $manager);
        $this->createCategory('Chars', $parent, 8, $manager);
        $this->createCategory('Engins TP', $parent, 9, $manager);


        $manager->flush();
    }

    //fonction appliqué ci dessus
    public function createCategory(string $name, Categories $parent = null, int $order, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower()); //permet de construire un slug qui reprend le nom du parent au format slug cad minuscule remplace$category->setSlug('informatique')
        $category->setParent($parent);
        $category->setCategoryOrder($order);
        $manager->persist($category);
        $this->addReference('cat-' . $this->counter, $category);
        $this->counter++;

        return $category;
    }
}
