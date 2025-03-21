<?php

namespace App\DTO\Friendship;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class FriendshipUserDTO
{
    public function __construct(
        #[Groups(['read', 'create', 'update'])]
        #[Assert\NotNull(groups: ['read', 'create', 'update'])]
        public readonly ?int $id,

        #[Groups(['read'])]
        #[Assert\NotBlank(groups: ['read'])]
        public readonly ?string $pseudo,

        #[Groups(['read'])]
        #[Assert\NotBlank(allowNull: true, groups: ['read'])]
        public readonly ?string $picture,
    ) {
    }
}
