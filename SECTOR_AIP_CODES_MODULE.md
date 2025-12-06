# Sector AIP Codes Module

## Overview
The Sector AIP Codes module is a separate, dedicated system for managing AIP (Annual Investment Program) codes associated with sectors. This module was separated from the main Sectors module to provide better organization and maintainability.

## Features

### 1. Separate View & Controller
- **Controller**: `SectorAipCodeController.php`
- **View**: `resources/views/sector-aip-codes/index.blade.php`
- **Routes**: Prefixed with `sector-aip-codes/`

### 2. Full CRUD Operations
- **Create**: Add new AIP codes with code, description, and active status
- **Read**: View all AIP codes for a specific sector in a DataTable
- **Update**: Edit existing AIP codes
- **Toggle**: Activate/deactivate AIP codes
- **Delete**: Remove AIP codes (with confirmation)

### 3. Permission-Based Access
- **View/Manage**: Requires `manage_departments` permission
- **Delete**: Requires `delete_departments` permission

### 4. User Interface
- Clean, modern design using Tailwind CSS
- DataTable integration with search, export (Excel, CSV, PDF, Print)
- Reusable Blade components (`x-input`, `x-input-label`, `x-confirmation-modal`)
- Responsive layout
- Visual status indicators (Active/Inactive badges)

## Routes

```php
// View all AIP codes for a sector
GET /sector-aip-codes/{sector}
Route: sector-aip-codes.index

// Create new AIP code
POST /sector-aip-codes/{sector}
Route: sector-aip-codes.store

// Update AIP code
PUT /sector-aip-codes/{sector}/{aipCode}
Route: sector-aip-codes.update

// Toggle AIP code status
PUT /sector-aip-codes/{sector}/{aipCode}/toggle
Route: sector-aip-codes.toggle

// Delete AIP code
DELETE /sector-aip-codes/{sector}/{aipCode}
Route: sector-aip-codes.destroy
```

## Database Schema

### Table: `sector_aip_codes`
- `id` - Primary key
- `sector_id` - Foreign key to sectors table
- `code` - Unique AIP code (e.g., "111-A20011")
- `description` - Optional description
- `is_active` - Boolean status (active/inactive)
- `created_at` - Timestamp
- `updated_at` - Timestamp

## Usage

### Accessing from Sectors Page
1. Navigate to Sectors page (`/sectors`)
2. Click "Manage" link in the AIP Codes column
3. You'll be redirected to the dedicated AIP codes page for that sector

### Adding AIP Code
1. Click "+ Add AIP Code" button
2. Enter code (required) - e.g., "111-A20011"
3. Enter description (optional)
4. Check "Active" checkbox if the code should be active
5. Click "Add"

### Editing AIP Code
1. Click the edit icon (pencil) next to the AIP code
2. Modify the fields
3. Click "Update"

### Toggling Status
1. Click the toggle icon (arrows) next to the AIP code
2. Confirm the action
3. Status will be updated

### Deleting AIP Code
1. Click the delete icon (trash) next to the AIP code
2. Confirm deletion in the modal
3. AIP code will be permanently removed

## Integration with Sectors

The Sectors table displays:
- Count of AIP codes per sector
- "Manage" link to access the AIP codes module
- Color-coded badges (purple for codes present, gray for none)

## Components Used

### Blade Components
- `<x-app-layout>` - Main layout wrapper
- `<x-dashboard-header>` - Page header with title and subtitle
- `<x-input>` - Styled input fields
- `<x-input-label>` - Input labels
- `<x-input-error>` - Validation error messages
- `<x-confirmation-modal>` - Reusable confirmation dialog

### JavaScript Libraries
- jQuery - DOM manipulation
- DataTables - Table functionality with search, sort, pagination, export

## Permissions Required

### To View/Manage AIP Codes
User must have `manage_departments` permission

### To Delete AIP Codes
User must have `delete_departments` permission

## File Structure

```
app/
├── Http/
│   └── Controllers/
│       └── SectorAipCodeController.php
└── Models/
    └── SectorAipCode.php

resources/
└── views/
    └── sector-aip-codes/
        └── index.blade.php

routes/
└── web.php (contains sector-aip-codes routes)

database/
└── migrations/
    └── 2025_12_06_022750_create_sector_aip_codes_table.php
```

## Benefits of Separation

1. **Cleaner Code**: Sectors view is no longer cluttered with AIP modal code
2. **Better UX**: Dedicated page provides more space and better organization
3. **Maintainability**: Easier to update and debug AIP code functionality
4. **Scalability**: Can add more features to AIP codes without affecting Sectors
5. **Reusability**: Uses standard Blade components for consistency

## Future Enhancements

Potential improvements:
- Bulk import of AIP codes from CSV/Excel
- AIP code history/audit trail
- AIP code categories or grouping
- Search and filter by status
- Export AIP codes report per sector
- Validation rules for AIP code format
