<?php

namespace App\Tests\Services\Validator;

use App\DTO\User\UserPasswordDTO;
use App\Services\Validator\NotIdenticalPassword;
use App\Services\Validator\NotIdenticalPasswordValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class NotIdenticalPasswordValidatorTest extends TestCase
{
    public function testValidateWithIdenticalPasswordsAddsViolation(): void
    {
        $dto = new UserPasswordDTO('password123', 'password123');
        $constraint = new NotIdenticalPassword();

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())
            ->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('getObject')->willReturn($dto);
        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder);

        $validator = new NotIdenticalPasswordValidator();
        $validator->initialize($context);
        $validator->validate($dto->newPassword, $constraint);
    }

    public function testValidateWithDifferentPasswordsDoesNothing(): void
    {
        $dto = new UserPasswordDTO('oldPassword', 'newPassword123');
        $constraint = new NotIdenticalPassword();

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->method('getObject')->willReturn($dto);
        $context->expects($this->never())
            ->method('buildViolation');

        $validator = new NotIdenticalPasswordValidator();
        $validator->initialize($context);
        $validator->validate($dto->newPassword, $constraint);
    }
}
