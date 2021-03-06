<?php

namespace Spatie\MediaLibrary\Test\Conversion;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Test\TestCase;
use Spatie\MediaLibrary\Conversion\Conversion;

class ConversionTest extends TestCase
{
    protected $conversionName = 'test';

    /** @var \Spatie\MediaLibrary\Conversion\Conversion */
    protected $conversion;

    public function setUp()
    {
        $this->conversion = new Conversion($this->conversionName);

        parent::setUp();
    }

    /** @test */
    public function it_can_get_its_name()
    {
        $this->assertEquals($this->conversionName, $this->conversion->getName());
    }

    /** @test */
    public function it_will_add_a_format_parameter_if_it_was_not_given()
    {
        $this->conversion->width(10);

        $this->assertEquals('jpg', $this->conversion->getManipulations()->getManipulationArgument('format'));
    }

    /** @test */
    public function it_will_use_the_format_parameter_if_it_was_given()
    {
        $this->conversion->format('png');

        $this->assertEquals('png', $this->conversion->getManipulations()->getManipulationArgument('format'));
    }

    /** @test */
    public function it_will_be_performed_on_the_given_collection_names()
    {
        $this->conversion->performOnCollections('images', 'downloads');
        $this->assertTrue($this->conversion->shouldBePerformedOn('images'));
        $this->assertTrue($this->conversion->shouldBePerformedOn('downloads'));
        $this->assertFalse($this->conversion->shouldBePerformedOn('unknown'));
    }

    /** @test */
    public function it_will_be_performed_on_all_collections_if_not_collection_names_are_set()
    {
        $this->conversion->performOnCollections('*');
        $this->assertTrue($this->conversion->shouldBePerformedOn('images'));
        $this->assertTrue($this->conversion->shouldBePerformedOn('downloads'));
        $this->assertTrue($this->conversion->shouldBePerformedOn('unknown'));
    }

    /** @test */
    public function it_will_be_performed_on_all_collections_if_not_collection_name_is_a_star()
    {
        $this->assertTrue($this->conversion->shouldBePerformedOn('images'));
        $this->assertTrue($this->conversion->shouldBePerformedOn('downloads'));
        $this->assertTrue($this->conversion->shouldBePerformedOn('unknown'));
    }

    /** @test */
    public function it_will_be_queued_by_default()
    {
        $this->assertTrue($this->conversion->shouldBeQueued());
    }

    /** @test */
    public function it_can_be_set_to_queued()
    {
        $this->assertTrue($this->conversion->queued()->shouldBeQueued());
    }

    /** @test */
    public function it_can_be_set_to_nonQueued()
    {
        $this->assertFalse($this->conversion->nonQueued()->shouldBeQueued());
    }

    /** @test */
    public function it_can_determine_the_extension_of_the_result()
    {
        $this->conversion->width(50);

        $this->assertEquals('jpg', $this->conversion->getResultExtension());

        $this->conversion->width(100)->format('png');

        $this->assertEquals('png', $this->conversion->getResultExtension());
    }

    /** @test */
    public function it_can_remove_a_previously_set_manipulation()
    {
        $this->assertEquals('jpg', $this->conversion->getManipulations()->getManipulationArgument('format'));

        $this->conversion->removeManipulation('format');

        $this->assertNull($this->conversion->getManipulations()->getManipulationArgument('format'));
    }

    /** @test */
    public function it_will_use_the_extract_duration_parameter_if_it_was_given()
    {
        $this->conversion->extractVideoFrameAtSecond(10);

        $this->assertEquals(10, $this->conversion->getExtractVideoFrameAtSecond());
    }

    /** @test */
    public function manipulations_can_be_set_using_an_instance_of_manipulations()
    {
        $this->conversion->setManipulations((new Manipulations())->width(10));

        $this->assertEquals([[
            'width' => 10,
            'format' => 'jpg',
        ]], $this->conversion
            ->getManipulations()
            ->getManipulationSequence()
            ->toArray()
        );
    }

    /** @test */
    public function manipulations_can_be_set_using_a_closure()
    {
        $this->conversion->setManipulations(function (Manipulations $manipulations) {
            $manipulations->width(10);
        });

        $this->assertEquals([[
            'width' => 10,
            'format' => 'jpg',
        ]], $this->conversion
            ->getManipulations()
            ->getManipulationSequence()
            ->toArray()
        );
    }
}
