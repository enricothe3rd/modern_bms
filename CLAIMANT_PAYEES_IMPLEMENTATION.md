# Claimant Payees & Payee Categories Implementation

## Overview
Complete CRUD system for managing Claimant Payees with categorization support.

## Database Structure

### Payee Categories Table
- `id` - Primary key
- `name` - Unique category name
- `description` - Optional description
- `timestamps`

### Claimant Payees Table
- `id` - Primary key
- `name` - Payee name
- `address` - Payee address
- `department_id` - Foreign key to departments table (cascade on delete)
- `payee_category_id` - Foreign key to payee_categories table (restrict on delete)
- `timestamps`

## Features

### Payee Categories
- **CRUD Operations**: Create, Read, Update, Delete
- **Validation**: Unique name, max 255 characters
- **Protection**: Cannot delete categories with assigned payees
- **Relationships**: Tracks count of claimant payees per category
- **DataTables**: Sortable, searchable table with pagination

### Claimant Payees
- **CRUD Operations**: Create, Read, Update, Delete
- **Fields**:
  - Name (required, max 255 characters)
  - Address (required, max 1000 characters)
  - Department (required, dropdown selection)
  - Category (required, dropdown selection)
- **Relationships**: 
  - Belongs to Department
  - Belongs to Payee Category
- **DataTables**: Sortable, searchable table with pagination

## Files Created

### Migrations
- `database/migrations/2025_12_06_031106_create_payee_categories_table.php`
- `database/migrations/2025_12_06_031114_create_claimant_payees_table.php`

### Models
- `app/Models/PayeeCategory.php`
- `app/Models/ClaimantPayee.php`

### Repositories
- `app/Repositories/PayeeCategoryRepository.php`
- `app/Repositories/ClaimantPayeeRepository.php`
- `app/Repositories/DepartmentRepository.php`

### Services
- `app/Services/PayeeCategoryService.php`
- `app/Services/ClaimantPayeeService.php`

### Factories
- `database/factories/PayeeCategoryFactory.php`
- `database/factories/ClaimantPayeeFactory.php`

### Request Classes
- `app/Http/Requests/PayeeCategoryRequest.php`
- `app/Http/Requests/ClaimantPayeeRequest.php`

### Controllers
- `app/Http/Controllers/PayeeCategoryController.php`
- `app/Http/Controllers/ClaimantPayeeController.php`

### Views
- `resources/views/payee-categories/index.blade.php`
- `resources/views/claimant-payees/index.blade.php`

## Routes

### Payee Categories
```php
GET    /payee-categories           - List all categories
POST   /payee-categories           - Create new category
GET    /payee-categories/{id}/edit - Edit category
PUT    /payee-categories/{id}      - Update category
DELETE /payee-categories/{id}      - Delete category
```

### Claimant Payees
```php
GET    /claimant-payees           - List all payees
POST   /claimant-payees           - Create new payee
GET    /claimant-payees/{id}/edit - Edit payee
PUT    /claimant-payees/{id}      - Update payee
DELETE /claimant-payees/{id}      - Delete payee
```

## Permissions Required
- `manage_payees` - For viewing and managing payees/categories
- `delete_payees` - For deleting payees/categories

## UI Features
- Modal-based forms for add/edit operations
- Confirmation dialogs for delete operations
- Success/error message notifications
- DataTables integration for sorting and searching
- Responsive design with Tailwind CSS
- Reusable Blade components (x-input, x-input-label, x-confirmation-modal)

## Validation Rules

### Payee Category
- Name: required, string, max 255, unique
- Description: nullable, string, max 1000

### Claimant Payee
- Name: required, string, max 255
- Address: required, string, max 1000
- Department ID: required, exists in departments table
- Payee Category ID: required, exists in payee_categories table

## Architecture Pattern

This implementation follows the **Repository-Service-Controller** pattern:

```
Controller → Service → Repository → Model
```

### Layer Responsibilities:
- **Controller**: Handles HTTP requests/responses, delegates to service
- **Service**: Contains business logic, orchestrates repositories
- **Repository**: Handles data access and queries
- **Model**: Eloquent ORM model with relationships

### Example Flow:
1. `ClaimantPayeeController` receives request
2. Calls `ClaimantPayeeService` method
3. Service uses `ClaimantPayeeRepository` for data operations
4. Repository interacts with `ClaimantPayee` model
5. Response flows back through the layers

## Business Logic
1. Categories cannot be deleted if they have assigned payees (enforced in Service layer)
2. Deleting a department will cascade delete all associated payees (database constraint)
3. All payees must have a valid category and department (validation in Request class)
4. Categories are ordered alphabetically (handled in Repository)
5. Payees are ordered alphabetically by name (handled in Repository)

## Testing
To test the implementation:
1. Run migrations: `php artisan migrate`
2. Access Payee Categories: `/payee-categories`
3. Access Claimant Payees: `/claimant-payees`
4. Create categories first, then create payees

## Next Steps
- Add to sidebar navigation (optional)
- Create comprehensive tests
- Add seeder for sample data
- Add export functionality (optional)
- Add bulk operations (optional)
