<?php

// Include helper functions
require_once __DIR__ . '/helpers.php';

// Enable CORS headers to allow requests from the front-end
header('Access-Control-Allow-Origin: http://cc.localhost');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight requests
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod === 'OPTIONS') {
    header('HTTP/1.1 204 No Content');
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Parse the path from the URL
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Fetch mock data from JSON files
$courses = json_decode(file_get_contents(__DIR__ . '/data/course_list.json'), true);
$categories = json_decode(file_get_contents(__DIR__ . '/data/categories.json'), true);

switch ($path[0]) {
        // Handle categories endpoint
    case 'categories':
        if ($requestMethod === 'GET') {
            if (isset($path[1])) {
                // Get category by ID
                $category = array_filter($categories, fn($cat) => $cat['id'] === $path[1]);
                echo json_encode(array_values($category));
            } else {
                // Get all categories and include course counts
                foreach ($categories as &$category) {
                    $category['count_of_courses'] = countCoursesInCategory(
                        $category['id'],
                        $courses
                    );
                }
                echo json_encode($categories);
            }
        }
        break;

        // Handle courses endpoint
    case 'courses':
        if ($requestMethod === 'GET') {
            // Filter by category ID if provided
            if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
                $filteredCourses = array_filter($courses, fn($course) => $course['category_id'] === $_GET['category_id']);
                foreach ($filteredCourses as &$course) {
                    $course['main_category_name'] = getCategoryNameById($course['category_id'], $categories);
                }
                echo json_encode(array_values($filteredCourses));
            } else {
                // Update course data to include main_category_name for all courses
                foreach ($courses as &$course) {
                    $course['main_category_name'] = getCategoryNameById($course['category_id'], $categories);
                }
                // Return all courses if no category filter is provided
                echo json_encode($courses);
            }
        }
        break;

        // Default response for unknown endpoints
    default:
        echo json_encode(['message' => 'Endpoint not found']);
        break;
}
