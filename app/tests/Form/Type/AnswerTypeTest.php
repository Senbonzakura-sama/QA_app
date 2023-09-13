<?php
/**
 * Answer type tests
 */
namespace App\Tests\Form\Type;

use App\Entity\Answer;
use App\Form\Type\AnswerType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Answer type test class
 */
class AnswerTypeTest extends TypeTestCase
{
    /**
     * Test for submitting valid data
     */
    public function testSubmitValidData(): void
    {
        // given
        $formData = [
            'content' => 'test content',
        ];
        $model = new Answer();
        $form = $this->factory->create(AnswerType::class, $model);

        $expected = new Answer();
        $expected->setContent('test content');

        // when
        $form->submit($formData);

        //then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $model);
    }
}
