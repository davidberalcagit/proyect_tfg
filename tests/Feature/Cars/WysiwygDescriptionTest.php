<?php

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Customers;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use App\Models\User;
use App\Models\CarStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'create cars']);
    $this->user = User::factory()->create();
    $this->user->givePermissionTo('create cars');

    $this->customer = Customers::factory()->create(['id_usuario' => $this->user->id]);

    $this->brand = Brands::factory()->create();
    $this->carModel = CarModels::factory()->create(['id_marca' => $this->brand->id]);
    $this->color = Color::factory()->create();
    $this->fuel = Fuels::factory()->create();
    $this->gear = Gears::factory()->create();

    $this->listingType = ListingType::firstOrCreate(['id' => 1, 'nombre' => 'Venta']);
    CarStatus::firstOrCreate(['id' => 4, 'nombre' => 'Pendiente']);
});

it('can store and display all wysiwyg html tags from the editor buttons', function () {
    $this->actingAs($this->user);

    Storage::fake('public');

    // Simulate clicking all buttons in the WYSIWYG editor
    $richTextDescription = "<h2>Heading 2</h2>
        <p><strong>Bold text</strong> and <i>Italic text</i></p>
        <p><a href=\"https://example.com\">Link text</a></p>
        <ul>
            <li>Bulleted item 1</li>
            <li>Bulleted item 2</li>
        </ul>
        <ol>
            <li>Numbered item 1</li>
        </ol>
        <blockquote>Blockquote text</blockquote>";

    $carData = [
        'id_marca' => $this->brand->id,
        'id_modelo' => $this->carModel->id,
        'id_color' => $this->color->id,
        'id_combustible' => $this->fuel->id,
        'id_marcha' => $this->gear->id,
        'anyo_matri' => 2020,
        'km' => 50000,
        'precio' => 15000,
        'matricula' => '1234ABC',
        'descripcion' => $richTextDescription,
        'id_listing_type' => $this->listingType->id,
        'image' => UploadedFile::fake()->image('car.jpg'),
    ];

    $response = $this->post(route('cars.store'), $carData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $car = Cars::where('matricula', '1234ABC')->first();
    expect($car)->not->toBeNull();

    // Use trim on both to avoid trailing/leading space issues
    expect(trim($car->descripcion))->toBe(trim($richTextDescription));

    // Verify that the rich text is rendered on the show page without being escaped
    $showResponse = $this->get(route('cars.show', $car));
    $showResponse->assertStatus(200);

    $showResponse->assertSee('<h2>Heading 2</h2>', false);
    $showResponse->assertSee('<strong>Bold text</strong>', false);
    $showResponse->assertSee('<i>Italic text</i>', false);
    $showResponse->assertSee('<a href="https://example.com">Link text</a>', false);
    $showResponse->assertSee('<li>Bulleted item 1</li>', false);
    $showResponse->assertSee('<ol>', false);
    $showResponse->assertSee('<blockquote>Blockquote text</blockquote>', false);
});
