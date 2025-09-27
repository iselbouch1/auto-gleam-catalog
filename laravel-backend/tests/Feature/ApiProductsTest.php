<?php

use App\Models\Category;
use App\Models\Product;

beforeEach(function () {
    $this->category = Category::factory()->create([
        'name' => 'Éclairage',
        'slug' => 'eclairage',
        'is_visible' => true,
    ]);

    $this->products = Product::factory(3)->create([
        'is_visible' => true,
    ]);

    $this->products->each(function ($product) {
        $product->categories()->attach($this->category);
        $product->attachTags(['LED', 'Premium']);
    });

    $this->hiddenProduct = Product::factory()->create([
        'is_visible' => false,
    ]);
});

test('can list products', function () {
    $response = $this->getJson('/api/v1/products');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'shortDescription',
                    'isVisible',
                    'isFeatured',
                    'categories',
                    'tags',
                    'images',
                ]
            ],
            'meta' => [
                'page',
                'per_page',
                'total',
                'has_next',
            ]
        ]);

    expect($response->json('data'))->toHaveCount(3);
    expect($response->json('meta.total'))->toBe(3);
});

test('can filter products by category', function () {
    $response = $this->getJson('/api/v1/products?category=eclairage');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

test('can filter products by tags', function () {
    $response = $this->getJson('/api/v1/products?tags=LED,Premium');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

test('can search products', function () {
    $product = $this->products->first();
    $response = $this->getJson('/api/v1/products?search=' . $product->name);

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($product->id);
});

test('can paginate products', function () {
    Product::factory(10)->visible()->create();

    $response = $this->getJson('/api/v1/products?per_page=5');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5);
    expect($response->json('meta.per_page'))->toBe(5);
    expect($response->json('meta.has_next'))->toBe(true);
});

test('only shows visible products by default', function () {
    $response = $this->getJson('/api/v1/products');

    $response->assertOk();
    
    $productIds = collect($response->json('data'))->pluck('id');
    expect($productIds)->not->toContain($this->hiddenProduct->id);
});

test('can show specific product', function () {
    $product = $this->products->first();
    
    $response = $this->getJson("/api/v1/products/{$product->slug}");

    $response->assertOk()
        ->assertJsonStructure([
            'id',
            'name',
            'slug',
            'shortDescription',
            'description',
            'categories',
            'tags',
            'images',
            'specs',
            'relatedProducts',
        ]);

    expect($response->json('id'))->toBe($product->id);
});

test('returns 404 for non-existent product', function () {
    $response = $this->getJson('/api/v1/products/non-existent-slug');

    $response->assertNotFound();
});

test('respects rate limiting', function () {
    // This would need to be adjusted based on your actual rate limiting setup
    for ($i = 0; $i < 65; $i++) {
        $response = $this->getJson('/api/v1/products');
        
        if ($i < 60) {
            $response->assertOk();
        } else {
            $response->assertStatus(429); // Too Many Requests
            break;
        }
    }
})->skip('Rate limiting test - uncomment when needed');