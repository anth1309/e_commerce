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
        $parent = $this->createCategory('Informatique', null, $manager);

        $this->createCategory('Cartes méres', $parent, $manager);
        $this->createCategory('Souris', $parent, $manager);
        $this->createCategory('Disques dur', $parent, $manager);
        $this->createCategory('Cartes graphique', $parent, $manager);

        $parent = $this->createCategory('Rc électrique', null, $manager);

        $this->createCategory('Camions militaire', $parent, $manager);
        $this->createCategory('Voitures scales 1/10', $parent, $manager);
        $this->createCategory('Chars', $parent, $manager);
        $this->createCategory('Engins TP', $parent, $manager);


        $manager->flush();
    }

    //fonction appliqué ci dessus
    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower()); //permet de construire un slug qui reprend le nom du parent au format slug cad minuscule remplace$category->setSlug('informatique')
        $category->setParent($parent);
        $manager->persist($category);
        $this->addReference('cat-' . $this->counter, $category);
        $this->counter++;

        return $category;
    }
}
