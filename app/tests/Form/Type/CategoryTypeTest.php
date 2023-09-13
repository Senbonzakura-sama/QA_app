<?php
/**
 * Category type tests
 */
namespace App\Tests\Form\Type;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Category type test class
 */
class CategoryTypeTest extends TypeTestCase
{
    /**
     * Test for submitting valid data
     */
    public function testSubmitValidData(): void
    {
        // given
        $formData = [
            'title' => 'test category',
        ];
        $model = new Category();
        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setTitle('test category');

        // when
        $form->submit($formData);

        //then
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $model);
    }
}
