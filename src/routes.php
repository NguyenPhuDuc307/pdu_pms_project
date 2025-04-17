$router->post('/admin/search_rooms', 'AdminController@searchRooms');

// Routes for teacher - Room Management
$router->get('/teacher/search_rooms', 'TeacherController@searchRooms');
$router->post('/teacher/search_rooms', 'TeacherController@searchRooms');
$router->get('/teacher/room_detail/{id}', 'TeacherController@roomDetail');
$router->get('/teacher/suggest_rooms', 'TeacherController@suggestAvailableRooms');
$router->post('/teacher/suggest_rooms', 'TeacherController@suggestAvailableRooms');

// Routes for student - Room Management
$router->get('/student/search_rooms', 'StudentController@searchRooms');
$router->post('/student/search_rooms', 'StudentController@searchRooms');
$router->get('/student/room_detail/{id}', 'StudentController@roomDetail');