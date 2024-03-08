<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter <string , Task>
 */
class TaskVoter extends Voter
{
    public const DELETE = 'TASK_DELETE';
    public const MODIFY = 'TASK_MODIFY';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, [self::DELETE, self::MODIFY], true)
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }
        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($task, $user);
            case self::MODIFY:
                return $this->canModify($task, $user);
        }

        return false;
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $user === $task->getUser() || (null === $task->getUser() && $user->getRoles() === ['ROLE_ADMIN']);
    }

    private function canModify(Task $task, User $user): bool
    {
        return $user === $task->getUser() || (null === $task->getUser() && $user->getRoles() === ['ROLE_ADMIN']);
    }
}
