<?php

namespace App\Validator;

use Symfony\Component\Filesystem\Path;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AbsolutePathValidator extends ConstraintValidator
{
    public function validate(mixed $path, Constraint $constraint): void
    {
        /* @var AbsolutePath $constraint */

        if (null === $path || '' === $path) {
            return;
        }

        $path = (string) $path;
        if (Path::isAbsolute($path)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ path }}', $path)
            ->addViolation();
    }
}
