<?php

// Helper function to count courses in each category
function countCoursesInCategory($categoryId, $courses)
{
    return count(array_filter($courses, fn($course) => $course['category_id'] === $categoryId));
}

// Helper function to get category name by ID
function getCategoryNameById($categoryId, $categories)
{
    foreach ($categories as $category) {
        if ($category['id'] === $categoryId) {
            return $category['name'];
        }
    }
    return null;
}
