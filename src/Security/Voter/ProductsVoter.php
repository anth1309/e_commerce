<?php

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $product): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if ($product instanceof Products) {
            return false;
        }
        return true;

        // equivalent car retourne true et true
        //return in_array($attribute, [self::EDIT, self::DELETE]) && $product instanceof Products

    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool

    {
        //on recupere l user a partir du token
        $user = $token->getUser();
        if (!$user instanceof UserInterface) return false;

        //on verifi si user est admincondition
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        //on verifie les permissions
        switch ($attribute) {
            case self::EDIT:
                //on verifi si l user peut editer
                return $this->canEdit();
                break;
            case self::DELETE:
                //on verifi si l user peur delete
                return $this->canDelete();
                break;
        }
    }
    private function canEdit()
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete()
    {
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
}
