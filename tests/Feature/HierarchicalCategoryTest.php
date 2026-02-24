<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HierarchicalCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Category tree nesting works
     * Tests unlimited nesting with parent-child relationships
     */
    public function test_category_tree_nesting_works(): void
    {
        // Arrange & Act: Create 4-level nested hierarchy
        $level1 = ProductCategory::factory()->create(['name' => 'Electronics']);
        $level2 = ProductCategory::factory()->create([
            'name' => 'Computers',
            'parent_id' => $level1->id,
        ]);
        $level3 = ProductCategory::factory()->create([
            'name' => 'Laptops',
            'parent_id' => $level2->id,
        ]);
        $level4 = ProductCategory::factory()->create([
            'name' => 'Gaming Laptops',
            'parent_id' => $level3->id,
        ]);

        // Assert: Relationships are correct
        $this->assertEquals($level1->id, $level2->parent_id);
        $this->assertEquals($level2->id, $level3->parent_id);
        $this->assertEquals($level3->id, $level4->parent_id);

        // Assert: Parent relationships work
        $this->assertEquals('Electronics', $level2->parent->name);
        $this->assertEquals('Computers', $level3->parent->name);
        $this->assertEquals('Laptops', $level4->parent->name);

        // Assert: Children relationships work
        $this->assertTrue($level1->children->contains($level2));
        $this->assertTrue($level2->children->contains($level3));
        $this->assertTrue($level3->children->contains($level4));

        // Assert: Depth calculation
        $this->assertEquals(0, $level1->getDepth());
        $this->assertEquals(1, $level2->getDepth());
        $this->assertEquals(2, $level3->getDepth());
        $this->assertEquals(3, $level4->getDepth());

        // Assert: Full path
        $this->assertEquals('Electronics', $level1->getFullPath());
        $this->assertEquals('Electronics > Computers', $level2->getFullPath());
        $this->assertEquals('Electronics > Computers > Laptops', $level3->getFullPath());
        $this->assertEquals('Electronics > Computers > Laptops > Gaming Laptops', $level4->getFullPath());
    }

    /**
     * Test 2: Circular reference prevention
     * Tests that categories cannot become their own ancestors
     */
    public function test_circular_reference_prevention(): void
    {
        // Arrange
        $parent = ProductCategory::factory()->create(['name' => 'Parent']);
        $child = ProductCategory::factory()->create([
            'name' => 'Child',
            'parent_id' => $parent->id,
        ]);
        $grandchild = ProductCategory::factory()->create([
            'name' => 'Grandchild',
            'parent_id' => $child->id,
        ]);

        // Act & Assert: Cannot set self as parent
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('A category cannot be its own parent');

        $parent->parent_id = $parent->id;
        $parent->save();
    }

    /**
     * Test 2b: Circular reference prevention for descendants
     * Tests that a category cannot have a descendant as parent
     */
    public function test_circular_reference_descendant_prevention(): void
    {
        // Arrange
        $parent = ProductCategory::factory()->create(['name' => 'Parent']);
        $child = ProductCategory::factory()->create([
            'name' => 'Child',
            'parent_id' => $parent->id,
        ]);
        $grandchild = ProductCategory::factory()->create([
            'name' => 'Grandchild',
            'parent_id' => $child->id,
        ]);

        // Act & Assert: Cannot set descendant as parent
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot create circular reference');

        $parent->parent_id = $grandchild->id; // Trying to create loop
        $parent->save();
    }

    /**
     * Test 3: Empty category filtering removes categories without products
     * Tests recursive filtering of empty categories
     */
    public function test_empty_category_filtering(): void
    {
        // Arrange: Create category hierarchy
        $electronics = ProductCategory::factory()->create(['name' => 'Electronics']);
        $computers = ProductCategory::factory()->create([
            'name' => 'Computers',
            'parent_id' => $electronics->id,
        ]);
        $laptops = ProductCategory::factory()->create([
            'name' => 'Laptops',
            'parent_id' => $computers->id,
        ]);
        $emptyCategory = ProductCategory::factory()->create([
            'name' => 'Empty Subcategory',
            'parent_id' => $computers->id,
        ]);

        // Add products to laptops category only
        $product = Product::factory()->create(['status' => 'active']);
        $product->categories()->attach($laptops->id, [
            'is_primary' => true,
            'display_order' => 1,
        ]);

        // Act: Filter empty categories
        $categories = ProductCategory::with('childrenRecursive')->topLevel()->get();
        $filtered = ProductCategory::filterEmptyCategories($categories);

        // Assert: Electronics and Computers kept (have descendant with products)
        $this->assertTrue($filtered->contains('id', $electronics->id));

        // Assert: Laptops kept (has products)
        $electronicsFiltered = $filtered->firstWhere('id', $electronics->id);
        $this->assertNotNull($electronicsFiltered->childrenRecursive);
        $this->assertTrue($electronicsFiltered->childrenRecursive->contains('id', $computers->id));

        $computersFiltered = $electronicsFiltered->childrenRecursive->firstWhere('id', $computers->id);
        $this->assertTrue($computersFiltered->childrenRecursive->contains('id', $laptops->id));

        // Assert: Empty category filtered out
        $this->assertFalse($computersFiltered->childrenRecursive->contains('id', $emptyCategory->id));
    }

    /**
     * Test 4: Recursive count includes descendants
     * Tests accurate product counting across category tree
     */
    public function test_recursive_count_includes_descendants(): void
    {
        // Arrange: Create nested categories
        $wellness = ProductCategory::factory()->create(['name' => 'Wellness']);
        $supplements = ProductCategory::factory()->create([
            'name' => 'Supplements',
            'parent_id' => $wellness->id,
        ]);
        $vitamins = ProductCategory::factory()->create([
            'name' => 'Vitamins',
            'parent_id' => $supplements->id,
        ]);

        // Add products at different levels
        $product1 = Product::factory()->create(['status' => 'active']);
        $product1->categories()->attach($wellness->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create(['status' => 'active']);
        $product2->categories()->attach($supplements->id, ['is_primary' => true, 'display_order' => 1]);

        $product3 = Product::factory()->create(['status' => 'active']);
        $product3->categories()->attach($vitamins->id, ['is_primary' => true, 'display_order' => 1]);

        $product4 = Product::factory()->create(['status' => 'active']);
        $product4->categories()->attach($vitamins->id, ['is_primary' => false, 'display_order' => 2]);

        // Add inactive product (should not be counted)
        $inactiveProduct = Product::factory()->create(['status' => 'inactive']);
        $inactiveProduct->categories()->attach($vitamins->id, ['is_primary' => true, 'display_order' => 1]);

        // Act & Assert: Recursive counts
        $this->assertEquals(4, $wellness->active_product_count); // All descendants
        $this->assertEquals(3, $supplements->active_product_count); // Supplements + Vitamins
        $this->assertEquals(2, $vitamins->active_product_count); // Only vitamins level

        // Inactive products not counted
        $this->assertEquals(4, $wellness->active_product_count); // Still 4, not 5
    }

    /**
     * Test 5: Cascade delete of children
     * Tests automatic deletion of children when parent is deleted
     */
    public function test_cascade_delete_of_children(): void
    {
        // Arrange
        $parent = ProductCategory::factory()->create(['name' => 'Parent']);
        $child1 = ProductCategory::factory()->create([
            'name' => 'Child 1',
            'parent_id' => $parent->id,
        ]);
        $child2 = ProductCategory::factory()->create([
            'name' => 'Child 2',
            'parent_id' => $parent->id,
        ]);
        $grandchild = ProductCategory::factory()->create([
            'name' => 'Grandchild',
            'parent_id' => $child1->id,
        ]);

        // Act: Delete parent
        $parent->delete();

        // Assert: All descendants cascade deleted
        $this->assertDatabaseMissing('product_categories', ['id' => $parent->id]);
        $this->assertDatabaseMissing('product_categories', ['id' => $child1->id]);
        $this->assertDatabaseMissing('product_categories', ['id' => $child2->id]);
        $this->assertDatabaseMissing('product_categories', ['id' => $grandchild->id]);
    }

    /**
     * Test 6: Category dropdown excludes self and descendants
     * Tests admin form validation logic
     */
    public function test_category_dropdown_excludes_self(): void
    {
        // Arrange
        $category1 = ProductCategory::factory()->create(['name' => 'Category 1']);
        $category2 = ProductCategory::factory()->create(['name' => 'Category 2']);
        $child = ProductCategory::factory()->create([
            'name' => 'Child of 1',
            'parent_id' => $category1->id,
        ]);
        $grandchild = ProductCategory::factory()->create([
            'name' => 'Grandchild of 1',
            'parent_id' => $child->id,
        ]);

        // Act: Check isDescendantOf utility
        $this->assertTrue($child->isDescendantOf($category1));
        $this->assertTrue($grandchild->isDescendantOf($category1));
        $this->assertTrue($grandchild->isDescendantOf($child));
        $this->assertFalse($category2->isDescendantOf($category1));
        $this->assertFalse($category1->isDescendantOf($child)); // Parent is not descendant of child

        // Assert: Get all descendants for exclusion
        $descendantIds = $category1->getDescendantIds();
        $this->assertCount(2, $descendantIds);
        $this->assertTrue($descendantIds->contains($child->id));
        $this->assertTrue($descendantIds->contains($grandchild->id));
        $this->assertFalse($descendantIds->contains($category2->id));
    }

    /**
     * Test 7: Primary category designation in pivot table
     * Tests multiple category assignment with primary flag
     */
    public function test_primary_category_designation(): void
    {
        // Arrange
        $category1 = ProductCategory::factory()->create(['name' => 'Primary Category']);
        $category2 = ProductCategory::factory()->create(['name' => 'Secondary Category']);
        $category3 = ProductCategory::factory()->create(['name' => 'Tertiary Category']);

        $product = Product::factory()->create();

        // Act: Assign multiple categories with primary designation
        $product->categories()->attach([
            $category1->id => ['is_primary' => true, 'display_order' => 1],
            $category2->id => ['is_primary' => false, 'display_order' => 2],
            $category3->id => ['is_primary' => false, 'display_order' => 3],
        ]);

        // Assert: Pivot data stored correctly
        $primaryCategory = $product->categories()
            ->wherePivot('is_primary', true)
            ->first();

        $this->assertEquals($category1->id, $primaryCategory->id);
        $this->assertTrue((bool)$primaryCategory->pivot->is_primary);

        // Assert: All categories assigned
        $this->assertCount(3, $product->categories);

        // Assert: Display order respected
        $orderedCategories = $product->categories()->orderBy('display_order')->get();
        $this->assertEquals($category1->id, $orderedCategories[0]->id);
        $this->assertEquals($category2->id, $orderedCategories[1]->id);
        $this->assertEquals($category3->id, $orderedCategories[2]->id);
    }

    /**
     * Test 8: Category display order respected
     * Tests ordering of sibling categories
     */
    public function test_category_display_order_respected(): void
    {
        // Arrange: Create sibling categories with specific display orders
        $parent = ProductCategory::factory()->create(['name' => 'Parent']);

        $child3 = ProductCategory::factory()->create([
            'name' => 'Third',
            'parent_id' => $parent->id,
            'display_order' => 3,
        ]);

        $child1 = ProductCategory::factory()->create([
            'name' => 'First',
            'parent_id' => $parent->id,
            'display_order' => 1,
        ]);

        $child2 = ProductCategory::factory()->create([
            'name' => 'Second',
            'parent_id' => $parent->id,
            'display_order' => 2,
        ]);

        // Act: Get children using ordered scope
        $orderedChildren = $parent->children()->ordered()->get();

        // Assert: Children returned in display_order sequence
        $this->assertEquals('First', $orderedChildren[0]->name);
        $this->assertEquals('Second', $orderedChildren[1]->name);
        $this->assertEquals('Third', $orderedChildren[2]->name);

        // Assert: Top-level categories also ordered
        $topLevel = ProductCategory::topLevel()->ordered()->get();
        $this->assertTrue($topLevel->count() >= 1);
        $this->assertEquals($parent->id, $topLevel->first()->id);
    }

}
