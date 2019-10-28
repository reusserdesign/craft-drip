<?php

namespace yournamespace\tests;

use Codeception\Test\Unit;
use Craft;
use extreme\drip\Drip;
use Solspace\Freeform\Freeform;
use UnitTester;

class DripVariableTest extends Unit
{
  /**
   * @var UnitTester
   */
    protected $tester;

  /** @test * */
    public function testDripAccountId()
    {
        $drip = new Drip('drip');
        $drip->setSettings([
        'dripAccountId' => '1234',
        ]);
        $drip->init();

        $template = '{{ craft.drip.dripAccountId }}';

        $output = Craft::$app->getView()->renderString($template);

        $this->assertEquals('1234', $output);
    }

    /** @test
     * Test that dripFields variable function returns array of at least 1 element
     * dripFields returns value used to populate a select field
     *
     */

    public function testDripFields()
    {
        $drip = new Drip('drip');
        $drip->init();

        $template = '{{ craft.drip.dripFields | length }}';

        $output = (int) Craft::$app->getView()->renderString($template);

        $this->assertThat($output, $this->logicalAnd(
            $this->isType('int'),
            $this->greaterThan(0)
        ));
    }


}
