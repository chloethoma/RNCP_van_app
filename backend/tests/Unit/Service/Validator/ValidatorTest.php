<?php

namespace App\Tests\Services\Validator;

use App\Services\Exceptions\Validation\InvalidReceivedDataException;
use App\Services\Validator\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTest extends TestCase
{
    private Validator $validator;
    private $symfonyValidator;

    protected function setUp(): void
    {
        $this->symfonyValidator = $this->createMock(ValidatorInterface::class);
        $this->validator = new Validator($this->symfonyValidator);
    }

    public function testValidateWithNoViolations(): void
    {
        $dto = new \stdClass();

        $violations = new ConstraintViolationList();

        $this->symfonyValidator
            ->method('validate')
            ->with($dto, null, [])
            ->willReturn($violations);

        $this->validator->validate($dto, 'DummyClass');

        $this->assertTrue(true);
    }

    public function testValidateWithViolations(): void
    {
        $this->expectException(InvalidReceivedDataException::class);
        $this->expectExceptionMessage('DummyClass');

        $dto = new \stdClass();

        $violation1 = new ConstraintViolation(
            'This value should not be blank.',
            '',
            [],
            '',
            'field1',
            ''
        );

        $violation2 = new ConstraintViolation(
            'Must be greater than 5.',
            '',
            [],
            '',
            'field2',
            ''
        );

        $violations = new ConstraintViolationList([$violation1, $violation2]);

        $this->symfonyValidator
            ->method('validate')
            ->with($dto, null, [])
            ->willReturn($violations);

        $this->validator->validate($dto, 'DummyClass');
    }
}
