# MVC Architecture Implementation

This project has been refactored to follow proper MVC (Model-View-Controller) architecture.

## Directory Structure

```
project/
├── Model/              # Data layer - Database operations
│   ├── Database.php           # Database connection singleton
│   ├── EventModel.php         # Event data operations
│   ├── EvaluationModel.php    # Evaluation data operations
│   ├── ParticipationModel.php # Participation data operations
│   ├── UserModel.php          # User data operations
│   └── AdminModel.php         # Admin dashboard data operations
│
├── Control/            # Controller layer - Request handling
│   ├── EventController.php         # Event request handling
│   ├── EvaluationController.php    # Evaluation request handling
│   ├── ParticipationController.php # Participation request handling
│   ├── AdminController.php         # Admin request handling
│   ├── admin_dashboard.php         # Admin dashboard entry point
│   ├── save_event.php              # Routes to EventController
│   ├── manage_evaluation.php       # Routes to EvaluationController
│   ├── manage_participation.php    # Routes to ParticipationController
│   ├── admin_moderation.php        # Routes to AdminController
│   └── get_events.php              # Routes to EventController
│
└── view/               # View layer - Presentation
    ├── FrontOffice/    # Front-end views
    └── Backoffice/     # Admin views
        └── admin_dashboard_view.php
```

## Architecture Principles

### Models (Model/)
- **Responsibility**: All database operations and data logic
- **No HTML/CSS/JS**: Pure PHP classes with database methods
- **Reusable**: Can be used by any controller
- **Examples**:
  - `EventModel::create()` - Creates a new event
  - `EventModel::getById()` - Retrieves an event
  - `EvaluationModel::submit()` - Submits an evaluation

### Controllers (Control/)
- **Responsibility**: Handle HTTP requests, validate input, call models, return responses
- **Thin**: Minimal logic, delegates to models
- **No HTML**: Controllers return JSON or pass data to views
- **Examples**:
  - `EventController::handleRequest()` - Routes event requests
  - `AdminController::getDashboardData()` - Gets dashboard data

### Views (view/)
- **Responsibility**: HTML, CSS, JavaScript for presentation
- **No Business Logic**: Only displays data passed from controllers
- **Examples**:
  - `admin_dashboard_view.php` - Admin dashboard HTML

## How It Works

### Example: Creating an Event

1. **Request**: POST to `save_event.php`
2. **Route**: `save_event.php` → `EventController::handleRequest()`
3. **Controller**: Validates input, calls `EventModel::create()`
4. **Model**: Executes SQL INSERT, returns event ID
5. **Controller**: Returns JSON response
6. **Client**: Receives success/error response

### Example: Viewing Admin Dashboard

1. **Request**: GET to `admin_dashboard.php`
2. **Controller**: `AdminController::getDashboardData()`
3. **Model**: `AdminModel` fetches stats, events, evaluations
4. **Controller**: Passes data to view
5. **View**: `admin_dashboard_view.php` renders HTML

## Benefits

1. **Separation of Concerns**: Each layer has a single responsibility
2. **Maintainability**: Easy to find and modify code
3. **Testability**: Models and controllers can be tested independently
4. **Reusability**: Models can be used by multiple controllers
5. **Scalability**: Easy to add new features following the same pattern

## Migration Notes

- Old files (`save_event.php`, `manage_evaluation.php`, etc.) now route to controllers
- Database connection is centralized in `Database.php` (Singleton pattern)
- All database queries moved to model classes
- View rendering separated from business logic

## Next Steps

To complete the MVC refactoring:
1. Move remaining HTML from `admin_dashboard.php` to `admin_dashboard_view.php`
2. Create view files for other pages if needed
3. Add error handling and validation layers
4. Consider adding a routing system for cleaner URLs







