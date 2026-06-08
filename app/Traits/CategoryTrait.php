<?php

namespace App\Traits;

use App\Models\Category;

class CategoryTrait
{
    # get most root category
    public static function getRootParentCategory(Category $category)
    {
        if ($category->parent_id) {
            // If the category has a parent, recursively call the function
            return CategoryTrait::getRootParentCategory($category->parentCategory);
        }

        // If no parent, then it is the root category
        return $category;
    }

    # get immediate children collection of a category
    public static function getImmediateChildren($id, $includeDeleted = false, $toArray = false)
    {
        $children = Category::where('parent_id', $id)->orderBy('sorting_order_level', 'desc')->get();
        $children = $toArray && !is_null($children) ? $children->toArray() : array();
        return $children;
    }

    # get immediate children ids of a categories
    public static function getImmediateChildrenIds($id, $includeDeleted = false)
    {
        $children = CategoryTrait::getImmediateChildren($id, $includeDeleted, true);
        return !empty($children) ? array_column($children, 'id') : array();
    }

    # get immediate children count
    public static function getImmediateChildrenCount($id, $includeDeleted = false)
    {
        return Category::where('parent_id', $id)->count();
    }

    # all sub-children of a category
    public static function subChildren($id, $includeDeleted = false, $dataArray = array())
    {
        $children = CategoryTrait::getImmediateChildren($id, $includeDeleted, true);

        if (!empty($children)) {
            foreach ($children as $child) {
                $dataArray[] = $child;
                $dataArray   = CategoryTrait::subChildren($child['id'], $includeDeleted, $dataArray);
            }
        }
        return $dataArray;
    }

    # all sub-children ids of a category 
    public static function childrenIds($id, $includeDeleted = false)
    {
        $children = CategoryTrait::subChildren($id, $includeDeleted = false);

        return !empty($children) ? array_column($children, 'id') : array();
    }

    # update category level
    public static function upLevelOneStep($id)
    {
        if (CategoryTrait::getImmediateChildrenIds($id, true) > 0) {
            foreach (CategoryTrait::getImmediateChildrenIds($id, true) as $value) {
                $category = Category::find($value);
                $category->level -= 1;
                $category->save();
                return CategoryTrait::upLevelOneStep($value);
            }
        }
    }

    # update category level 
    public static function downLevelOneStep($id)
    {
        if (CategoryTrait::getImmediateChildrenIds($id, true) > 0) {
            foreach (CategoryTrait::getImmediateChildrenIds($id, true) as $value) {
                $category = Category::find($value);
                $category->level += 1;
                $category->save();
                return CategoryTrait::downLevelOneStep($value);
            }
        }
    }

    # update parent id of child / children
    public static function moveChildrenToParent($id)
    {
        $childrenIds = CategoryTrait::getImmediateChildrenIds($id, true);
        $category = Category::where('id', $id)->first();
        CategoryTrait::upLevelOneStep($id);
        Category::whereIn('id', $childrenIds)->update(['parent_id' => $category->parent_id]);
    }
}
