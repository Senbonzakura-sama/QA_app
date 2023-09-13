<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeTest extends TypeTestCase
{
    public function testFormFields()
    {
        $form = $this->factory->create(UserType::class);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
    }

    public function testSubmitValidData()
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $form = $this->factory->create(UserType::class);

        $expectedUser = new User();
        $expectedUser->setEmail('test@example.com');
        $expectedUser->setPassword('password123');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($expectedUser, $form->getData());
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => User::class]);

        $type = new UserType();
        $type->configureOptions($resolver);
    }

    public function testGetBlockPrefix()
    {
        $type = new UserType();
        $prefix = $type->getBlockPrefix();

        $this->assertSame('user', $prefix);
    }
}
