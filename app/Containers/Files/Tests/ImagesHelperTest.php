<?php

namespace  App\Containers\Files\Tests;

use App\Helpers\Tests\TestsFacilitator;
use Tests\TestCase;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\UpdateFailedException;

use App\Containers\Files\Helpers\ImagesHelper;

use Illuminate\Support\Str;

use App\Containers\Files\Models\Image;
use App\Containers\Files\Models\ImageType;

use Illuminate\Http\Testing\File;
use App\Helpers\Storage\StoreHelper;

class ImagesHelperTest extends TestCase
{
    use TestsFacilitator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    private function storeImage(): Image
    {
        return Image::create([
            'link' => 'link',
            'size' => 85456,
            'type_id' => 1
        ]);
    }

    /**
     * Test successful types.
     *
     * @return void
     */
    public function test_types_successful()
    {
        $types = ImageType::all();

        $this->assertEquals(ImagesHelper::types(), $types);
    }

    /**
     * Test successful type id by type name.
     *
     * @return void
     */
    public function test_getTypeIdByName_successful()
    {
        // Those are the seeded types
        $types = [
            'logo',
            'profile',
            'cover',
            'general',
        ];

        foreach($types as $typeName) {
            $typeID = ImageType::where('name', $typeName)->first()->id;
            $typeId = ImagesHelper::getTypeIdByName($typeName);
            $this->assertEquals($typeID, $typeId);
        }
    }

    /**
     * Test fail getTypeIdByName.
     *
     * @return void
     */
    public function test_getTypeIdByName_fail()
    {
        $this->expectException(NotFoundException::class);

        $result = ImagesHelper::getTypeIdByName('dwefffefwe');

        $this->assertException($result, 'NotFoundException');
    }

    /**
     * Test successful image by id.
     *
     * @return void
     */
    public function test_image_by_id_successful()
    {
        $image = $this->storeImage();

        // With load type
        $imageById = ImagesHelper::getImageById($image->id);
        $image = Image::find($image->id)->load(['type']);
        $this->assertEquals($imageById, $image);

        // Without load type
        $imageById = ImagesHelper::getImageById($image->id, true);
        $image = Image::find($image->id);
        $this->assertEquals($imageById, $image);
    }

    /**
     * Test fail getImageById.
     *
     * @return void
     */
    public function test_getImageById_fail()
    {
        $this->expectException(NotFoundException::class);

        $result = ImagesHelper::getImageById(456148562);

        $this->assertException($result, 'NotFoundException');
    }

    /**
     * Test successful create image.
     *
     * @return void
     */
    public function test_create_successful()
    {
        $result = ImagesHelper::create([
            'link' => 'dddewe',
            'size' => 45312,
        ], 'logo');

        $image = Image::orderBy('id', 'desc')->first()->load(['type']);

        $this->assertEquals($image, $result);
    }

    /**
     * Test fail create image.
     *
     * @return void
     */
    public function test_create_fail()
    {
        $this->expectException(NotFoundException::class);

        $result = ImagesHelper::create([
            'link' => 'dddewe',
            'size' => 45312,
        ], 'UNKNOWN');

        $this->assertException($result, 'NotFoundException');
    }

    /**
     * Test successful update_by_id image.
     *
     * @return void
     */
    public function test_update_by_id_successful()
    {
        $image = $this->storeImage();

        $result = ImagesHelper::updateById($image->id, [
            'link' => 'dddewe',
            'size' => 45312,
        ], 'logo');

        $image = ImagesHelper::getImageById($image->id);
        $this->assertEquals($image, $result);
    }

    /**
     * Test fail update_by_id image.
     *
     * @return void
     */
    public function test_update_by_id_fail()
    {
        $this->expectException(UpdateFailedException::class);

        $result = ImagesHelper::updateById(5452, [
            'link' => 'dddewe',
            'size' => 45312,
        ], 'logo');

        $this->assertException($result, 'UpdateFailedException');
    }

    /**
     * Test successful update image.
     *
     * @return void
     */
    public function test_update_successful()
    {
        $image = $this->storeImage();

        $data = [
            'link' => 'swdew',
            'size' => 6233
        ];
        $result = ImagesHelper::update($image, $data, 'logo');

        $image = ImagesHelper::getImageById($image->id);
        $this->assertEquals($image, $result);
    }

    /**
     * Test successful delete image.
     *
     * @return void
     */
    public function test_delete_successful()
    {
        $file = File::image('image.png', 400, 100);
        $link = StoreHelper::storeFile($file, 'uploads');
        $image = Image::create([
            'link' => $link,
        ]);

        $result = ImagesHelper::delete($image->id, true);
        $this->assertEquals(true, $result);

        $deletedImageCount = Image::onlyTrashed()->where('id', $image->id)->count();
        $this->assertEquals(1, $deletedImageCount);
    }
}
