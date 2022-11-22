<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Address;
use App\Entity\User;
use App\Security\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class AddressVoter extends Voter
{
    public const ADDRESS_CREATE = 'ADRESS_CREATE';
    public const ADDRESS_EDIT = 'ADDRESS_EDIT';
    public const ADDRESS_VIEW_ITEM = 'ADDRESS_VIEW_ITEM';
    public const ADDRESS_DELETE = 'ADDRESS_DELETE';

    public function __construct(private Security $security)
    {
        // Note: You can't use Voters on the collection GET method
    }

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, [
            self::ADDRESS_CREATE,
            self::ADDRESS_EDIT,
            self::ADDRESS_VIEW_ITEM,
            self::ADDRESS_DELETE,
        ], true)
            && $subject instanceof Address;
    }

    /**
     * @param Address $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::ADDRESS_CREATE:
            case self::ADDRESS_EDIT:
            case self::ADDRESS_VIEW_ITEM:
                if ($this->canCreateEditAndViewItem($user, $subject->getUser())) {
                    return true;
                }

                break;

            case self::ADDRESS_DELETE:
                if ($this->isAdmin()) {
                    return true;
                }

                break;
        }

        return false;
    }

    private function canCreateEditAndViewItem(User $loggedinUser = null, User $userAddress = null): bool
    {
        return $this->isAdmin() || $loggedinUser === $userAddress;
    }

    private function isAdmin(): bool
    {
        return $this->security->isGranted(Role::ROLE_ADMIN);
    }
}
