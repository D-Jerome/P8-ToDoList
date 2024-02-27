<?php 

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private const NOT_BLANK_USERNAME_MESSAGE = "Vous devez saisir un nom d'utilisateur.";
    private const FORMAT_EMAIL_MESSAGE = "Le format de l'adresse n'est pas correcte.";
    private const NOT_BLANK_EMAIL_MESSAGE = "Vous devez saisir une adresse email.";
    private const VALID_USERNAME = "test";
    private const VALID_EMAIL = "test@test.com";
    private const VALID_PASSWORD = "!@12QWqw";
    private const INVALID_PASSWORD = "122221qww";
    private const BLANK_DATA = "";
    private const INVALID_EMAIL = "testtest.com";
    private ValidatorInterface $validator;

    protected function setUp():void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }

    private function getValidationErrors(User $user, int $numberOfExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);
        $this->assertCount($numberOfExpectedErrors, $errors);
        return $errors;
    }

    public function testValidUser()
    {
        $user = (new User());
        $user->setUsername(self::VALID_USERNAME);
        $user->setPassword(self::VALID_PASSWORD);
        $user->setEmail(self::VALID_EMAIL);
        
        $this->getValidationErrors($user,0);

    }

    public function testInvalidEmailFormat()
    {
        $user = (new User());
        $user->setUsername(self::VALID_USERNAME);
        $user->setPassword(self::VALID_PASSWORD);
        $user->setEmail(self::INVALID_EMAIL);
        
        $this->getValidationErrors($user,1);

    }
    

    public function testBlankUsername()
    {
        $user = (new User());
        $user->setUsername(self::BLANK_DATA);
        $user->setPassword(self::VALID_PASSWORD);
        $user->setEmail(self::VALID_EMAIL);
        
        $this->getValidationErrors($user,1);

    }
    

    public function testBlankEmail()
    {
        $user = (new User());
        $user->setUsername(self::VALID_USERNAME);
        $user->setPassword(self::VALID_PASSWORD);
        $user->setEmail(self::BLANK_DATA);
        
        $this->getValidationErrors($user,1);

    }
        
    
}